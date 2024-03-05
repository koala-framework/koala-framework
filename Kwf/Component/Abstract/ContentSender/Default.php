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

    public function getContent($includeMaster)
    {
        if ($this->_data->getBaseProperty('preLogin')) {
            $ignore = false;
            foreach (Kwf_Config::getValueArray('preLoginIgnore') as $i) {
                if (substr($_SERVER['REDIRECT_URL'], 0, strlen($i)) == $i) $ignore  = true;
            }
            foreach (Kwf_Config::getValueArray('preLoginIgnoreIp') as $i) {
                $ip = $_SERVER['REMOTE_ADDR'];
                if ($ip == $i) $ignore = true;
                if (!$ignore && substr($i, -1) == '*') {
                    $i = substr($i, 0, -1);
                    if (substr($ip, 0, strlen($i)) == $i) $ignore = true;
                }
                if (!$ignore && substr($i, 0, 1) == '*') {
                    $i = substr($i, 1);
                    if (substr($ip, -strlen($i)) == $i) $ignore = true;
                }
            }
            if (!$ignore) Kwf_Setup::checkPreLogin($this->_data->getBaseProperty('preLoginUser'), $this->_data->getBaseProperty('preLoginPassword'));
        }

        foreach ($this->_data->getPlugins('Kwf_Component_Plugin_Interface_Redirect') as $p) {
            $p = Kwf_Component_Plugin_Abstract::getInstance($p, $this->_data->componentId);
            if ($redirect = $p->getRedirectUrl($this->_data)) {
                header('Location: '.$redirect);
                exit;
            }
        }

        $benchmarkEnabled = Kwf_Benchmark::isEnabled();

        if ($benchmarkEnabled) $startTime = microtime(true);
        $process = $this->_getProcessInputComponents($includeMaster);
        if ($benchmarkEnabled) Kwf_Benchmark::subCheckpoint('getProcessInputComponents', microtime(true)-$startTime);
        self::_callProcessInput($process);
        if ($benchmarkEnabled) Kwf_Benchmark::checkpoint('processInput');

        $ret = array();
        $hasDynamicParts = false;
        $ret['content'] = $this->_render($includeMaster, $hasDynamicParts);
        if ($benchmarkEnabled) Kwf_Benchmark::checkpoint('render');

        $ret['mimeType'] = 'text/html; charset=utf-8';

        if (!$includeMaster) {
            $assetsBox = $this->_data->getChildComponent('-assets');
            if ($assetsBox) {
                $ret['assets'] = $assetsBox->render(null, false, $hasDynamicParts);
            } else {
                $ret['assets'] = '';
            }
        }

        if (!$hasDynamicParts && !$process) {
            $ret['lifetime'] = 60*60;
        }
        self::_callPostProcessInput($process);
        if ($benchmarkEnabled) Kwf_Benchmark::checkpoint('postProcessInput');

        return $ret;
    }

    public function sendContent($includeMaster)
    {
        $content = $this->getContent($includeMaster);
        $content['contents'] = $content['content'];
        unset($content['content']);
        if (!isset($content['lifetime'])) $content['lifetime'] = false;
        if (Kwf_Benchmark::isEnabled()) {
            ob_start();
            Kwf_Benchmark::output();
            $content['contents'] .= ob_get_contents();
            ob_end_clean();
        }

        foreach ($this->_getHeaders() as $header => $headerValue) {
            header($header . ': ' . $headerValue);
        }

        Kwf_Media_Output::outputWithoutShutdown($content);
        exit;
    }

    protected function _getHeaders()
    {
        return array(
            'Strict-Transport-Security' => 'max-age=31536000',
            'X-Content-Type-Options' => 'nosniff',
            'X-Frame-Options' => 'SAMEORIGIN',
            'X-XSS-Protection' => '1; mode=block',
            'Referrer-Policy' => 'strict-origin-when-cross-origin',
        );
    }

    //removed, if required add _getMimeType() method
    final protected function _sendHeader() {}
}
