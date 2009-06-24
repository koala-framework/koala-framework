<?php
class Vpc_TextImage_Text_TestComponent_Form extends Vpc_Basic_Text_Form
{
    public function __construct($name, $class)
    {
        $this->_stylesModel = new Vpc_Basic_Text_StylesModel(array(
            'proxyModel' => new Vps_Model_FnF()
        ));
        parent::__construct($name, $class);
    }
}
