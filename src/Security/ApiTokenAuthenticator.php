<?php

namespace App\Security;

use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;

class ApiTokenAuthenticator extends AbstractAuthenticator
{

    public function __construct(
        public UserRepository $userRepository
    ){
    }

    /**
     * This method defines if this authenticator is going to be used, return true if yes, false if no
     */
    public function supports(Request $request): ?bool
    {
        return $request->headers->has('x-api-token');
    }

    public function authenticate(Request $request): Passport
    {
        $apiToken = $request->headers->get('x-api-token');

        if (null === $apiToken) {
            /** Goes to onAuthenticationFailure */
            throw new CustomUserMessageAuthenticationException('No Api token provided');
        }

        return new SelfValidatingPassport(
            new UserBadge($apiToken, function ($apiToken) {
                $user = $this->userRepository->findByApiToken($apiToken);

                if (!$user) {
                    /** Goes to onAuthenticationFailure */
                    throw new UserNotFoundException();
                }

                /** Goes to onAuthenticationSuccess */
                return $user;
            })
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        return new JsonResponse(['message' => $exception->getMessage()], Response::HTTP_UNAUTHORIZED);
    }

}
