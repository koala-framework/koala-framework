<?php
interface Vpc_Interface
{
    public function getId();
    public function generateHierarchy($filename = '');
    public function setPageCollection(Vps_PageCollection_Abstract $pageCollection);
    public function getTemplateVars($mode);
    public function findComponent($id, $findDecorators = false);
    // Fe
    public function getComponentInfo();
    public function saveFrontendEditing(Zend_Controller_Request_Http $request);
}