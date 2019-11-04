<?php
/**
 * @package Kwc
 * @subpackage Basic
 */
class Kwc_Basic_LinkTag_Component extends Kwc_Abstract_Cards_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['dataClass'] = 'Kwc_Basic_LinkTag_Data';
        $ret['componentName'] = trlKwfStatic('Link');
        $ret['componentIcon'] = 'page_link';
        $ret['default']['component'] = 'intern';
        $ret['extConfig'] = 'Kwf_Component_Abstract_ExtConfig_None';
        $ret['generators']['child']['component'] = array(
            'intern'   => 'Kwc_Basic_LinkTag_Intern_Component',
            'extern'   => 'Kwc_Basic_LinkTag_Extern_Component',
            'mail'     => 'Kwc_Basic_LinkTag_Mail_Component',
            'phone'    => 'Kwc_Basic_LinkTag_Phone_Component',
            'download' => 'Kwc_Basic_DownloadTag_Component'
        );
        $ret['assetsAdmin']['files'][] = 'kwf/Kwc/Basic/LinkTag/ComboBox.js';
        $ret['flags']['noIndex'] = true; //don't include in sitemap.xml
        $cc = Kwf_Registry::get('config')->kwc->childComponents;
        if (isset($cc->Kwc_Basic_LinkTag_Component)) {
            $ret['generators']['child']['component'] = array_merge(
                $ret['generators']['child']['component'],
                $cc->Kwc_Basic_LinkTag_Component->toArray()
            );
        }
        return $ret;
    }

    public static function validateSettings($settings, $componentClass)
    {
        parent::validateSettings($settings, $componentClass);
        if (isset($settings['generators']['link'])) {
            throw new Kwf_Exception("\$ret['generators']['link'] is deprecated, use \$ret['generators']['child']");
        }
    }

    public function getTemplateVars(Kwf_Component_Renderer_Abstract $renderer)
    {
        $ret = parent::getTemplateVars($renderer);
        $ret['linkTag'] = $ret['child'];
        return $ret;
    }

    public function getLinkTitle()
    {
        return $this->_getRow()->title_text;
    }
}
