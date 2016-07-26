<?php
class Kwf_Component_Event_CreateMediaUrl extends Kwf_Events_Event_Abstract
{
    /**
     * @var Kwf_Component_Data
     */
    public $component;
    public $url;

    public function __construct($componentClass, Kwf_Component_Data $component, $url)
    {
        $this->class = $componentClass;
        $this->component = $component;
        $this->url = $url;
    }
}
