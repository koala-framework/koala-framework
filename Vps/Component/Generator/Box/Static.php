<?php
class Vps_Component_Generator_Box_Static extends Vps_Component_Generator_Static
{
    protected function _init()
    {
        if (is_array($this->_settings['component'])) {
            if (isset($this->_settings['box'])) {
                throw new Vps_Exception("How would you put multiple components into one box?");
            }
        } else {
            $this->_settings['component'] = array($this->_settings['generator'] => $this->_settings['component']);
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
