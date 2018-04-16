<?php
namespace KwfBundle\Assets;

use Symfony\Component\Asset\PathPackage;

class Package extends PathPackage
{
    private $webPath = '/assets/web/vendor/symfony/symfony/src/Symfony/Bundle/FrameworkBundle/Resources/public/';

    public function getUrl($path)
    {
        return parent::getUrl($this->webPath . str_replace('bundles/framework/', '', $path));
    }
}
