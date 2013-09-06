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
        $ret['assets']['files'][] = 'kwf/Kwc/Statistics/OptBox/Component.js';
        $ret['cssClass'] = 'webStandard';
        $ret['flags']['hasHeaderIncludeCode'] = true;
        return $ret;
    }

    /**
     * Puts a JavaScript Variable used in Kwf_js/Statistics.js
     *
     * @return string
     */
    public function getIncludeCode()
    {
        if (Kwf_Statistics::getDefaultOptValue($this->getData()) == 'in') return;

        $ret = '<script type="text/javascript">';
        $ret .= $this->_getJavascriptIncludeCode();
        $ret .= '</script>';
        return $ret;
    }

    protected function _getJavascriptIncludeCode()
    {
        $value = Kwf_Statistics::getDefaultOptValue($this->getData());
        $components = Kwf_Component_Data_Root::getInstance()->getComponentsByClass(
            'Kwc_Statistics_Opt_Component', array('subroot' => $this->getData())
        );
        $url = isset($components[0]) ? $components[0]->url : '';
        $cssClass = self::getCssClass($this);
        $controllerUrl = Kwc_Admin::getInstance($this->getData()->componentClass)
            ->getControllerUrl();
        $reload = $this->_reloadOnOptChanged() ? 'true' : 'false';
        $ret  = "if (Kwf == 'undefined') Kwf = {};";
        $ret .= "if (Kwf.Statistics == 'undefined') Kwf.Statistics = {};";
        $ret .= "Kwf.Statistics.defaultOptValue = '$value';";
        $ret .= "Kwf.Statistics.optUrl = '$url';";
        $ret .= "Kwf.Statistics.cssClass = '$cssClass';";
        $ret .= "Kwf.Statistics.controllerUrl = '$controllerUrl';";
        $ret .= "Kwf.Statistics.reloadOnOptChanged = $reload;";
        return $ret;
    }

    protected function _reloadOnOptChanged()
    {
        return true;
    }
}

