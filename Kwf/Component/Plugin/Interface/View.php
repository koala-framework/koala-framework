<?php
interface Kwf_Component_Plugin_Interface_View
{
    const EXECUTE_BEFORE_CACHE = 'beforeCache'; //execute before saving to view cache
    const EXECUTE_BEFORE = 'before'; //nur das template dieser komponente wurde ausgefuert, {component ...} sachen stehen noch drinnen
    const EXECUTE_AFTER = 'after';   //nachdem alles wie zB Unterkomponenten eingefuegt wurden

    public function processOutput($output);
    public static function getExecutionPoint();
}
