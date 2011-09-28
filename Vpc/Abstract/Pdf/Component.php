<?php
abstract class Vpc_Abstract_Pdf_Component extends Vpc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['contentSender'] = 'Vpc_Abstract_Pdf_ContentSender';
        return $ret;
    }

    /** @deprecated moved to ContentSender */
    protected final function _getPdfComponent() {}
}
