<?php
class Kwf_Component_Generator_Box_Static extends Kwf_Component_Generator_Static
{
    protected function _init()
    {
        if (count($this->_settings['component']) > 1) {
            if (isset($this->_settings['box'])) {
                throw new Kwf_Exception("How would you put multiple components into one box?");
            }
        }
        parent::_init();
    }

    protected function _formatConfig($parentData, $key)
    {
        $ret = parent::_formatConfig($parentData, $key);
        $ret['box'] = isset($this->_settings['box']) ? $this->_settings['box'] : $key;
        return $ret;
    }

    public function getBoxes()
    {
        if (isset($this->_settings['box'])) {
            return array($this->_settings['box']);
        }
        return array_keys($this->_settings['component']);
    }

    public function getGeneratorFlags()
    {
        $ret = parent::getGeneratorFlags();
        $ret['box'] = true;
        return $ret;
    }
}
