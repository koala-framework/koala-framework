<?php
class Kwf_Assets_Dispatcher
{
    public static function getInstance()
    {
        static $i;
        if (!isset($i)) {
            $cls = Kwf_Config::getValue('assets.dispatcherClass');
            $i = new $cls();
        }
        return $i;
    }

    public function dispatch($url)
    {
        if (substr($url, 0, 14)=='/assets/build/') {
            $out = $this->getOutputForUrl($url);
            Kwf_Media_Output::output($out);
        }
    }

    public function allowSourceAccess()
    {
        $ok = false;
        foreach (Kwf_Config::getValueArray('debug.assets.sourceAccessIp') as $i) {
            if (!$i) continue;
            if (substr($i, -1)=='*') {
                $i = substr($i, 0, -1);
                if (substr($_SERVER['REMOTE_ADDR'], 0, strlen($i)) == $i) {
                    $ok = true;
                }
            } else {
                if ($_SERVER['REMOTE_ADDR'] == $i) $ok = true;
            }
        }
        return $ok;
    }

    public function getOutputForUrl($url)
    {
        if (substr($url, 0, 14) != '/assets/build/') throw new Kwf_Exception("invalid url: '$url'");
        $url = substr($url, 14);

        if (substr($url, -4)=='.css') {
            $mimeType = 'text/css; charset=utf-8';
        } else if (substr($url, -3)=='.js') {
            $mimeType = 'text/javascript; charset=utf-8';
        } else {
            throw new Kwf_Assets_NotFoundException("Invalid filetype");
        }

        if (!preg_match('#^[a-z0-9./]+$#i', $url) || preg_match('#\.\.#i', $url)) {
            throw new Kwf_Exception_NotFound();
        }

        return array(
            'file' => 'build/assets/'.$url,
            'mimeType' => $mimeType
        );
    }
}
