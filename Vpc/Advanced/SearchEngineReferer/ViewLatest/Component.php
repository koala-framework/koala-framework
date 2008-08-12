<?php
class Vpc_Advanced_SearchEngineReferer_ViewLatest_Component
    extends Vpc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['limit'] = 5;
        $ret['viewCache'] = false;
        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();

        $table = $this->getData()->parent->getComponent()->getTable();
        $rowset = $table->fetchAll($this->_getWhere(), 'id DESC', $this->_getSetting('limit'));

        $ret['referers'] = array();
        foreach ($rowset as $row) {
            $host = parse_url($row->referer_url, PHP_URL_HOST);
            $ret['referers'][] = array(
                'component' => Vps_Component_Data_Root::getInstance()->getComponentByDbId($row->component_id),
                'row'       => $row,
                'host'      => $host,
                'query'     => Vpc_Advanced_SearchEngineReferer_Component::getQueryVar($row->referer_url)
            );
        }
        return $ret;
    }

    protected function _getWhere()
    {
        return array();
    }
}
