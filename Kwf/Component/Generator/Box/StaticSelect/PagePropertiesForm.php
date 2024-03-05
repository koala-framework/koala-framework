<?php
class Kwf_Component_Generator_Box_StaticSelect_PagePropertiesForm extends Kwf_Form
{
    private $_generator;
    public function __construct(Kwf_Component_Generator_Box_StaticSelect $generator)
    {
        $this->_generator = $generator;
        parent::__construct();

        $this->setModel($generator->getModel());
        $label = $generator->getSetting('boxName');
        if (!$label) $label = trlKwf('Type');
        $select = $this->add(new Kwf_Form_Field_Select('component', $label));
        $select->setWidth(400);
        $select->setAllowBlank(false);

        $countContentComponents = 0;
        foreach ($generator->getSetting('component') as $k=>$c) {
            if (is_instance_of($c, 'Kwc_Basic_ParentContent_Component')) {
            } else if (is_instance_of($c, 'Kwc_Basic_None_Component')) {
            } else {
                $countContentComponents++;
            }
        }
        $values = array();
        foreach ($generator->getSetting('component') as $k=>$c) {
            if (is_instance_of($c, 'Kwc_Basic_ParentContent_Component')) {
                $t = trlKwf('Inherit from parent page');
            } else if (is_instance_of($c, 'Kwc_Basic_None_Component')) {
                $t = trlKwf('No content');
            } else {
                $t = trlKwf('Own settings for this page');
                if ($countContentComponents > 1) {
                    $t .= ': '.Kwf_Trl::getInstance()->trlStaticExecute(Kwc_Abstract::getSetting($c, 'componentName'));
                }
            }
            $values[$k] = $t;
        }
        $select->setValues($values);
        $valueKeys = array_Keys($values);
        $select->setDefaultValue(array_shift($valueKeys));
    }

    protected function _createMissingRow($id)
    {
        $ret = parent::_createMissingRow($id);
        $keys = array_keys($this->_generator->getSetting('component'));
        $ret->component = array_shift($keys);
        return $ret;
    }
}
