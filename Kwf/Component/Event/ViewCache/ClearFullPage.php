<?php
class Kwf_Component_Event_ViewCache_ClearFullPage extends Kwf_Events_Event_Abstract
{
    public $urls;

    public function __construct($componentClass, array $urls)
    {
        parent::__construct($componentClass);
        $this->urls = $urls;
    }
}
