<?php
class Kwc_List_Gallery_Pdf extends Kwc_List_Images_Pdf
{
    protected function _getGalleryColumns()
    {
        return $this->_component->getRow()->columns;
    }
}
