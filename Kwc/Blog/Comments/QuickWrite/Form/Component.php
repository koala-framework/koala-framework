<?php
class Kwc_Blog_Comments_QuickWrite_Form_Component extends Kwc_Posts_Write_Form_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['placeholder']['submitButton'] = trlKwfStatic('Post Comment');
        return $ret;
    }

    protected function _getSettingsRow()
    {
        return $this->_getPostsComponent()->getComponent()->getRow();
    }
}
