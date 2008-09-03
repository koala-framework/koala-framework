<?php
class Vpc_Composite_Downloads_Pdf extends Vpc_Abstract_Pdf
{
    public function writeContent()
    {
        $childComponents = $this->_component->getData()
                            ->getChildComponents(array('generator' => 'child'));
        foreach ($childComponents as $component) {
            $component->getComponent()->getPdfWriter($this->_pdf)->writeContent();
        }
    }

}
