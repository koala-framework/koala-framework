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

    public static function getAssetVariables($section = 'web', &$mtimeFiles = array())
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
        return $assetVariables[$section];
    }

    public static function expandAssetVariables($contents, $section = 'web', &$mtimeFiles = array())
    {
        if (strpos($contents, 'var(')===false) return $contents;
        $assetVariables = self::getAssetVariables($section, $mtimeFiles);
        foreach ($assetVariables as $k=>$i) {
            //$contents = preg_replace('#\\$'.preg_quote($k).'([^a-z0-9A-Z])#', "$i\\1", $contents); //deprecated syntax
            $contents = str_replace('var('.$k.')', $i, $contents);
        }
        return $contents;
    }

    protected function _processContents($ret)
    {
        $pathType = $this->getType();

        if ($pathType == 'ext2') {
            //hack um bei ext-css-dateien korrekte pfade für die bilder zu haben
            $ret = str_replace('../images/', '/assets/ext2/resources/images/', $ret);
        } else if ($pathType == 'mediaelement') {
            //hack to get the correct paths for the mediaelement pictures
            $ret = str_replace('url(', 'url(/assets/mediaelement/build/', $ret);
        } else if ($pathType == 'socialshareprivacy') {
            $ret = str_replace('url(', 'url(/assets/socialshareprivacy/socialshareprivacy/', $ret);
        }
        if ($baseUrl = Kwf_Setup::getBaseUrl()) {
            $ret = preg_replace('#url\\((\s*[\'"]?)/assets/#', 'url($1'.$baseUrl.'/assets/', $ret);
        }

        $ret = self::expandAssetVariables($ret);


        if (strpos($ret, 'cssClass') !== false && (strpos($ret, '$cssClass') !== false || strpos($ret, '.cssClass') !== false)) {
            $cssClass = $this->_getComponentCssClass();
            if ($cssClass) {
                $ret = str_replace('$cssClass', $cssClass, $ret);
                $ret = str_replace('.cssClass', '.'.$cssClass, $ret);
            }
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
