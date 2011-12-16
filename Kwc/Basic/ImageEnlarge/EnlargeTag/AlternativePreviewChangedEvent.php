<?php
class Kwc_Basic_ImageEnlarge_EnlargeTag_AlternativePreviewChangedEvent extends Kwf_Component_Event_Abstract
{
    public $componentId;

    public function __construct($componentClass, $componentId)
    {
        $this->class = $componentClass;
        $this->componentId = $componentId;
    }
}
