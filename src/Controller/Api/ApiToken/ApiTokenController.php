<?php


namespace App\Controller\Api\ApiToken;


use App\Controller\BaseController;
use App\Entity\User\ApiToken;
use App\Entity\User\User;
use App\Form\User\UserType;
use App\Tools\ApiProblem;
use App\Tools\ApiProblemException;
use Carbon\Carbon;
use Doctrine\Common\Annotations\AnnotationReader;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class ApiTokenController
 * @Route("/token" , defaults={"_format" : "JSON"})
 */
class ApiTokenController extends BaseController
{


    /**
     * @Route("/{accessToken}", name="api_token_show" , methods={"GET"})
     */
    public function showAction($accessToken, Request $request)
    {
        $apiToken = $this->getEntityManager()->getRepository(ApiToken::class)->findOneBy(['accessToken' => $accessToken]);
        if (!$apiToken) {
            throw $this->createNotFoundException("Token " . $accessToken . " is invalid!");
        }
        $response = $this->createApiResponse($apiToken);
        return $response;
    }

    /**
     * @Route("/refresh", name="api_token_refresh" , methods={"POST"})
     */
    public function refreshAction(Request $request)
    {
        $refreshToken = $request->headers->get('AUTH-REFRESH-TOKEN');
        $apiToken = $this->getEntityManager()->getRepository(ApiToken::class)->findOneBy(['refreshToken' => $refreshToken]);
        $token = $this->createToken($apiToken->getCreatedBy());
        $response = $this->createApiResponse($token, 201);
        return $response;
    }

}