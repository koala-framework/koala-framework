<?php
class Kwc_Basic_LinkTag_Youtube_Data extends Kwf_Component_Data
{
    public function __get($var)
    {
        if ($var == 'url' || $var == 'rel') {
            return $this->getChildComponent('_video')->$var;
        } else {
            return parent::__get($var);
        }
    }
}
