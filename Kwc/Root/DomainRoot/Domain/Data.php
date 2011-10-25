<?php
class Kwc_Root_DomainRoot_Domain_Data extends Kwf_Component_Data
{
    public function __get($var)
    {
        if ($var == 'filename') {
            return null;
        } else {
            return parent::__get($var);
        }
    }
}
