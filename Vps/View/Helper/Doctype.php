<?php
class Vps_View_Helper_Doctype extends Zend_View_Helper_Doctype
{
    public function doctype($doctype = 'XHTML1_STRICT')
    {
        $return = parent::doctype($doctype);
        if (substr($doctype, 0, 5) == 'XHTML') {
            $return = '<?xml version="1.0" encoding="utf-8"?>' . "\n" . $return . "\n";
        }
        return $return;
    }
}
