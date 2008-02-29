<?php
class Vps_Filter extends Zend_Filter
{
    public static function get($value, $classBaseName, array $args = array(), $namespaces = array())
    {
        $namespaces = array_merge(array('Vps_Filter'), (array) $namespaces);
        return parent::get($value, $classBaseName, $args, $namespaces);
    }
}
