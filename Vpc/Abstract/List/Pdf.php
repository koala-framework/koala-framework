<?php
class Vpc_Abstract_List_Pdf extends Vpc_Abstract_Pdf
{
    public function writeContent()
    {
        foreach ($this->_component->getChildComponentIds() as $id) {
            $component = $this->_component->getData()->getChildComponent('-'.$id)->getComponent();
            $component->getPdfWriter($this->_pdf)->writeContent();
        }
    }

}
