<?php
class Kwc_List_Switch_Preview_Component extends Kwc_Basic_Image_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['child']['component']['large'] =
            'Kwc_List_Switch_Preview_Large_Component';
        $ret['dimensions'] = array(
            'default'=>array(
                'width' => 100,
                'height' => 75,
                'scale' => Kwf_Media_Image::SCALE_CROP
            )
        );
        return $ret;
    }

    protected function _getChildContentWidth(Kwf_Component_Data $child)
    {
        if ($child->id == 'large') {
            return Kwc_Abstract_Composite_Component::getContentWidth();
        }
        return parent::_getChildContentWidth($child);
    }
}
