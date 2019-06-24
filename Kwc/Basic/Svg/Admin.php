<?php
class Kwc_Basic_Svg_Admin extends Kwc_Abstract_Admin
{
    public function componentToString(Kwf_Component_Data $data)
    {
        $row = $data->getComponent()->getRow();
        if ($upload = $row->getParentRow('Upload')) {
            return $upload->filename . '.' . $upload->extension;
        }
        return '';
    }
}
