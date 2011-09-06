<?php
class Vps_Component_Generator_Box_StaticSelect extends Vps_Component_Generator_Static
{
    protected function _init()
    {
        if (!is_array($this->_settings['component']) || $this->_settings['component'] < 2) {
            throw new Vps_Exception("You need at least two components for a Box_StaticSelect generator");
        }
        if (!isset($this->_settings['model'])) {
            $this->_settings['model'] = 'Vps_Component_Generator_Box_StaticSelect_Model';
        }
        parent::_init();
    }

    protected function _formatConfig($parentData, $key)
    {
        $ret = array(
            'componentId' => $parentData->componentId . $this->_idSeparator . $this->getGeneratorKey(),
            'dbId' => $parentData->dbId . $this->_idSeparator . $this->getGeneratorKey(),
            'parent' => $parentData,
            'isPage' => false,
            'isPseudoPage' => false,
            'inherit' => false
        );
        $ret['box'] = $this->_settings['generator'];

        $row = $this->_getModel()->getRow($parentData->dbId.'-'.$this->getGeneratorKey());
        if (!$row || !$row->component) {
            $cmps = $this->_settings['component'];
            $ret['componentClass'] = array_shift($cmps);
        } else {
            $ret['componentClass'] = $this->_settings['component'][$row->component];
        }
        return $ret;
    }

    public function getChildData($parentData, $select = array())
    {
        Vps_Benchmark::count('GenStaticSelect::getChildData');

        if (is_array($select)) {
            $select = new Vps_Component_Select($select);
        }
        $data = $this->_createData($parentData, $this->getGeneratorKey(), $select);
        return array($data);
    }

    public function getBoxes()
    {
        return array($this->getGeneratorKey());
    }

    public function getGeneratorFlags()
    {
        $ret = parent::getGeneratorFlags();
        $ret['box'] = true;
        return $ret;
    }

    public function getPagePropertiesForm()
    {
        $form = new Vps_Form();
        $form->setModel($this->_getModel());
        $select = $form->add(new Vps_Form_Field_Select('component', trlVps('Box Type')));
        $select->setAllowBlank(false);
        $values = array();
        foreach ($this->_settings['component'] as $k=>$c) {
            $values[$k] = Vpc_Abstract::getSetting($c, 'componentName');
        }
        $select->setValues($values);
        return $form;
    }

    public function getStaticChildComponentIds()
    {
        return array($this->_idSeparator.$this->getGeneratorKey());
    }
}
