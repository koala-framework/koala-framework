<?php
class Kwc_Newsletter_Detail_Mail_Component extends Kwc_Mail_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['content'] = array(
            'class' => 'Kwf_Component_Generator_Static',
            'component' => 'Kwc_Newsletter_Detail_Mail_Paragraphs_Component'
        );
        $ret['recipientSources'] = array(
            'n' => 'Kwc_Newsletter_Subscribe_Model'
        );
        return $ret;
    }
}
