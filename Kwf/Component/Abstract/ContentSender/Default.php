<?php
class Kwf_Component_Abstract_ContentSender_Default extends Kwf_Component_Abstract_ContentSender_Abstract
{
    private static function _getRequestWithFiles()
    {
        $ret = $_REQUEST;
        //in _REQUEST sind _FILES nicht mit drinnen
        foreach ($_FILES as $k=>$file) {
            if (is_array($file['tmp_name'])) {
                //wenn name[0] dann kommts in komischer form daher -> umwandeln
                foreach (array_keys($file['tmp_name']) as $i) {
                    foreach (array_keys($file) as $prop) {
                        $ret[$k][$i][$prop] = $file[$prop][$i];
                    }
                }
            } else {
                $ret[$k] = $file;
            }
        }
        return $ret;
    }

    protected function _getProcessInputComponents($includeMaster)
    {
        return self::__getProcessInputComponents($this->_data);
    }

    //public for unittest
    public static function __getProcessInputComponents($data)
    {
        $showInvisible = Kwf_Component_Data_Root::getShowInvisible();

        $cacheId = 'procI-'.$data->componentId;
        $success = false;
        if (!$showInvisible) { //don't cache in preview
            $cacheContents = Kwf_Cache_Simple::fetch($cacheId, $success);
            //cache is cleared in Kwf_Component_Events_ProcessInputCache
        }
        if (!$success) {
            $datas = array();
            foreach (self::_findProcessInputComponents($data) as $p) {
                $plugins = array();
                $c = $p;
                do {
                    foreach ($c->getPlugins('Kwf_Component_Plugin_Interface_SkipProcessInput') as $i) {
                        $plugins[] = array(
                            'pluginClass' => $i,
                            'componentId' => $c->componentId
                        );
                    }
                    $c = $c->parent;
                } while($c && !$c->isPage);
                $datas[] = array(
                    'data' => $p,
                    'plugins' => $plugins,
                );
            }
            if (!$showInvisible) {
                $cacheContents = array();
                foreach ($datas as $p) {
                    $cacheContents[] = array(
                        'data' => $p['data']->kwfSerialize(),
                        'plugins' => $p['plugins'],
                    );
                }
                Kwf_Cache_Simple::add($cacheId, $cacheContents);
            }
        } else {
            $datas = array();
            foreach ($cacheContents as $d) {
                $datas[] = array(
                    'data' => Kwf_Component_Data::kwfUnserialize($d['data']),
                    'plugins' => $d['plugins'],
                );
            }
        }
        //ask SkipProcessInput plugins if it should be skipped
        //evaluated every time
        $process = array();
        foreach ($datas as $d) {
            foreach ($d['plugins'] as $p) {
                $plugin = Kwf_Component_Plugin_Abstract::getInstance($p['pluginClass'], $p['componentId']);
                $result = $plugin->skipProcessInput();
                if ($result === Kwf_Component_Plugin_Interface_SkipProcessInput::SKIP_SELF_AND_CHILDREN) {
                    continue 2;
                }
                if ($result === Kwf_Component_Plugin_Interface_SkipProcessInput::SKIP_SELF &&
                    $p['componentId'] == $d['data']->componentId
                ) {
                    continue 2;
                }
            }
            $process[] = $d['data'];
        }

        return $process;
    }

    protected static function _findProcessInputComponents($data)
    {
        $process = $data
            ->getRecursiveChildComponents(array(
                    'page' => false,
                    'flags' => array('processInput' => true)
                ));
        $process = array_merge($process, $data
            ->getRecursiveChildComponents(array(
                    'page' => false,
                    'flags' => array('forwardProcessInput' => true)
                )));
        if (Kwf_Component_Abstract::getFlag($data->componentClass, 'processInput')) {
            $process[] = $data;
        }
        if (Kwf_Component_Abstract::getFlag($data->componentClass, 'forwardProcessInput')) {
            $process[] = $data;
        }
        $ret = array();
        foreach ($process as $i) {
            if (Kwf_Component_Abstract::getFlag($i->componentClass, 'processInput')) {
                $ret[] = $i;
            }
            if (Kwf_Component_Abstract::getFlag($i->componentClass, 'forwardProcessInput')) {
                $ret = array_merge($ret, $i->getComponent()->getForwardProcessInputComponents());
            }
        }
        return $ret;
    }

    protected static function _callProcessInput($process)
    {
        static $benchmarkEnabled;
        if (!isset($benchmarkEnabled)) $benchmarkEnabled = Kwf_Benchmark::isEnabled();

        $postData = self::_getRequestWithFiles();
        foreach ($process as $i) {
            Kwf_Benchmark::count('processInput', $i->componentId);
            if (method_exists($i->getComponent(), 'preProcessInput')) {
                $startTime = microtime(true);
                $i->getComponent()->preProcessInput($postData);
                if ($benchmarkEnabled) Kwf_Benchmark::subCheckpoint($i->componentId.' preProcessInput', microtime(true)-$startTime);
            }
        }
        foreach ($process as $i) {
            if (method_exists($i->getComponent(), 'processInput')) {
                $startTime = microtime(true);
                $i->getComponent()->processInput($postData);
                if ($benchmarkEnabled) Kwf_Benchmark::subCheckpoint($i->componentId, microtime(true)-$startTime);
            }
        }
        if (class_exists('Kwf_Component_ModelObserver', false)) { //Nur wenn klasse jemals geladen wurde kann auch was zu processen drin sein
            Kwf_Component_ModelObserver::getInstance()->process(false);
        }
    }

    protected static function _callPostProcessInput($process)
    {
        $postData = self::_getRequestWithFiles();
        foreach ($process as $i) {
            if (method_exists($i->getComponent(), 'postProcessInput')) {
                $i->getComponent()->postProcessInput($postData);
            }
        }
        if (class_exists('Kwf_Component_ModelObserver', false)) { //Nur wenn klasse jemals geladen wurde kann auch was zu processen drin sein
            Kwf_Component_ModelObserver::getInstance()->process();
        }
    }

    protected function _render($includeMaster)
    {
        return $this->_data->render(null, $includeMaster);
    }

    public function sendContent($includeMaster)
    {
        if (Kwf_Util_Https::supportsHttps()) {

            $foundRequestHttps = Kwf_Util_Https::doesComponentRequestHttps($this->_data);

            if (isset($_SERVER['HTTPS'])) {
                //we are on https
                if (!$foundRequestHttps && isset($_COOKIE['kwcAutoHttps']) && !Zend_Session::sessionExists() && !Zend_Session::isStarted()) {
                    //we where auto-redirected to https but don't need https anymore
                    setcookie('kwcAutoHttps', '', 0, '/'); //delete cookie
                    Kwf_Util_Https::ensureHttp();
                }
            } else {
                //we are on http
                if ($foundRequestHttps) {
                    setcookie('kwcAutoHttps', '1', 0, '/');
                    Kwf_Util_Https::ensureHttps();
                }
            }

            Kwf_Benchmark::checkpoint('check requestHttps');
        }

        $this->_sendHeader();
        $startTime = microtime(true);
        $process = $this->_getProcessInputComponents($includeMaster);
        Kwf_Benchmark::subCheckpoint('getProcessInputComponents', microtime(true)-$startTime);
        self::_callProcessInput($process);
        Kwf_Benchmark::checkpoint('processInput');
        echo $this->_render($includeMaster);
        Kwf_Benchmark::checkpoint('render');
        self::_callPostProcessInput($process);
        Kwf_Benchmark::checkpoint('postProcessInput');
    }

    protected function _sendHeader()
    {
        header('Content-Type: text/html; charset=utf-8');
    }
}
