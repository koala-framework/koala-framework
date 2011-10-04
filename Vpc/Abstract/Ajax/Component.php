<?php
abstract class Vpc_Abstract_Ajax_Component extends Vpc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['contentSender'] = 'Vpc_Abstract_Ajax_ContentSender';
        return $ret;
    }
}
