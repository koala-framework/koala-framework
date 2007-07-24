<?php
interface Vpc_Formular_Field_Interface {
    
    public function processInput();
    public function validateField($mandatory);
	public function getName();
	public function setName($name);
	public function setErrorField($fieldname);

}
