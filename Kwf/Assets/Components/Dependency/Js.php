<?php
class Kwf_Assets_Components_Dependency_Js extends Kwf_Assets_Components_Dependency_Abstract
{
    public function getMimeType()
    {
        return 'text/javascript';
    }

    public function isCommonJsEntry()
    {
        return true;
    }

    public function __toString()
    {
        $ret = $this->_componentClass.'-js';
        if ($this->getDeferLoad()) {
            $ret .= ' defer';
        }
        return $ret;
    }
}
