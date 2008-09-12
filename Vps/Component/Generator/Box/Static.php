<?php
class Vps_Component_Generator_Box_Static extends Vps_Component_Generator_Static
    implements Vps_Component_Generator_Box_Interface
{
    protected function _formatConfig($parentData, $key)
    {
        $ret = parent::_formatConfig($parentData, $key);
        $ret['box'] = isset($this->_settings['box']) ? $this->_settings['box'] : $key;
        return $ret;
    }

    public function getPriority()
    {
        return $this->_settings['priority'];
    }
    public function getBoxes()
    {
        if (isset($this->_settings['box'])) {
            return array($this->_settings['box']);
        }
        return array_keys($this->_settings['component']);
    }
}
