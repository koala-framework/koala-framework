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

    protected static function _callProcessInput($process)
    {
        static $benchmarkEnabled;
        if (!isset($benchmarkEnabled)) $benchmarkEnabled = Kwf_Benchmark::isEnabled();

        $postData = self::_getRequestWithFiles();
        foreach ($process as $i) {
            Kwf_Benchmark::count('processInput', $i->componentId);
            if (method_exists($i->getComponent(), 'preProcessInput')) {
                if ($benchmarkEnabled) $startTime = microtime(true);
                $i->getComponent()->preProcessInput($postData);
                if ($benchmarkEnabled) Kwf_Benchmark::subCheckpoint($i->componentId.' preProcessInput', microtime(true)-$startTime);
            }
        }
        foreach ($process as $i) {
            if (method_exists($i->getComponent(), 'processInput')) {
                if ($benchmarkEnabled) $startTime = microtime(true);
                $i->getComponent()->processInput($postData);
                if ($benchmarkEnabled) Kwf_Benchmark::subCheckpoint($i->componentId, microtime(true)-$startTime);
            }
        }
        if (class_exists('Kwf_Events_ModelObserver', false)) { //Nur wenn klasse jemals geladen wurde kann auch was zu processen drin sein
            Kwf_Events_ModelObserver::getInstance()->process(false);
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
        if (class_exists('Kwf_Events_ModelObserver', false)) { //Nur wenn klasse jemals geladen wurde kann auch was zu processen drin sein
            Kwf_Events_ModelObserver::getInstance()->process();
        }
    }

    public function sendContent($includeMaster)
    {
        $benchmarkEnabled = Kwf_Benchmark::isEnabled();

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

            if ($benchmarkEnabled) Kwf_Benchmark::checkpoint('check requestHttps');
        }


        $this->_sendHeader();
        if ($benchmarkEnabled) $startTime = microtime(true);
        $process = $this->_getProcessInputComponents($includeMaster);
        if ($benchmarkEnabled) Kwf_Benchmark::subCheckpoint('getProcessInputComponents', microtime(true)-$startTime);
        self::_callProcessInput($process);
        if ($benchmarkEnabled) Kwf_Benchmark::checkpoint('processInput');
        echo $this->_render($includeMaster);
        if ($benchmarkEnabled) Kwf_Benchmark::checkpoint('render');
        self::_callPostProcessInput($process);
        if ($benchmarkEnabled) Kwf_Benchmark::checkpoint('postProcessInput');
    }

    protected function _sendHeader()
    {
        header('Content-Type: text/html; charset=utf-8');
    }
}
