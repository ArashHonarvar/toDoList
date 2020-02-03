<?php


namespace App\Controller\Api\User;


use App\Controller\BaseController;
use App\Entity\User\ApiToken;
use App\Entity\User\User;
use App\Form\User\UserType;
use App\Service\CustomPagination;
use App\Tools\ApiProblem;
use App\Tools\ApiProblemException;
use Carbon\Carbon;
use Doctrine\Common\Annotations\AnnotationReader;
use Hateoas\Representation\CollectionRepresentation;
use Hateoas\Representation\PaginatedRepresentation;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class UserController
 * @Route("/user" , defaults={"_format" : "JSON"})
 */
class UserController extends BaseController
{

    /**
     * @Route("/register", name="api_user_register" , methods={"POST"})
     */
    public function registerAction(Request $request)
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $this->processForm($request, $form);
        if (!$form->isValid()) {
            $this->throwApiProblemValidationException($form);
        }
        $password = $this->getPasswordEncoder()->encodePassword($user, $user->getPassword());
        $user->setPassword($password);
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
        $response = $this->createApiResponse($user, 201);
        return $response;
    }

    /**
     * @Route("/token/list", name="api_user_token_list" , methods={"GET"})
     */
    public function tokenListAction(Request $request, CustomPagination $pagination)
    {
        $username = $request->headers->get('AUTH-USERNAME');
        $tokensQuery = $this->getEntityManager()->getRepository(ApiToken::class)->findTokensByUsername($username);
        $limit = $request->query->get('limit', 5);
        $page = $request->query->get('page', 1);
        $paginatedData = $pagination->paginate($tokensQuery, "api_user_token_list", $page, $limit);
        $response = $this->createApiResponse($paginatedData, 200, true);
        return $response;
    }


    /**
     * @Route("/token/generate", name="api_user_generate_token" , methods={"POST"})
     */
    public function generateTokenAction(Request $request)
    {
        $username = $request->headers->get('AUTH-USERNAME');
        $user = $this->getEntityManager()->getRepository(User::class)->findOneBy(['username' => $username]);
        $token = $this->createToken($user);
        $response = $this->createApiResponse($token, 201);
        return $response;
    }

    /**
     * @Route("/show/{accessToken}", name="api_user_show_with_token" , methods={"GET"})
     */
    public function showWithAccessTokenAction($accessToken, Request $request)
    {
        $apiToken = $this->getEntityManager()->getRepository(ApiToken::class)->findOneBy(['accessToken' => $accessToken]);
        $user = null;
        if ($apiToken) {
            $user = $apiToken->getCreatedBy();
        }
        $response = $this->createApiResponse($user);
        return $response;
    }


}