<?php
class Kwc_Cards_TestComponent extends Kwc_Abstract_Cards_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['ownModel'] = 'Kwc_Cards_TestModel';
        $ret['generators']['child']['component'] = array(
            'enlarge' => 'Kwc_Cards_Sub1_Component',
            'none' => 'Kwc_Cards_Sub2_Component',
        );
        return $ret;
    }
}
