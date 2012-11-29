<?php
class Kwf_Acl_Resource_Component_Structure extends Zend_Acl_Resource
{
    public function __construct($resourceId)
    {
        $this->_resourceId = (string) 'kwc_structure_' . strtolower($resourceId);
    }
}
