<?php
class Vpc_Advanced_SearchEngineReferer_Component extends Vpc_Abstract_Composite_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['allowedHosts'] = array(
            'google', 'yahoo', 'msn', 'live', 'aol', 'altavista'
        );
        $ret['generators']['child']['component']['view'] =
            'Vpc_Advanced_SearchEngineReferer_ViewMyLatest_Component';
        $ret['componentName'] = trlVps('Search engine referer');
        $ret['childModel'] = 'Vpc_Advanced_SearchEngineReferer_Model';
        $ret['saveReferer'] = true;
        $ret['viewCache'] = true;
        $ret['flags']['processInput'] = true;
        return $ret;
    }

    public static function getStaticCacheMeta($componentClass)
    {
        $ret = parent::getStaticCacheMeta();
        $ret[] = new Vps_Component_Cache_Meta_Static_Model(Vpc_Abstract::getSetting($componentClass, 'childModel'));
        return $ret;
    }

    public function getViewCacheLifetime()
    {
        return $this->getData()->getChildComponent('-view')->getComponent()->getViewCacheLifetime();
    }

    public function processInput()
    {
        if (!$this->_getSetting('saveReferer')) return;
        if (!isset($_SERVER['HTTP_REFERER'])) return;
        if (!$_SERVER['HTTP_REFERER']) return;
        $referer = $_SERVER['HTTP_REFERER'];
        $host = parse_url($referer, PHP_URL_HOST);
        $allowedHosts = $this->_getSetting('allowedHosts');
        if (preg_match('/^(www\.)?(('.implode(')|(', $allowedHosts).'))\.[a-z]+$/i', $host)) {
            $model = $this->getChildModel();

            $rowCompare = $model->getRow($model->select()
                ->whereEquals('component_id', $this->getData()->parent->componentId)
                ->order('id', 'DESC'));

            $query = self::getQueryVar($referer);

            if ($rowCompare) {
                $hostCompare = parse_url($rowCompare->referer_url, PHP_URL_HOST);
                $queryCompare = self::getQueryVar($rowCompare->referer_url);
            }

            if ((!$rowCompare || $hostCompare != $host || $queryCompare != $query)
                && strpos($query, 'site:') === false
            ) {
                $row = $model->createRow();
                $row->component_id = $this->getData()->parent->componentId;
                $row->referer_url = $referer;
                $row->save();

                // alte löschen
                $select = new Vps_Model_Select();
                $select->whereEquals('component_id', $row->component_id)
                    ->order('id', 'DESC')
                    ->limit(20, 10);
                $deleteRows = $model->getRows($select);
                foreach ($deleteRows as $deleteRow) {
                    $deleteRow->delete();
                }
            }
        }
    }

    public static function getQueryVar($url)
    {
        $host = parse_url($url, PHP_URL_HOST);
        $queryString = parse_url($url, PHP_URL_QUERY);
        $queryVars = explode('&', $queryString);
        if (count($queryVars)) {
            foreach ($queryVars as $queryVar) {
                if (strpos($host, 'yahoo') !== false) {
                    if (substr($queryVar, 0, 2) == 'p=') {
                        $query = substr($queryVar, 2);
                        break;
                    }
                } else {
                    if (substr($queryVar, 0, 2) == 'q=') {
                        $query = substr($queryVar, 2);
                        break;
                    }
                }
            }
        }
        return isset($query) ? urldecode($query) : null;
    }
}
