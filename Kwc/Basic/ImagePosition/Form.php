<?php
class Kwc_Basic_ImagePosition_Form extends Kwc_Abstract_Composite_Form 
{
    protected $_createFieldsets = false;
    
    public function __construct($name, $class)
    {
        parent::__construct($name, $class);
        $this->add(new Kwf_Form_Field_Select('image_position', trlKwf('Position of Image')))
            ->setValues(array('left' => trlKwf('Left'), 'right' => trlKwf('Right'), 'center' => trlKwf('Center')));
    }
}
