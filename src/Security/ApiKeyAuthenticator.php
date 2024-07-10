<?php

namespace App\Security;

use App\Entity\ApiKey;
use App\Repository\ApiKeyRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;
use Psr\Log\LoggerInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class ApiKeyAuthenticator extends AbstractAuthenticator implements UserProviderInterface
{
    public function __construct(
        private ApiKeyRepository $apiKeyRepository,
        private LoggerInterface $logger
    ) {
    }

    public function supports(Request $request): ?bool
    {
        return $request->headers->has('X-API-KEY');
    }

    public function authenticate(Request $request): Passport
    {
        $apiKey = $request->headers->get('X-API-KEY');
        if (null === $apiKey) {
            throw new AuthenticationException('No API key provided');
        }

        return new SelfValidatingPassport(
            new UserBadge($apiKey, function($apiKey) {
                $apiKeyEntity = $this->apiKeyRepository->findOneBy(['token' => $apiKey]);
                if (!$apiKeyEntity) {
                    throw new UserNotFoundException();
                }
                return $this->loadUserByIdentifier($apiKeyEntity->getUser1());
            })
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        $data = [
            'message' => strtr($exception->getMessageKey(), $exception->getMessageData())
        ];

        return new JsonResponse($data, Response::HTTP_UNAUTHORIZED);
    }

    public function loadUserByIdentifier(string $identifier): UserInterface
    {
        return new ApiKeyUser($identifier);
    }

    public function refreshUser(UserInterface $user): UserInterface
    {
        return $user;
    }

    public function supportsClass(string $class): bool
    {
        return ApiKeyUser::class === $class;
    }
}
