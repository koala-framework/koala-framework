<?php
abstract class Vpc_Formular_Field_Abstract extends Vpc_Abstract implements Vpc_Formular_Field_Interface
{
    public function processInput() {}
    public function validateField($mandatory) {}
    public function getValue() {
        //scheißlösung
        return $this->_row->value;
    }
    public function getName() {
        //scheißlösung
        return $this->_row->name;
    }
}