<?php
namespace KwfBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\PreAuthenticatedToken;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Http\Authentication\SimplePreAuthenticatorInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationFailureHandlerInterface;


class KwfUserAuthenticator implements SimplePreAuthenticatorInterface, AuthenticationFailureHandlerInterface
{
    protected $userModel;

    public function __construct(\Kwf_User_Model $userModel)
    {
        $this->userModel = $userModel;
    }

    public function createToken(Request $request, $providerKey)
    {
        $userRow = $this->userModel->getAuthedUser();
        if (!$userRow) return null;

        return new PreAuthenticatedToken(
            'anon.',
            $userRow->id,
            $providerKey
        );
    }

    public function authenticateToken(TokenInterface $token, UserProviderInterface $userProvider, $providerKey)
    {
        if (!$userProvider instanceof KwfUserProvider) {
            throw new \InvalidArgumentException(
                sprintf(
                    'The user provider must be an instance of KwfUserProvider (%s was given).',
                    get_class($userProvider)
                )
            );
        }
        $userId = $token->getCredentials();
        $user = $userProvider->loadUserByUsername($userId);
        return new PreAuthenticatedToken(
            $user,
            $token->getCredentials(),
            $providerKey,
            $user->getRoles()
        );
    }

    public function supportsToken(TokenInterface $token, $providerKey)
    {
        return $token instanceof PreAuthenticatedToken && $token->getProviderKey() === $providerKey;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        throw new \Exception();
        return new Response(
            // this contains information about *why* authentication failed
            // use it, or return your own message
            strtr($exception->getMessageKey(), $exception->getMessageData()),
            403
        );
    }
}
