<?php
class Kwc_Advanced_Imprint_Disclaimer_Form extends Kwc_Abstract_Form
{
    public function __construct($name, $class)
    {
        parent::__construct($name, $class);

        $this->setLabelWidth(200);
        $this->fields->add(new Kwf_Form_Field_TextField('disclaimer_name', trlKwf('Disclaimer name')))
            ->setWidth(300);
        $this->fields->add(new Kwf_Form_Field_Select('disclaimer_type', trlKwf('Disclaimer type')))
            ->setValues($this->_getDisclaimerText())
            ->setWidth(300);
    }

    protected function _getDisclaimerText()
    {
        $disclaimerText = array();
        $disclaimerText[''] = trlKwf('None');
        $disclaimerText['de'] = trlKwf('Germany');
        return $disclaimerText;
    }
}
