<?php
interface E3_Component_Interface
{
    public function getTemplateVars();
    public function getComponentInfo();
    public function getId();
    public function generateHierarchy(E3_PageCollection_Abstract $pageCollection, $filename='');
}