<?php
class Vpc_Newsletter_Detail_Mail_Component extends Vpc_Mail_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['content'] = array(
            'class' => 'Vps_Component_Generator_Static',
            'component' => 'Vpc_Newsletter_Detail_Mail_Paragraphs_Component'
        );
        return $ret;
    }
}
