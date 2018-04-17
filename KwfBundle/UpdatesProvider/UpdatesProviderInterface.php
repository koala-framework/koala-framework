<?php
namespace KwfBundle\UpdatesProvider;

interface UpdatesProviderInterface
{
    /**
     * @return array
     */
    public function getUpdates();
}
