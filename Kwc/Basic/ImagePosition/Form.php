<?php
class Vpc_Basic_ImagePosition_Form extends Vpc_Abstract_Composite_Form 
{
    protected $_createFieldsets = false;
    
    public function __construct($name, $class)
    {
        parent::__construct($name, $class);
        $this->add(new Vps_Form_Field_Select('image_position', trlVps('Position of Image')))
            ->setValues(array('left' => trlVps('Left'), 'right' => trlVps('Right'), 'center' => trlVps('Center')));
    }
}
