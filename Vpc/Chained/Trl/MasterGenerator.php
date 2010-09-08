<?php
class Vpc_Chained_Trl_MasterGenerator extends Vpc_Chained_Abstract_MasterGenerator
{
    private $_languageRow = null;

    public function getPagesControllerConfig($component, $generatorClass = null)
    {
        $ret = parent::getPagesControllerConfig($component, $generatorClass);
        $ret['icon'] = 'plugin';
        return $ret;
    }

    private function _getLanguageRow()
    {
        if (!$this->_languageRow) {
            $s = new Vps_Model_Select();
            $s->whereEquals('master', 1);
            $this->_languageRow = $this->_getModel()->getRow($s);
        }
        return $this->_languageRow;
    }

    protected function _formatConfig($parentData, $componentKey)
    {
        $data = parent::_formatConfig($parentData, $componentKey);
        $data['name'] = $this->_getLanguageRow()->name;
        $data['language'] = $this->_getLanguageRow()->filename;
        return $data;
    }

    protected function _getFilenameFromRow($componentKey, $parentData)
    {
        return $this->_getLanguageRow()->filename;
    }
}
