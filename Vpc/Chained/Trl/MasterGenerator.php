<?php
class Vpc_Chained_Trl_MasterGenerator extends Vpc_Chained_Abstract_MasterGenerator
{
    private $_languageRowCache = array();

    public function getPagesControllerConfig($component, $generatorClass = null)
    {
        $ret = parent::getPagesControllerConfig($component, $generatorClass);
        $ret['icon'] = 'plugin';
        return $ret;
    }

    protected function _getLanguageRow($parentData)
    {
        $s = new Vps_Model_Select();
        $s->whereEquals('master', 1);
        return $this->_getModel()->getRow($s);
    }

    private function _getLanguageRowCached($parentData)
    {
        if (!isset($this->_languageRowCache[$parentData->componentId])) {
            $this->_languageRowCache[$parentData->componentId] = $this->_getLanguageRow($parentData);
        }
        return $this->_languageRowCache[$parentData->componentId];
    }

    protected function _formatConfig($parentData, $componentKey)
    {
        $data = parent::_formatConfig($parentData, $componentKey);
        $data['name'] = $this->_getLanguageRowCached($parentData)->name;
        $data['language'] = $this->_getLanguageRowCached($parentData)->filename;
        return $data;
    }

    protected function _getFilenameFromRow($componentKey, $parentData)
    {
        return $this->_getLanguageRowCached($parentData)->filename;
    }
}
