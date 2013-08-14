<?php
class Kwf_Assets_Dependency_File_Js extends Kwf_Assets_Dependency_File
{
    public function getMimeType()
    {
        return 'text/javascript';
    }

    public function getContents($language)
    {
        $ret = parent::getContents($language);

        //TODO same code is in in File_Css too
        $pathType = substr($this->_fileName, 0, strpos($this->_fileName, '/'));
        if ($pathType == 'ext') {
            //hack um bei ext-css-dateien korrekte pfade für die bilder zu haben
            $ret = str_replace('../images/', '/assets/ext/resources/images/', $ret);
        } else if ($pathType == 'mediaelement') {
            //hack to get the correct paths for the mediaelement pictures
            $ret = str_replace('url(', 'url(/assets/mediaelement/build/', $ret);
        }

        /*
        TODO implement
        $ret = str_replace(
            '{$application.maxAssetsMTime}',
            $this->getDependencies()->getMaxFileMTime(),
            $ret);
        */

        static $jsLoader;
        if (!isset($jsLoader)) $jsLoader = new Kwf_Trl_JsLoader();

        $ret = $jsLoader->trlLoad($ret, $language);
        $ret = $this->_hlp($ret, $language);

        if ($baseUrl = Kwf_Setup::getBaseUrl()) {
            $ret = preg_replace('#url\\((\s*[\'"]?)/assets/#', 'url($1'.$baseUrl.'/assets/', $ret);
            $ret = preg_replace('#([\'"])/(kwf|vkwf|admin|assets)/#', '$1'.$baseUrl.'/$2/', $ret);
        }

        return $ret;
    }

    private function _hlp($contents, $language)
    {
        //TODO 1902 $language verwenden
        $matches = array();
        preg_match_all("#hlp\(['\"](.+?)['\"]\)#", $contents, $matches);
        foreach ($matches[0] as $key => $search) {
            $r = hlp($matches[1][$key]);
            $r = str_replace(array("\n", "\r", "'"), array('\n', '', "\\'"), $r);
            $contents = str_replace($search, "'" . $r . "'", $contents);
        }
        return $contents;
    }

    public function getContentsPacked($language)
    {
        $contents = $this->getContents($language);

        $contents = str_replace("\r", "\n", $contents);

        // remove comments
        $contents = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $contents);
        // deaktiviert wg. urls mit http:// in hilfetexten $contents = preg_replace('!//[^\n]*!', '', $contents);

        // remove tabs, spaces, newlines, etc. - funktioniert nicht - da fehlen hinundwider ;
        //$contents = str_replace(array("\r", "\n", "\t"), "", $contents);

        // multiple whitespaces
        $contents = str_replace("\t", " ", $contents);
        $contents = preg_replace('/(\n)\n+/', '$1', $contents);
        $contents = preg_replace('/(\n)\ +/', '$1', $contents);
        $contents = preg_replace('/(\ )\ +/', '$1', $contents);

        return $contents;
    }
}
