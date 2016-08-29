<?php
class Kwf_Events_Event_CreateAssetsPackageUrls extends Kwf_Events_Event_Abstract
{
    public $prefix = '';

    public $assetsPackage;
    public $subroot = null;

    public function __construct($class, $assetsPackage, $subroot = null)
    {
        parent::__construct($class);
        $this->assetsPackage = $assetsPackage;
        $this->subroot = $subroot;
    }
}
