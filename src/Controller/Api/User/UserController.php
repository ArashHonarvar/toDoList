<?php


namespace App\Controller\Api\User;


use App\Controller\BaseController;
use App\Entity\User\ApiToken;
use App\Entity\User\User;
use App\Form\User\UserType;
use App\Tools\ApiProblem;
use App\Tools\ApiProblemException;
use Carbon\Carbon;
use Doctrine\Common\Annotations\AnnotationReader;
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

    private function processForm(Request $request, FormInterface $form)
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
        $clearMissing = $request->getMethod() != 'PATCH';
        $form->submit($data, $clearMissing);
    }

    private function getErrorsFromForm(FormInterface $form)
    {
        $errors = array();
        foreach ($form->getErrors() as $error) {
            $errors[] = $error->getMessage();
        }

        foreach ($form->all() as $childForm) {
            if ($childForm instanceof FormInterface) {
                if ($childErrors = $this->getErrorsFromForm($childForm)) {
                    $errors[$childForm->getName()] = $childErrors;
                }
            }
        }

        return $errors;
    }


    private function throwApiProblemValidationException(FormInterface $form)
    {
        $errors = $this->getErrorsFromForm($form);
        $apiProblem = new ApiProblem(
            400,
            ApiProblem::TYPE_VALIDATION_ERROR
        );

        $apiProblem->set("errors", $errors);
        throw new ApiProblemException($apiProblem);
    }

    private function createToken(User $user)
    {
        $apiToken = new ApiToken();
        $apiToken->setCreatedBy($user);
        $now = Carbon::instance(new \DateTime('now'));
        $now->addMonths(1);
        $expiredAt = $now->toDateTimeString();
        $apiToken->setExpiredAt(new \DateTime($expiredAt));
        $this->getEntityManager()->persist($apiToken);
        $this->getEntityManager()->flush();
        return $apiToken;
    }


}