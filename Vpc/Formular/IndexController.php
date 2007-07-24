<?php
class Vpc_Formular_IndexController extends Vpc_Paragraphs_IndexController
{
    protected function _getTable()
    {
        return Zend_Registry::get('dao')->getTable('Vpc_Formular_IndexModel');
    }
}