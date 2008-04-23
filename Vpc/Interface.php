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
    public function getTemplateVars();
    public function getSearchVars();
    public function getComponentById($id);
    public function getComponentByClass($class);
    // Fe
    public function getComponentInfo();
}
