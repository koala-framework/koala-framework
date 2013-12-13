<?php
class Kwc_Abstract_List_Columns_Pdf extends Kwc_List_Images_Pdf
{
    protected function _getGalleryColumns()
    {
        return $this->_component->getRow()->columns;
    }
}
