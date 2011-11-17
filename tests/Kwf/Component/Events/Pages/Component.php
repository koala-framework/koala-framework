<?php
class Kwf_Component_Events_Pages_Component extends Kwf_Component_NoCategoriesRoot
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['page']['model'] = 'Kwf_Component_Events_Pages_Model';
        $ret['generators']['page']['component'] = array(
            'empty' => 'Kwc_Basic_Empty_Component',
            'text' => 'Kwc_Basic_Html_Component',
        );
        unset($ret['generators']['title']);
        return $ret;
    }
}
?>