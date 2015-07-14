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

    public static function getAssetVariables($section = 'web')
    {
        static $assetVariables = array();
        if (!isset($assetVariables[$section])) {
            $assetVariables[$section] = Kwf_Config::getValueArray('assetVariables');
            if (file_exists('assetVariables.ini')) {
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

    public static function expandAssetVariables($contents, $section = 'web')
    {
        if (strpos($contents, 'var(')===false) return $contents;
        $assetVariables = self::getAssetVariables($section);
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
        }

        //convert relative paths (as in bower dependencies; example: jquery.socialshareprivacy, mediaelement
        $fn = $this->getFileNameWithType();
        $fnDir = substr($fn, 0, strrpos($fn, '/'));
        $ret = preg_replace('#url\(\s*([^)]+?)\s*\)#', 'url(\1)', $ret); //remove spaces inside url()
        $ret = preg_replace('#url\((\'|")(?![a-z]+:)([^/\'"])#', 'url(\1/assets/'.$fnDir.'/\2', $ret);
        $ret = preg_replace('#url\((?![a-z]+:)([^/\'"])#', 'url(/assets/'.$fnDir.'/\1', $ret);
        $ret = self::expandAssetVariables($ret);

        if (strpos($ret, 'kwfup-') !== false) {
            if (Kwf_Config::getValue('application.uniquePrefix')) {
                $ret = str_replace('kwfup-', Kwf_Config::getValue('application.uniquePrefix').'-', $ret);
            } else {
                $ret = str_replace('kwfup-', '', $ret);
            }
        }
        if (strpos($ret, 'kwcbem__') !== false) {
            if (Kwf_Config::getValue('application.uniquePrefix')) {
                $ret = str_replace('.cssClass .kwcbem__', '.kwcbem__', $ret);
                $ret = str_replace('kwcbem__', $this->_getComponentCssClass().'__', $ret);
            } else {
                $ret = str_replace('kwcbem__', '', $ret);
            }
        }
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

        return Kwf_SourceMaps_SourceMap::createEmptyMap($this->_cacheContents);
    }
}
