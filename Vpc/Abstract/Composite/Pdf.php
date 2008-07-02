<?php
class Vpc_Abstract_Composite_Pdf extends Vpc_Abstract_Pdf
{
    public function writeContent()
    {
        foreach ($this->_component->getData()->getChildComponents(array('treecache' => 'Vpc_Abstract_Composite_TreeCache')) as $component) {
            $component->getComponent()->getPdfWriter($this->_pdf)->writeContent();
        }
    }

}
