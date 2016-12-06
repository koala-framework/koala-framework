<?php
namespace KwfBundle\Security;

use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\User\User;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;

class KwfUserProvider implements UserProviderInterface
{
    protected $userModel;

    public function __construct(\Kwf_User_Model $userModel)
    {
        $this->userModel = $userModel;
    }

    public function loadUserByUsername($userId)
    {
        $userRow = $this->userModel->getRow($userId);
        if (!$userRow) {
            throw new UsernameNotFoundException($userId);
        }
        return new KwfUser($userRow);
    }

    public function refreshUser(UserInterface $user)
    {
        throw new UnsupportedUserException();
    }

    public function supportsClass($class)
    {
        throw new \Kwf_Exception();
        return 'Symfony\Component\Security\Core\User\User' === $class;
    }
}
