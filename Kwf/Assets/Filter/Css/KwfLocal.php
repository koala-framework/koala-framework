<?php
class Kwf_Assets_Filter_Css_KwfLocal extends Kwf_Assets_Filter_Css_SelectorReplace
{
    public function __construct()
    {
        parent::__construct(array());
    }

    public function getPluginOptions(Kwf_Assets_Dependency_Abstract $dependency = null)
    {
        $replacements = array();

        if (!$dependency) {
            throw new Kwf_Exception("dependency is required for this filter");
        }

        if ($dependency instanceof Kwf_Assets_Dependency_File) {
            $prefix = Kwf_Config::getValue('application.uniquePrefix');
            if ($prefix) $prefix .= '-';
            else $prefix = '';
            $replacements['kwfLocal'] = $prefix.self::getLocalClassForDependency($dependency);
        }

        return array(
            'replacements' => $replacements
        );
    }

    public static function getLocalClassForDependency(Kwf_Assets_Dependency_File $dependency)
    {
        $ret = $dependency->getFileNameWithType();
        $ret = preg_replace('#^web/commonjs/views/#', '', $ret);
        $ret = preg_replace('#^web/reactjs/components/#', '', $ret);
        $ret = preg_replace('#^web/components/(.*?)backbone/views/#', '\1', $ret);
        $ret = preg_replace('#^web/components/(.*?)reactjs/components/#', '\1', $ret);
        $ret = preg_replace('#^web/#', '', $ret);
        $ret = preg_replace('#\.underscore\.tpl$#', '', $ret);
        $ret = preg_replace('#\.[a-z]+$#', '', $ret);
        $ret = preg_replace_callback('#/(.)#', function($m) {
            return strtoupper($m[1]);
        }, $ret);
        $ret = str_replace('/', '', $ret);
        return $ret;
    }

}
