<?php
class Kwc_Basic_LinkTag_TestLinkTag_Data extends Kwf_Component_Data
{
    public function __get($var)
    {
        if ($var == 'url') {
            return 'http://example.com';
        } else if ($var == 'rel') {
            return 'foo';
        } else {
            return parent::__get($var);
        }
    }
}
