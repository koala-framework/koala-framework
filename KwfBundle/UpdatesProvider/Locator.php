<?php
namespace KwfBundle\UpdatesProvider;

class Locator
{
    private $updateProviders;

    public function __construct()
    {
        $this->updateProviders = array();
    }

    public function addUpdateProvider(UpdatesProviderInterface $provider)
    {
        $this->updateProviders[] = $provider;
    }

    public function getUpdateProviders()
    {
        return $this->updateProviders;
    }
}
