<?php
/**
 * OptIn-Component when a website requires Cookie Opt In
 *
 * Shows a layer with cookie informations
 * Set the default value for opt-in for JavaScript
 */
class Kwc_Statistics_OptBox_Component extends Kwc_Abstract_Composite_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['assets']['files'][] = 'kwf/Kwc/Statistics/OptBox/Component.js';
        $ret['cssClass'] = 'webStandard';
        $ret['flags']['hasHeaderIncludeCode'] = true;
        $ret['flags']['processInput'] = true;
        return $ret;
    }

    public function processInput($postData)
    {
        if (isset($postData['accept'])) {
            Kwf_Statistics::setUserOptValue(Kwf_Statistics::OPT_IN);
            header('Location: ' . $this->getData()->url);
            exit;
        }
    }

    /**
     * Puts a JavaScript Variable used in Kwf_js/Statistics.js
     *
     * @return string
     */
    public function getIncludeCode()
    {
        $value = Kwf_Statistics::getDefaultOptValue($this->getData());
        $ret = '<script type="text/javascript">';
        $ret .= "if (Kwf == 'undefined') Kwf = {};";
        $ret .= "if (Kwf.Statistics == 'undefined') Kwf.Statistics = {};";
        $ret .= "Kwf.Statistics.defaultOptValue = '$value';";
        $ret .= '</script>';
        return $ret;
    }

    public function getTemplateVars($renderer)
    {
        $ret = parent::getTemplateVars();
        $ret['optComponent'] = Kwf_Component_Data_Root::getInstance()->getComponentByClass(
            'Kwc_Statistics_Opt_Component', array('subroot' => $this->getData(), 'limit' => 1)
        );
        return $ret;
    }
}

