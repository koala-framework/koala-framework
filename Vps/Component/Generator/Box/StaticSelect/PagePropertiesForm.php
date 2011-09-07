<?php
class Vps_Component_Generator_Box_StaticSelect_PagePropertiesForm extends Vps_Form
{
    private $_generator;
    public function __construct(Vps_Component_Generator_Box_StaticSelect $generator)
    {
        $this->_generator = $generator;
        parent::__construct();

        $this->setModel($generator->getModel());
        $label = $generator->getSetting('boxName');
        if (!$label) $label = trlVps('Type');
        $select = $this->add(new Vps_Form_Field_Select('component', $label));
        $select->setAllowBlank(false);
        $values = array();
        foreach ($generator->getChildComponentClasses() as $k=>$c) {
            $values[$k] = Vpc_Abstract::getSetting($c, 'componentName');
        }
        $select->setValues($values);
        $select->setDefaultValue(array_shift(array_Keys($values)));

    }

    protected function _createMissingRow($id)
    {
        $ret = parent::_createMissingRow($id);
        $ret->component = array_shift(array_keys($this->_generator->getChildComponentClasses()));
        return $ret;
    }
}
