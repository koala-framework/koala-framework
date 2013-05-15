<?php
class Kwc_FormCards_FormRadio_Component extends Kwc_Form_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = 'Kontaktformular';
        return $ret;
    }
}
