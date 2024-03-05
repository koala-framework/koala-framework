<?php
/**
 * OptIn-Component when a website requires Cookie Opt In
 *
 * Shows a layer with cookie informations
 * Set the default value for opt-in for JavaScript
 * Depends on Kwc_Statistics_Opt_Component
 * Has to be set up as box with 'inherit' => true and 'unique' => true
 */
class Kwc_Statistics_OptBox_Component extends Kwc_Abstract_Composite_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['rootElementClass'] = 'kwfUp-webStandard';
        $ret['componentName'] = trlKwfStatic('Cookie-Opt Banner');
        $ret['generators']['child']['component']['linktag'] = 'Kwc_Basic_LinkTag_Component';
        $ret['extConfig'] = 'Kwf_Component_Abstract_ExtConfig_Form';
        $ret['ownModel'] = 'Kwf_Component_FieldModel';
        $ret['flags']['hasFooterIncludeCode'] = true;
        return $ret;
    }

    public function getIncludeCode()
    {
        return $this->getData();
    }

    public function getTemplateVars(Kwf_Component_Renderer_Abstract $renderer)
    {
        $ret = parent::getTemplateVars($renderer);
        $ret['showBanner'] = $this->_getRow()->show_banner;

        $ret['config'] = array(
            'showBanner' => $ret['showBanner']
        );

        $ret['headline'] = $this->_getRow()->headline;
        $ret['text'] = $this->_getRow()->text;
        $ret['moreText'] = $this->_getRow()->more_text;
        $ret['acceptText'] = $this->_getRow()->accept_text;
        $ret['kwfUp'] = Kwf_Config::getValue('application.uniquePrefix') ? Kwf_Config::getValue('application.uniquePrefix').'-' : '';
        return $ret;
    }
}
