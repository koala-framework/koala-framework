<?php
class Kwf_Assets_Components_Dependency_Css extends Kwf_Assets_Components_Dependency_Abstract
{
    public function getMimeType()
    {
        return 'text/css';
    }

    public function __toString()
    {
        return $this->_componentClass.'-css';
    }
}
