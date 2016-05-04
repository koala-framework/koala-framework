<?php
class Kwc_Statistics_Opt_Component extends Kwc_Abstract_Composite_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['rootElementClass'] = 'kwfUp-webStandard';
        $ret['componentName'] = trlKwfStatic('Cookie Opt In / Opt Out');
        $ret['ownModel'] = 'Kwf_Component_FieldModel';
        return $ret;
    }

    public function getTemplateVars(Kwf_Component_Renderer_Abstract $renderer = null)
    {
        $ret = parent::getTemplateVars($renderer);
        $config = array();
        $config['textOptIn'] = $this->getRow()->text_opt_in;
        if (!$config['textOptIn']) {
            $config['textOptIn'] = $this->getData()->trl('Cookies are set when visiting this webpage. Click to deactivate cookies.');
        }
        $config['textOptOut'] = $this->getRow()->text_opt_out;
        if (!$config['textOptOut']) {
            $config['textOptOut'] = $this->getData()->trl('No cookies are set when visiting this webpage. Click to activate cookies.');
        }
        $config['textOptIn'] = nl2br($config['textOptIn']);
        $config['textOptOut'] = nl2br($config['textOptOut']);
        $ret['config'] = $config;
        return $ret;
    }
}
