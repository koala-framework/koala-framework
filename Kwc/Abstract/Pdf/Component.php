<?php
abstract class Kwc_Abstract_Pdf_Component extends Kwc_Abstract
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['contentSender'] = 'Kwc_Abstract_Pdf_ContentSender';
        return $ret;
    }

    /** @deprecated moved to ContentSender */
    protected final function _getPdfComponent() {}
}
