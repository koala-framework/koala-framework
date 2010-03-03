<?php
class Vpc_Root_TrlRoot_MasterGenerator extends Vps_Component_Generator_PseudoPage_Static
{
    private $_langaugeRow = null;
    protected $_inherits = true;
    protected $_loadTableFromComponent = true;

    public function getPagesControllerConfig($component, $generatorClass = null)
    {
        $ret = parent::getPagesControllerConfig($component, $generatorClass);
        $ret['icon'] = 'font';
        return $ret;
    }

    private function _getLanguageRow()
    {
        if (!$this->_langaugeRow) {
            $s = new Vps_Model_Select();
            $s->whereEquals('master', 1);
            $this->_langaugeRow = $this->_getModel()->getRow($s);
        }
        return $this->_langaugeRow;
    }

    protected function _formatConfig($parentData, $componentKey)
    {
        $data = parent::_formatConfig($parentData, $componentKey);
        $data['name'] = $this->_getLanguageRow()->name;
        return $data;
    }
    
    protected function _getFilenameFromRow($componentKey, $parentData)
    {
        return $this->_getLanguageRow()->filename;
    }
}
