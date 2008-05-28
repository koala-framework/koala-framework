<?php
class Vpc_Abstract_List_Pdf extends Vpc_Abstract_Pdf
{
    public function writeContent()
    {
        foreach ($this->_component->getChildComponentTreeCacheRows() as $row) {
            $component = $row->getComponent();
            $component->getPdfWriter($this->_pdf)->writeContent();
        }
    }

}
