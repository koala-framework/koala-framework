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
        $ret['cssClass'] = 'kwfup-webStandard';
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
        $value = Kwf_Statistics::getDefaultOptValue($this->getData());
        $optInShowBox = $this->getData()->getBaseProperty('statistics.optInShowBox');

        $html = '';
        if ($value == 'out' || ($value == 'in' && $optInShowBox)) {
            $components = Kwf_Component_Data_Root::getInstance()->getComponentsByClass(
                'Kwc_Statistics_Opt_Component', array('subroot' => $this->getData())
            );
            $url = isset($components[0]) ? $components[0]->url : null;
            $html = $this->_getOptBoxInnerHtml($url);
            if (!$html) {
                $exception = new Kwf_Exception('To disable optbox please change config.');
                $exception->logOrThrow();
            }
            $html = '<div class="' . self::getCssClass($this) . '"><div class="inner">' . $html . '<div></div>';
            $html = str_replace("'", "\'", $html);
        }

        $ret  = '<script type="text/javascript">';
        $ret .= "if (typeof Kwf == 'undefined') Kwf = {};";
        $ret .= "if (typeof Kwf.Statistics == 'undefined') Kwf.Statistics = {};";
        $ret .= "Kwf.Statistics.defaultOptValue = '$value';";
        $ret .= "Kwf.Statistics.optBoxHtml = '$html';";
        $ret .= $this->_getJavascriptIncludeCode();
        $ret .= '</script>';
        return $ret;
    }

    protected function _getJavascriptIncludeCode()
    {
        return '';
    }

    protected function _getOptBoxInnerHtml($optUrl = null)
    {
        $ret = $this->getData()->trlKwf('This website uses cookies to help us give you the best experience when you visit our website.');
        if ($optUrl) {
            $ret .= ' <a href="' . $optUrl . '" class="info">' . $this->getData()->trlKwf('More information about the use of cookies') . '</a>';
        }
        $ret .= '<a href="" class="accept"><span>' . $this->getData()->trlKwf('Accept and continue') . '</span></a>';
        return $ret;
    }
}

