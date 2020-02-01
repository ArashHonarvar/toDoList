<?php


namespace App\Controller\Api\Task;


use App\Controller\BaseController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class TaskController
 * @Route("/api/task" , defaults={"_format" : "JSON"})
 */
class TaskController extends BaseController
{
    /**
     * @Route("/list" , methods={"GET"})
     */
    public function listAction()
    {
        $data = ["programmers" => "arash"];
        $response = $this->createApiResponse($data);
        return $response;
    }
}