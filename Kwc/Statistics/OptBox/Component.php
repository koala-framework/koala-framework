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
    public static function getSettings()
    {
        $ret = parent::getSettings();
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

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['showBanner'] = $this->_getRow()->show_banner;
        if ($ret['showBanner']) {
            $ret['config'] = array(
                'html' => $this->_getOptBoxInnerHtml()
            );
        }
        return $ret;
    }

    protected function _getOptBoxInnerHtml()
    {
        $ret = $this->_getRow()->text;
        if ($this->getData()->getChildComponent('-linktag')->hasContent()) {
            $ret .= ' ';
            $ret .= $this->getData()->getChildComponent('-linktag')->render();
            $ret .= $this->_getRow()->more_text;
            $ret .= '</a>';
        }
        $ret .= '<a href="#" class="accept"><span>' . $this->_getRow()->accept_text . '</span></a>';
        return $ret;
     }
}
