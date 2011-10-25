<?php
class Kwc_Trl_DateHelper_German extends Kwc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['date'] = array(
            'class' => 'Kwf_Component_Generator_Page_Static',
            'component' => 'Kwc_Trl_DateHelper_Date_Component',
            'name' => 'date',
        );
        $ret['generators']['dateTime'] = array(
            'class' => 'Kwf_Component_Generator_Page_Static',
            'component' => 'Kwc_Trl_DateHelper_DateTime_Component',
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
