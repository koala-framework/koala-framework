<?php
abstract class Kwc_Abstract_Ajax_Component extends Kwc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['contentSender'] = 'Kwc_Abstract_Ajax_ContentSender';
        $ret['flags']['noIndex'] = true;
        return $ret;
    }
}
