<?php
class Kwc_Statistics_Opt_FrontendForm extends Kwc_Abstract_FrontendForm
{
    private $_isOptIn;

    public function __construct($name, $componentClass, $isOptIn)
    {
        $this->_isOptIn = $isOptIn;
        $ret = parent::__construct($name, $componentClass);
        return $ret;
    }

    protected function _init()
    {
        $this->setModel(new Kwf_Model_FnF(
            array('data' => array(array('id' => 1, 'opt' => $this->_isOptIn)))
        ));
        $this->setId(1);
        if ($this->_isOptIn) {
            $label = trlKwfStatic('Cookies are set when visiting this webpage. Click to deactivate cookies.');
        } else {
            $label = trlKwfStatic('No cookies are set when visiting this webpage. Click to activate cookies.');
        }
        $this->add(new Kwf_Form_Field_Checkbox('opt', ''))
            ->setBoxLabel($label);
        parent::_init();
    }
}
