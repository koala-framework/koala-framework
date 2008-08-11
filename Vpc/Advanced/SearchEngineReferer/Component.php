<?php
class Vpc_Advanced_SearchEngineReferer_Component extends Vpc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['allowedHosts'] = array(
            'google', 'yahoo', 'msn', 'live', 'aol', 'altavista'
        );
        $ret['componentName'] = trlVps('Search engine referer');
        $ret['tablename'] = 'Vpc_Advanced_SearchEngineReferer_Model';
        $ret['limit'] = 5;
        $ret['viewCache'] = false;
        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $this->_saveReferer();

        $limit = $this->_getSetting('limit');

        $table = $this->getTable();
        $where = array('component_id = ?' => $this->getData()->dbId);
        $ret['referers'] = array();
        $i = 0;
        $rowset = $table->fetchAll($where, 'id DESC');
        foreach ($rowset as $row) {
            if ($i < $limit) {
                $host = parse_url($row->referer_url, PHP_URL_HOST);
                $tmpVar = array(
                    'time'    => $row->create_time,
                    'referer' => $row->referer_url,
                    'host'    => $host,
                    'query'   => $this->_getQueryVar($row->referer_url)
                );
                $ret['referers'][] = $tmpVar;
            } else {
                $row->delete();
            }
            $i++;
        }
        return $ret;
    }

    private function _saveReferer()
    {
        if (!empty($_SERVER['HTTP_REFERER'])) {
            $referer = $_SERVER['HTTP_REFERER'];
            $host = parse_url($referer, PHP_URL_HOST);
            $allowedHosts = $this->_getSetting('allowedHosts');
            if (preg_match('/^(www\.)?(('.implode(')|(', $allowedHosts).'))\.[a-z]+$/i', $host)) {
                $table = $this->getTable();

                $where = array('component_id = ?' => $this->getData()->dbId);
                $rowCompare = $table->fetchRow($where, 'id DESC');

                $query = $this->_getQueryVar($referer);

                if ($rowCompare) {
                    $hostCompare = parse_url($rowCompare->referer_url, PHP_URL_HOST);
                    $queryCompare = $this->_getQueryVar($rowCompare->referer_url);
                }

                if ((!$rowCompare || $hostCompare != $host || $queryCompare != $query)
                    && strpos($query, 'site:') === false
                ) {
                    $row = $table->createRow();
                    $row->component_id = $this->getData()->dbId;
                    $row->referer_url = $referer;
                    $row->save();
                    return true;
                }
            }
        }
        return false;
    }

    private function _getQueryVar($url)
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
