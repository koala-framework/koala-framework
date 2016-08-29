<?php
class Kwc_Basic_LinkTag_Youtube_Data extends Kwc_Basic_LinkTag_Abstract_Data
{
    public function __get($var)
    {
        if ($var == 'url' || $var == 'rel') {
            return $this->getChildComponent('_video')->$var;
        } else {
            return parent::__get($var);
        }
    }

    public function getLinkDataAttributes()
    {
        return $this->getChildComponent('_video')->getLinkDataAttributes();
    }

    public function getLinkClass()
    {
        return $this->getChildComponent('_video')->getLinkClass();
    }
}
