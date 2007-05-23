<?php
interface Vps_Component_Interface
{
    public function getId();
    public function generateHierarchy($filename = '');
    // Fe
    public function getTemplateVars($mode);
    public function getComponentInfo();
    public function saveFrontendEditing(Zend_Controller_Request_Http $request);
}