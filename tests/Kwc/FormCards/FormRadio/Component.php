<?php
class Kwc_FormCards_FormRadio_Component extends Kwc_Form_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['componentName'] = 'Kontaktformular';
        return $ret;
    }
}
