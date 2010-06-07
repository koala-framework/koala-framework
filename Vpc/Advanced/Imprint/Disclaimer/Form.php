<?php
class Vpc_Advanced_Imprint_Disclaimer_Form extends Vpc_Abstract_Form
{
    public function __construct($name, $class)
    {
        parent::__construct($name, $class);

        $this->setLabelWidth(200);
        $this->fields->add(new Vps_Form_Field_TextField('disclaimer_name', trlVps('Disclaimer name')))
            ->setWidth(300);
        $this->fields->add(new Vps_Form_Field_Select('disclaimer_type', trlVps('Disclaimer type')))
            ->setValues($this->_getDisclaimerText())
            ->setWidth(300);
    }

    protected function _getDisclaimerText()
    {
        $disclaimerText = array();
        $disclaimerText[''] = trlVps('None');
        $disclaimerText['de'] = trlVps('Germany');
        return $disclaimerText;
    }
}
