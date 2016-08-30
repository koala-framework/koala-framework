<?php
class Kwc_Abstract_Pdf_Trl_Component extends Kwc_Chained_Trl_Component
{
    public static function getSettings($masterComponentClass = null)
    {
        $ret = parent::getSettings($masterComponentClass);
        $ret['contentSender'] = 'Kwc_Abstract_Pdf_Trl_ContentSender';
        return $ret;
    }
}
