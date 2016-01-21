<?php
class Kwf_Component_Abstract_ContentSender_Default extends Kwf_Component_Abstract_ContentSender_Abstract
{
    private static function _getRequestWithFiles()
    {
        //don't use $_REQUEST, we don't want cookies
        $ret = array_merge($_GET, $_POST);
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
        if ($this->_data->getBaseProperty('preLogin')) {
            $ignore = false;
            foreach (Kwf_Config::getValueArray('preLoginIgnoreIp') as $i) {
                if ($_SERVER['REMOTE_ADDR'] == $i) $ignore = true;
                if (!$ignore) {
                    $i = substr($i, 0, -1);
                    if ($i == '*' && substr($_SERVER['REMOTE_ADDR'], 0, strlen($i)) == $i) $ignore = true;
                }
                if (!$ignore) {
                    $i = substr($i, 1);
                    if ($i == '*' && substr($_SERVER['REMOTE_ADDR'], -strlen($i)) == $i) $ignore = true;
                }
            }
            if (!$ignore && (empty($_SERVER['PHP_AUTH_USER']) || empty($_SERVER['PHP_AUTH_PW']) ||
                $_SERVER['PHP_AUTH_USER'] != $this->_data->getBaseProperty('preLoginUser') ||
                $_SERVER['PHP_AUTH_PW'] != $this->_data->getBaseProperty('preLoginPassword')
            )) {
                header('WWW-Authenticate: Basic realm="Page locked by preLogin"');
                throw new Kwf_Exception_AccessDenied();
            }
        }

        $benchmarkEnabled = Kwf_Benchmark::isEnabled();

        if ($benchmarkEnabled) $startTime = microtime(true);
        $process = $this->_getProcessInputComponents($includeMaster);
        if ($benchmarkEnabled) Kwf_Benchmark::subCheckpoint('getProcessInputComponents', microtime(true)-$startTime);
        self::_callProcessInput($process);
        if ($benchmarkEnabled) Kwf_Benchmark::checkpoint('processInput');

        $hasDynamicParts = false;
        $out = $this->_render($includeMaster, $hasDynamicParts);
        if ($benchmarkEnabled) Kwf_Benchmark::checkpoint('render');

        header('Content-Type: text/html; charset=utf-8');
        if (!$hasDynamicParts) {
            $lifetime = 60*60;
            header('Cache-Control: public, max-age='.$lifetime);
            header('Expires: '.gmdate("D, d M Y H:i:s \G\M\T", time()+$lifetime));
            header('Pragma: public');
        }
        echo $out;

        self::_callPostProcessInput($process);
        if ($benchmarkEnabled) Kwf_Benchmark::checkpoint('postProcessInput');
    }

    //removed, if required add _getMimeType() method
    final protected function _sendHeader() {}
}
