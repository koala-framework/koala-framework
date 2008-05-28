<?php
class Vpc_Composite_Images_Pdf extends Vpc_Abstract_List_Pdf
{
    public function writeContent($percentage)
    {
    foreach ($this->_component->getChildComponentTreeCacheRows() as $row) {
            $component = $row->getComponent();
            $component->getPdfWriter($this->_pdf)->writeContent($percentage);
        }
    }

}
