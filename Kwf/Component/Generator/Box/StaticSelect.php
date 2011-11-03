<?php
class Kwf_Component_Generator_Box_StaticSelect extends Kwf_Component_Generator_Static
{
    protected $_eventsClass = 'Kwf_Component_Generator_Box_Events_StaticSelect';

    protected function _init()
    {
        if (!is_array($this->_settings['component']) || $this->_settings['component'] < 2) {
            throw new Kwf_Exception("You need at least two components for a Box_StaticSelect generator");
        }
        if (!isset($this->_settings['model'])) {
            $this->_settings['model'] = 'Kwf_Component_Generator_Box_StaticSelect_Model';
        }
        if (!isset($this->_settings['boxName'])) {
            $this->_settings['boxName'] = null;
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
        $ret['box'] = $this->getGeneratorKey();

        $id = $parentData->dbId.'-'.$this->getGeneratorKey();
        $component = $this->_getModel()->fetchColumnByPrimaryId('component', $id);
        if (!$component) {
            $cmps = $this->_settings['component'];
            $ret['componentClass'] = array_shift($cmps);
        } else {
            $ret['componentClass'] = $this->_settings['component'][$component];
        }
        return $ret;
    }

    public function getChildData($parentData, $select = array())
    {
        Kwf_Benchmark::count('GenStaticSelect::getChildData');

        if (is_array($select)) {
            $select = new Kwf_Component_Select($select);
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
        return new Kwf_Component_Generator_Box_StaticSelect_PagePropertiesForm($this);
    }

    public function getStaticChildComponentIds()
    {
        return array($this->_idSeparator.$this->getGeneratorKey());
    }
}
