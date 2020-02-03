<?php


namespace App\Security;


use App\Entity\User\ApiToken;
use App\Entity\User\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;

class TokenAuthenticator extends AbstractGuardAuthenticator
{

    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * Called on every request to decide if this authenticator should be
     * used for the request. Returning false will cause this authenticator
     * to be skipped.
     */
    public function supports(Request $request)
    {
        return "api_token_show_with_token" == $request->attributes->get('_route') ? false : true;
    }

    /**
     * Called on every request. Return whatever credentials you want to
     * be passed to getUser() as $credentials.
     */
    public function getCredentials(Request $request)
    {
        if($request->attributes->get('_route') == "x"){
            $data = ['refresh-token' =>  $request->headers->get('AUTH-REFRESH-TOKEN') , 'access-token' => null ];
        }else{
            $data = ['access-token' =>  $request->headers->get('AUTH-ACCESS-TOKEN') , 'refresh-token' => null ];
        }
        return $data;
    }

    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        $apiAccessToken = $credentials['access-token'];
        $apiRefreshToken = $credentials['refresh-token'];

        if (null === $apiAccessToken && null === $apiRefreshToken) {
            throw new CustomUserMessageAuthenticationException(
                'Authentication Headers are missing'
            );
        }

        if (isset($apiAccessToken)) {
            $token = $this->entityManager->getRepository(ApiToken::class)->findOneBy(['accessToken' => $apiAccessToken]);
            if ($token->isExpired()) {
            throw new CustomUserMessageAuthenticationException(
                'Token expired'
            );
        }
        } else {
            $token = $this->entityManager->getRepository(ApiToken::class)->findOneBy(['refreshToken' => $apiRefreshToken]);
        }

        if (!$token) {
            throw new CustomUserMessageAuthenticationException(
                'Invalid API Token'
            );
        }

        // if a User object, checkCredentials() is called
        return $token->getCreatedBy();
    }

    public function checkCredentials($credentials, UserInterface $user)
    {
        // check credentials - e.g. make sure the password is valid
        // no credential check is needed in this case

        // return true to cause authentication success
        return true;
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        // on success, let the request continue
        return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        $data = [
            'message' => strtr($exception->getMessageKey(), $exception->getMessageData())

            // or to translate this message
            // $this->translator->trans($exception->getMessageKey(), $exception->getMessageData())
        ];

        return new JsonResponse($data, Response::HTTP_FORBIDDEN);
    }

    /**
     * Called when authentication is needed, but it's not sent
     */
    public function start(Request $request, AuthenticationException $authException = null)
    {
        $data = [
            // you might translate this message
            'message' => 'Authentication Required'
        ];

        return new JsonResponse($data, Response::HTTP_UNAUTHORIZED);
    }

    public function supportsRememberMe()
    {
        return false;
    }

}
