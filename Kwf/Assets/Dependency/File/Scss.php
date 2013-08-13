<?php
class Kwf_Assets_Dependency_File_Scss extends Kwf_Assets_Dependency_File_Css
{
    public function getContents($language)
    {
        $ret = parent::getContents($language);
        static $scssParser;
        if (!isset($scssParser)) {
            $scssParser = new Kwf_Util_SassParser(array(
                'style' => 'compact',
                'cache' => false,
                'syntax' => 'scss',
                'debug' => true,
                'debug_info' => false,
                'load_path_functions' => array('Kwf_Util_SassParser::loadCallback'),
                'functions' => Kwf_Util_SassParser::getExtensionsFunctions(array('Compass', 'Susy', 'Kwf')),
                'extensions' => array('Compass', 'Susy', 'Kwf')
            ));
        }
        $ret = $scssParser->toCss($ret, false);
        return $ret;
    }

}
