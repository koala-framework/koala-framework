<?php
class Kwc_Abstract_Composite_Pdf extends Kwc_Abstract_Pdf
{
    public function writeContent()
    {
        foreach ($this->_component->getData()->getChildComponents(array('generator' => 'child')) as $component) {
            $component->getComponent()->getPdfWriter($this->_pdf)->writeContent();
        }
    }

}
