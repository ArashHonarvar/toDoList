<?php


namespace App\Controller\Api\User;


use App\Controller\BaseController;
use App\Tools\ApiProblem;
use App\Tools\ApiProblemException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class UserController
 * @Route("/api/user" , defaults={"_format" : "JSON"})
 */
class UserController extends BaseController
{

    /**
     * @Route("/register", name="api_user_register" , methods={"POST"})
     */
    public function register(Request $request)
    {
        $body = $request->getContent();
        $data = json_decode($body, true);
        if (null === $data) {
            $apiProblem = new ApiProblem(
                400,
                ApiProblem::TYPE_INVALID_REQUEST_BODY_FORMAT
            );

            throw new ApiProblemException($apiProblem);
        }

        $location = $this->generateUrl('api_user_register', [
            'nickname' => "arash"
        ]);
        $response = $this->createApiResponse(["test" => "test"], 201);
        $response->headers->set('location', $location);
        return $response;
    }

}