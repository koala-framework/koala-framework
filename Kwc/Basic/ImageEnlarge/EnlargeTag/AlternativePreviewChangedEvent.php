<?php
class Kwc_Basic_ImageEnlarge_EnlargeTag_AlternativePreviewChangedEvent extends Kwf_Component_Event_Abstract
{
    public $component;

    public function __construct($componentClass, Kwf_Component_Data $component)
    {
        $this->class = $componentClass;
        $this->component = $component;
    }
}
