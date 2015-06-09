<?php
class Kwf_Filter extends Zend_Filter
{
    public static function filterStatic($value, $classBaseName, array $args = array(), $namespaces = array())
    {
        $namespaces = array_merge(array('Kwf_Filter'), (array) $namespaces);
        class_exists('Kwf_Filter_' . ucfirst($classBaseName)); //trigger autoloader
        return parent::filterStatic($value, $classBaseName, $args, $namespaces);
    }
}
