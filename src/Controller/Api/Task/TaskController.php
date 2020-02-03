<?php


namespace App\Controller\Api\Task;


use App\Controller\BaseController;
use App\Entity\Task\Task;
use App\Entity\Task\TaskLog;
use App\Entity\User\ApiToken;
use App\Entity\User\User;
use App\Form\Task\TaskType;
use App\Form\User\UserType;
use App\Service\CustomPagination;
use Doctrine\ORM\EntityNotFoundException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class TaskController
 * @Route("/api/task" , defaults={"_format" : "JSON"})
 */
class TaskController extends BaseController
{
    private $default_items_per_page = 5;

    /**
     * @Route("/create" , name="api_task_create" , methods={"POST"})
     */
    public function createAction(Request $request)
    {
        $task = new Task();
        $form = $this->createForm(TaskType::class, $task);
        $this->processForm($request, $form);
        if (!$form->isValid()) {
            $this->throwApiProblemValidationException($form);
        }
        $accessToken = $request->headers->get('AUTH-ACCESS-TOKEN');
        $user = $this->getUserByAccessToken($accessToken);
        $task->setCreatedBy($user);
        $this->getEntityManager()->persist($task);
        $this->getEntityManager()->flush();
        $this->createTaskLog($task, $user, "Task has been created");
        $response = $this->createApiResponse($task, 201);
        return $response;
    }

    /**
     * @Route("/{taskId}" , name="app_api_task_update" , methods={"PUT"})
     */
    public function updateAction($taskId, Request $request)
    {
        $task = $this->getEntityManager()->getRepository(Task::class)->findNotDeleted($taskId);
        $oldStatus = $task->getStatus();
        if (!$task) {
            throw $this->createNotFoundException("Task with id " . $taskId . " not found!");
        }

        $form = $this->createForm(TaskType::class, $task);
        $this->processForm($request, $form);
        if (!$form->isValid()) {
            $this->throwApiProblemValidationException($form);
        }
        $this->getEntityManager()->persist($task);
        $this->getEntityManager()->flush();
        if ($oldStatus != $task->getStatus()) {
            $this->createTaskLog($task, $task->getCreatedBy(), "Status was changed to " . $task->getStatus());
        }
        $response = $this->createApiResponse($task);
        return $response;
    }

    /**
     * @Route("/{taskId}/change/status/{status}" , name="api_task_change_status" , methods={"PUT"})
     */
    public function changeStatusAction($taskId, $status, Request $request)
    {
        if (!in_array($status, [Task::STATUS_READY, Task::STATUS_DOING, Task::STATUS_DONE, Task::STATUS_EXPIRED])) {
            throw $this->createNotFoundException("Status " . $status . " is not valid!");
        }
        $task = $this->getEntityManager()->getRepository(Task::class)->findNotDeleted($taskId);
        if (!$task) {
            throw $this->createNotFoundException("Task with id " . $taskId . " not found!");
        }
        $task->setStatus($status);
        $this->createTaskLog($task, $task->getCreatedBy(), "Status was changed to " . $status);
        $this->getEntityManager()->flush();
        $response = $this->createApiResponse($task, 201);
        return $response;
    }

    /**
     * @Route("/{taskId}/logs" , name="api_task_logs_list" , methods={"GET"})
     */
    public function logsListAction($taskId, Request $request, CustomPagination $pagination)
    {
        $logsQuery = $this->getEntityManager()->getRepository(TaskLog::class)->findLogsByTaskId($taskId, true);
        $limit = $request->query->get('limit', $this->default_items_per_page);
        $page = $request->query->get('page', 1);
        $paginatedData = $pagination->paginate($logsQuery, "api_task_logs_list", $request->query->all(), $page, $limit);
        $response = $this->createApiResponse($paginatedData);
        return $response;
    }

    /**
     * @Route("/list" , name="api_task_list" , methods={"GET"})
     */
    public function listAction(Request $request, CustomPagination $pagination)
    {
        //Filter by status
        $filter = $request->query->get('status');
        //
        $accessToken = $request->headers->get('AUTH-ACCESS-TOKEN');
        $user = $this->getUserByAccessToken($accessToken);
        $tasksQuery = $this->getEntityManager()->getRepository(Task::class)->findAllNotDeletedByUser($user, $filter, true);
        $limit = $request->query->get('limit', $this->default_items_per_page);
        $page = $request->query->get('page', 1);
        $paginatedData = $pagination->paginate($tasksQuery, "api_task_list", $request->query->all(), $page, $limit);
        $response = $this->createApiResponse($paginatedData);
        return $response;
    }

    /**
     * @Route("/home" , name="api_task_homepage" , methods={"GET"})
     */
    public function homepageAction(Request $request, CustomPagination $pagination)
    {
        //Filter by status
        $filter = $request->query->get('status');
        //
        $accessToken = $request->headers->get('AUTH-ACCESS-TOKEN');
        $user = $this->getUserByAccessToken($accessToken);
        $tasksQuery = $this->getEntityManager()->getRepository(Task::class)->findDoableNotDeletedByUser($user, $filter, true);
        $limit = $request->query->get('limit', $this->default_items_per_page);
        $page = $request->query->get('page', 1);
        $paginatedData = $pagination->paginate($tasksQuery, "api_task_homepage", $request->query->all(), $page, $limit);
        $response = $this->createApiResponse($paginatedData);
        return $response;
    }

    /**
     * @Route("/{taskId}" , name="api_task_show" , methods={"GET"})
     */
    public function showAction($taskId, Request $request)
    {
        $task = $this->getEntityManager()->getRepository(Task::class)->findNotDeleted($taskId);
        if (!$task) {
            throw $this->createNotFoundException("Task with id " . $taskId . " not found!");
        }
        $response = $this->createApiResponse($task, 200);
        return $response;
    }

    /**
     * @Route("/{taskId}" , name="api_task_delete" , methods={"DELETE"})
     */
    public function deleteAction($taskId)
    {
        $task = $this->getEntityManager()->getRepository(Task::class)->findNotDeleted($taskId);
        if (!$task) {
            throw $this->createNotFoundException("Task with id " . $taskId . " not found!");
        }
        $task->setIsDeleted(true);
        $this->getEntityManager()->persist($task);
        $this->getEntityManager()->flush();
        $this->createTaskLog($task, $task->getCreatedBy(), "Task was deleted");
        $response = $this->createApiResponse(null, 204);
        return $response;
    }
}