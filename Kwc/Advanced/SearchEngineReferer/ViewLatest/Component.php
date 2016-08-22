<?php
class Kwc_Advanced_SearchEngineReferer_ViewLatest_Component
    extends Kwc_Abstract
{
    private $_referersCache = null;
    private $_parentModel;

    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['limit'] = 5;
        $ret['placeholder']['header'] = trlKwfStatic('Latest referer');
        return $ret;
    }

    public function getViewCacheLifetime()
    {
        return 60*60;
    }

    public function getTemplateVars(Kwf_Component_Renderer_Abstract $renderer)
    {
        $ret = parent::getTemplateVars($renderer);
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
                $component = Kwf_Component_Data_Root::getInstance()->getComponentById(
                    $row->component_id
                );
                $this->_referersCache[] = array(
                    'component' => $component,
                    'row'       => $row,
                    'host'      => $host,
                    'query'     => Kwc_Advanced_SearchEngineReferer_Component::getQueryVar($row->referer_url)
                );
            }
        }
        return $this->_referersCache;
    }

    public function emptyReferersCache()
    {
        $this->_referersCache = null;
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
