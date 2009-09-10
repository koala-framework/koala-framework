<?php
class Vpc_NewsletterCategory_Detail_Component extends Vpc_Newsletter_Detail_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['mail'] = array(
            'class' => 'Vps_Component_Generator_Static',
            'component' => 'Vpc_NewsletterCategory_Detail_Mail_Component'
        );
        return $ret;
    }
}
