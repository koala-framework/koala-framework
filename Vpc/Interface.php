<?php
/**
 * Interface für Komponenten (Prefix Vpc)
 * 
 * Decorators implementieren dieses Interface und erweitern in Folge
 * Vpc_Decorator_Abstract, Komponenten erweitern Vpc_Abstract
 * 
 * @package Vpc
 * @copyright Copyright (c) 2007, Vivid Planet Software GmbH
 */
interface Vpc_Interface
{
    public function getId();
    public function getPageId();
    public function setPageCollection(Vps_PageCollection_Abstract $pageCollection);
    public function getTemplateVars();
    public function findComponent($id);
    public function findComponentByClass($class);
    // Fe
    public function getComponentInfo();
}
