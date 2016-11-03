<?php
namespace KwfBundle\Security;

use Symfony\Component\Security\Core\Authentication\Token\AbstractToken;
class KwfUserToken extends AbstractToken
{
    /**
     * Constructor.
     *
     * @param string          $secret A secret used to make sure the token is created by the app and not by a malicious client
     * @param string          $user   The user
     * @param RoleInterface[] $roles  An array of roles
     */
    public function __construct(KwfUser $user)
    {
        parent::__construct($user->getRoles());
        $this->setUser($user);
        $this->setAuthenticated(true);
    }

    /**
     * {@inheritdoc}
     */
    public function getCredentials()
    {
        return '';
    }

    /**
     * {@inheritdoc}
     */
    public function serialize()
    {
        throw new \Kwf_Exception();
        return serialize(array($this->secret, parent::serialize()));
    }

    /**
     * {@inheritdoc}
     */
    public function unserialize($serialized)
    {
        list($this->secret, $parentStr) = unserialize($serialized);
        parent::unserialize($parentStr);
    }
}
