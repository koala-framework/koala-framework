<?php
class Kwc_News_List_View_Component extends Kwc_Directories_List_View_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['placeholder']['readMore'] = trlKwfStatic('Read more').' &raquo;';
        $ret['showPreviewImage'] = true;
        return $ret;
    }

    public function getPartialVars($partial, $nr, $info)
    {
        $ret = parent::getPartialVars($partial, $nr, $info);
        $ret['showPreviewImage'] = $this->_getSetting('showPreviewImage');
        return $ret;
    }
}
