<?php
class Kwf_Component_Generator_MultiBox_Static extends Kwf_Component_Generator_Static
{
    protected function _formatConfig($parentData, $key)
    {
        $ret = parent::_formatConfig($parentData, $key);
        $ret['box'] = isset($this->_settings['box']) ? $this->_settings['box'] : $key;
        return $ret;
    }

    public function getGeneratorFlags()
    {
        $ret = parent::getGeneratorFlags();
        $ret['multiBox'] = true;
        return $ret;
    }
}
