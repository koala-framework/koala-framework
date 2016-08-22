<?php
class Kwc_Blog_Comments_QuickWrite_Form_Component extends Kwc_Posts_Write_Form_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['placeholder']['submitButton'] = trlKwfStatic('Post Comment');
        return $ret;
    }

    protected function _getSettingsRow()
    {
        return $this->_getPostsComponent()->getComponent()->getRow();
    }


    protected function _afterInsert(Kwf_Model_Row_Interface $row)
    {
        parent::_afterInsert($row);
        $dir = $this->getData()->getParentByClass('Kwc_Blog_Comments_Directory_Component');
        if ($dir) {
            $dir->getComponent()->afterAddComment($row);
        }
    }

}
