<?php
class Kwf_Acl_Resource_Component_Structure extends Zend_Acl_Resource
{
    public function __construct($componentClass)
    {
        parent::__construct('kwc_structure_' . $componentClass);
    }
}
