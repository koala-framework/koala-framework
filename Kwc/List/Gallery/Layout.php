<?php
class Kwc_List_Gallery_Layout extends Kwf_Component_Layout_Default
{
    public function getChildContentWidth(Kwf_Component_Data $data, Kwf_Component_Data $child)
    {
        $ownWidth = parent::getChildContentWidth($data, $child);
        $contentMargin = $this->_getSetting('contentMargin');
        $breakpoint = $this->_getSetting('breakpoint');
        $columns = (int)$data->getComponent()->getGalleryColumns();
        $ownWidth -= ($columns-1) * $contentMargin;

        if (!$columns) $columns = 1;
        if ($columns >=5 && $columns <= 10) {
            $originColumnWidth = (int)floor($ownWidth / $columns);
            if ($columns == 6) {
                $columns = 3;
            }
            if ($columns % 2 == 0) {
                $columns = 4;
            } else {
                $columns = 3;
            }
            $ret = (int)floor((($breakpoint - $contentMargin) - ($columns-1) * $contentMargin) / $columns);
            if ($ret < $originColumnWidth) {
                $ret = $originColumnWidth;
            }
        } else {
            $ret = (int)floor($ownWidth / $columns);
        }
        return $ret;
    }
}
