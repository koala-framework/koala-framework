<?php
class Kwf_Component_Events_PseudoPage_Component extends Kwc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['child'] = array(
            'class' => 'Kwf_Component_Generator_PseudoPage_Table',
            'component' => 'Kwc_Basic_Empty_Component'
        );
        $ret['childModel'] = 'Kwf_Component_Events_PseudoPage_Model';
        return $ret;
    }
}
?>