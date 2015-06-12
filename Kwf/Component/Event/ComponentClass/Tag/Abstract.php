<?php
class Kwf_Component_Event_ComponentClass_Tag_Abstract extends Kwf_Events_Event_Abstract
{
    /**
     * @var string
     */
    public $tag;

    public function __construct($componentClass, $tag)
    {
        $this->class = $componentClass;
        $this->tag = $tag;
    }
}
