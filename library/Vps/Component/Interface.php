<?php
interface Vps_Component_Interface
{
    public function getTemplateVars($mode);
    public function getComponentInfo();
    public function getId();
    public function generateHierarchy(Vps_PageCollection_Abstract $pageCollection, $filename = '');
    public function saveFrontendEditing(Zend_Controller_Request_Http $request);
}