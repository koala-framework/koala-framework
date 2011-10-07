<?php
class Vpc_Trl_DateHelper_German extends Vpc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['date'] = array(
            'class' => 'Vps_Component_Generator_Page_Static',
            'component' => 'Vpc_Trl_DateHelper_Date_Component',
            'name' => 'date',
        );
        $ret['generators']['dateTime'] = array(
            'class' => 'Vps_Component_Generator_Page_Static',
            'component' => 'Vpc_Trl_DateHelper_DateTime_Component',
            'name' => 'dateTime',
        );
        $ret['flags']['hasLanguage'] = true;
        return $ret;
    }

    public function getLanguage()
    {
        return $this->getData()->language;
    }
}
