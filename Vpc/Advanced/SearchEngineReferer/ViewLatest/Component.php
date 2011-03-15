<?php
class Vpc_Advanced_SearchEngineReferer_ViewLatest_Component
    extends Vpc_Abstract
{
    private $_referersCache = null;
    private $_parentModel;

    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['limit'] = 5;
        $ret['placeholder']['header'] = trlVps('Latest referer');
        return $ret;
    }

    public function getViewCacheLifetime()
    {
        return 60*60;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['referers'] = $this->_getReferers();
        return $ret;
    }

    protected function _getParentModel()
    {
        if (!isset($this->_parentModel)) {
            $this->_parentModel = $this->getData()->parent->getComponent()->getChildModel();
        }
        return $this->_parentModel;
    }

    private function _getReferers()
    {
        if (is_null($this->_referersCache)) {
            $model = $this->_getParentModel();
            $rowset = $model->getRows($this->_getSelect());

            $this->_referersCache = array();
            foreach ($rowset as $row) {
                $host = parse_url($row->referer_url, PHP_URL_HOST);
                $component = Vps_Component_Data_Root::getInstance()->getComponentById(
                    $row->component_id
                );
                $this->_referersCache[] = array(
                    'component' => $component,
                    'row'       => $row,
                    'host'      => $host,
                    'query'     => Vpc_Advanced_SearchEngineReferer_Component::getQueryVar($row->referer_url)
                );
            }
        }
        return $this->_referersCache;
    }

    public function hasContent()
    {
        $refs = $this->_getReferers();
        return count($refs) ? true : false;
    }

    protected function _getSelect()
    {
        $model = $this->_getParentModel();
        return $model->select()
            ->order('id', 'DESC')
            ->limit($this->_getSetting('limit'));
    }
}
