<?php
class Kwc_Advanced_SearchEngineReferer_Component extends Kwc_Abstract_Composite_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['allowedHosts'] = array(
            'google', 'yahoo', 'msn', 'live', 'aol', 'altavista'
        );
        $ret['generators']['child']['component']['view'] =
            'Kwc_Advanced_SearchEngineReferer_ViewMyLatest_Component';
        $ret['componentName'] = trlKwfStatic('Search engine referer');
        $ret['childModel'] = 'Kwc_Advanced_SearchEngineReferer_Model';
        $ret['saveReferer'] = true;
        $ret['flags']['processInput'] = true;
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
                ->whereEquals('component_id', $this->getData()->componentId)
                ->order('id', 'DESC'));

            $query = self::getQueryVar($referer);
            
            if ($query){
                if ($rowCompare) {
                    $hostCompare = parse_url($rowCompare->referer_url, PHP_URL_HOST);
                    $queryCompare = self::getQueryVar($rowCompare->referer_url);
                }
    
                if ((!$rowCompare || $hostCompare != $host || $queryCompare != $query)
                    && strpos($query, 'site:') === false
                ) {
                    $row = $model->createRow();
                    $row->component_id = $this->getData()->componentId;
                    $row->referer_url = $referer;
                    $row->save();
    
                    // alte löschen
                    $select = new Kwf_Model_Select();
                    $select->whereEquals('component_id', $row->component_id)
                        ->order('id', 'DESC')
                        ->limit(20, 10);
                    $deleteRows = $model->getRows($select);
                    foreach ($deleteRows as $deleteRow) {
                        $deleteRow->delete();
                    }
                    Kwf_Events_ModelObserver::getInstance()->process();
                }
            } else {
                file_put_contents('log/unknownsearchenginereferer', $referer. "\n", FILE_APPEND);
            }
            
        }
    }

    public static function getQueryVar($url)
    {
        $host = parse_url($url, PHP_URL_HOST);
        $parts = parse_url($url);
        $queryString = null;
        if (isset($parts['query'])) {
            $queryString = $parts['query'];
        } else if (isset($parts['fragment'])) {
            $queryString = $parts['fragment'];
        }
        if (!$queryString) return null;
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
