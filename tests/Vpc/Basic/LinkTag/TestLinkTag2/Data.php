<?php
class Vpc_Basic_LinkTag_TestLinkTag2_Data extends Vps_Component_Data
{
    public function __get($var)
    {
        if ($var == 'url') {
            return 'http://example2.com';
        } else if ($var == 'rel') {
            return 'foo';
        } else {
            return parent::__get($var);
        }
    }
}
