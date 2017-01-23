<?php
class Kwf_Assets_ProviderList_Default extends Kwf_Assets_ProviderList_Abstract
{
    protected $_pathTypesCacheId = 'assets-file-paths';

    public static function getInstance()
    {
        return Kwf_Assets_Package_Default::getDefaultProviderList();
    }

    public function __construct()
    {
        $providers = array();
        if (Kwf_Component_Data_Root::getComponentClass()) {
            $providers[] = new Kwf_Assets_Components_Provider(Kwf_Component_Data_Root::getComponentClass());
        } else {
            $providers[] = new Kwf_Assets_Provider_NoComponents();
        }
        if (file_exists('dependencies.ini')) {
            $providers[] = new Kwf_Assets_Provider_Ini('dependencies.ini');
        }
        $providers = array_merge($providers, self::getVendorProviders());
        $providers[] = new Kwf_Assets_Provider_IniNoFiles();
        $providers[] = new Kwf_Assets_Provider_Dynamic();
        $providers[] = new Kwf_Assets_TinyMce_Provider();
        $providers[] = new Kwf_Assets_Provider_JsClassKwf();
        $providers[] = new Kwf_Assets_Provider_JsClass('./ext', 'web/ext', 'App');
        $providers[] = new Kwf_Assets_Provider_CssByJs(array('web/commonjs', 'kwf/commonjs'));
        $providers[] = new Kwf_Assets_Provider_ExtTrl();
        $providers[] = new Kwf_Assets_Provider_ErrorHandler();
        $providers[] = new Kwf_Assets_Provider_AtRequires();
        $providers[] = new Kwf_Assets_Provider_ViewsUser();
        $providers[] = new Kwf_Assets_Modernizr_Provider();
        $providers[] = new Kwf_Assets_CommonJs_Provider();
        $providers[] = new Kwf_Assets_Provider_KwfCommonJs();
        $providers[] = new Kwf_Assets_CommonJs_JQueryPluginProvider();
        $providers[] = new Kwf_Assets_ResponsiveEl_Provider();
        $providers[] = new Kwf_Assets_CommonJs_Underscore_TemplateProvider();

        $filters = array();
        $filters[] = new Kwf_Assets_Filter_Css_MultiplePostCss(array(
            new Kwf_Assets_Filter_Css_Autoprefixer(),
            //new Kwf_Assets_Filter_Css_PrefixerKeyframes(),
            //new Kwf_Assets_Filter_Css_PrefixerFontface(),
            //new Kwf_Assets_Filter_Css_MediaQueriesDropRedundant(),
            new Kwf_Assets_Filter_Css_KwfLocal(),
            new Kwf_Assets_Filter_Css_UniquePrefix(),
        ));

        parent::__construct($providers, $filters);
    }
}
