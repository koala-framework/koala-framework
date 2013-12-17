<?php
class Kwf_Assets_Dependency_File_Css extends Kwf_Assets_Dependency_File
{
    private $_contentsCache;

    public function getMimeType()
    {
        return 'text/css';
    }
    public function getContents($language)
    {
        $ret = parent::getContents($language);
        $ret = $this->_processContents($ret);
        return $ret;
    }

    public static function expandAssetVariables($contents, $section = 'web', &$mtimeFiles = array())
    {
        static $assetVariables = array();
        if (!isset($assetVariables[$section])) {
            $assetVariables[$section] = Kwf_Config::getValueArray('assetVariables');
            if (file_exists('assetVariables.ini')) {
                $mtimeFiles[] = 'assetVariables.ini';
                $cfg = new Zend_Config_Ini('assetVariables.ini', $section);
                $assetVariables[$section] = array_merge($assetVariables[$section], $cfg->toArray());
            }
            foreach ($assetVariables[$section] as $k=>$i) {
                //also support lowercase variables
                if (strtolower($k) != $k) $assetVariables[$section][strtolower($k)] = $i;
            }
        }
        foreach ($assetVariables[$section] as $k=>$i) {
            $contents = preg_replace('#\\$'.preg_quote($k).'([^a-z0-9A-Z])#', "$i\\1", $contents); //deprecated syntax
            $contents = str_replace('var('.$k.')', $i, $contents);
        }
        return $contents;
    }

    protected function _processContents($ret)
    {
        $pathType = substr($this->_fileName, 0, strpos($this->_fileName, '/'));

        if ($pathType == 'ext') {
            //hack um bei ext-css-dateien korrekte pfade für die bilder zu haben
            $ret = str_replace('../images/', '/assets/ext/resources/images/', $ret);
        } else if ($pathType == 'mediaelement') {
            //hack to get the correct paths for the mediaelement pictures
            $ret = str_replace('url(', 'url(/assets/mediaelement/build/', $ret);
        }

        $ret = self::expandAssetVariables($ret);


        $cssClass = $this->_fileName;
        if (defined('VKWF_PATH') && substr($cssClass, 0, strlen(VKWF_PATH)) == VKWF_PATH) {
            $cssClass = substr($cssClass, strlen(VKWF_PATH)+1);
        }
        if (substr($cssClass, 0, strlen(KWF_PATH)) == KWF_PATH) {
            $cssClass = substr($cssClass, strlen(KWF_PATH)+1);
        }
        if (substr($cssClass, 0, strlen(getcwd())) == getcwd()) {
            $cssClass = substr($cssClass, strlen(getcwd())+1);
        }
        if (substr($cssClass, 0, 11) == 'components/') {
            $cssClass = substr($cssClass, 11);
        }
        if (substr($cssClass, 0, 7) == 'themes/') {
            $cssClass = substr($cssClass, 7);
        }
        if (substr($cssClass, -4) == '.css') {
            $cssClass = substr($cssClass, 0, -4);
        }
        if (substr($cssClass, -5) == '.scss') {
            $cssClass = substr($cssClass, 0, -5);
        }
        if (substr($cssClass, -9) == '.printcss') {
            $cssClass = substr($cssClass, 0, -9);
        }
        if (substr($cssClass, -10) == '/Component') {
            $cssClass = substr($cssClass, 0, -10);
        } else if (substr($cssClass, -7) == '/Master') {
            $cssClass = substr($cssClass, 0, -7);
            $cssClass = 'master'.$cssClass;
        } else {
            $cssClass = false;
        }

        if ($cssClass) {
            $cssClass = str_replace('/', '', $cssClass);
            $cssClass = strtolower(substr($cssClass, 0, 1)) . substr($cssClass, 1);
            $ret = str_replace('$cssClass', $cssClass, $ret);
            $ret = str_replace('.cssClass', '.'.$cssClass, $ret);
        }
        return $ret;
    }

    public function getContentsPacked($language)
    {
        if (!isset($this->_cacheContents)) {

            $contents = $this->getContents($language);

            $contents = str_replace("\r", "\n", $contents);

            // remove comments
            //$contents = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $contents);

            // multiple whitespaces
            $contents = str_replace("\t", " ", $contents);
            $contents = preg_replace('/(\n)\n+/', '$1', $contents);
            $contents = preg_replace('/(\n)\ +/', '$1', $contents);
            $contents = preg_replace('/(\ )\ +/', '$1', $contents);

            $this->_cacheContents = $contents;
        }

        return $this->_cacheContents;
    }
}
