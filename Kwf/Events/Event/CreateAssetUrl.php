<?php
class Kwf_Events_Event_CreateAssetUrl extends Kwf_Events_Event_Abstract
{
    public $url;
    public function __construct($class, $url)
    {
        parent::__construct($class);
        $this->url = $url;
    }
}
