<?php
class Vpc_Abstract_List_Pdf extends Vpc_Abstract_Pdf
{
    public function writeContent()
    {
        foreach ($this->_component->getData()->getChildComponents(array('generator' => 'child')) as $component) {
            $component->getComponent()->getPdfWriter($this->_pdf)->writeContent();
        }
    }

}
