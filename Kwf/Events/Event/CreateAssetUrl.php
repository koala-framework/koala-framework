<?php
class Kwf_Events_Event_CreateAssetUrl extends Kwf_Events_Event_Abstract
{
    public $url;
    public $subroot = null;

    public function __construct($class, $url, $subroot = null)
    {
        parent::__construct($class);
        $this->url = $url;
        $this->subroot = $subroot;
    }
}
