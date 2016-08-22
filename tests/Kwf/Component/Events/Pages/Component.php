<?php
class Kwf_Component_Events_Pages_Component extends Kwf_Component_NoCategoriesRoot
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['generators']['page']['model'] = 'Kwf_Component_Events_Pages_Model';
        $ret['generators']['page']['component'] = array(
            'empty' => 'Kwc_Basic_None_Component',
            'text' => 'Kwc_Basic_Html_Component',
        );
        unset($ret['generators']['title']);
        return $ret;
    }
}
?>