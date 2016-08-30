<?php
class Kwc_FormCards_Form_Component extends Kwc_Form_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['componentName'] = 'Kontaktformular';
        return $ret;
    }
}
