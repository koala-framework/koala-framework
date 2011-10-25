<?php
class Kwc_TextImage_Text_TestComponent_Form extends Kwc_Basic_Text_Form
{
    public function __construct($name, $class)
    {
        $this->_stylesModel = new Kwc_Basic_Text_StylesModel(array(
            'proxyModel' => new Kwf_Model_FnF()
        ));
        parent::__construct($name, $class);
    }
}
