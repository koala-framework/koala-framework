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
        $ret['tablename'] = 'Vpc_Advanced_SearchEngineReferer_Model';
        $ret['limit'] = 5;
        $ret['saveReferer'] = true;
        $ret['flags']['processInput'] = true;
        return $ret;
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
            $table = $this->getTable();

            $where = array('component_id = ?' => $this->getData()->parent->dbId);
            $rowCompare = $table->fetchRow($where, 'id DESC');

            $query = self::getQueryVar($referer);

            if ($rowCompare) {
                $hostCompare = parse_url($rowCompare->referer_url, PHP_URL_HOST);
                $queryCompare = self::getQueryVar($rowCompare->referer_url);
            }

            if ((!$rowCompare || $hostCompare != $host || $queryCompare != $query)
                && strpos($query, 'site:') === false
            ) {
                $row = $table->createRow();
                $row->component_id = $this->getData()->parent->dbId;
                $row->referer_url = $referer;
                $row->save();
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
