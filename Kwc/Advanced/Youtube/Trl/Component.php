<?php
class Kwc_Advanced_Youtube_Trl_Component extends Kwc_Chained_Trl_Component
{
    public static function getSettings($masterComponentClass)
    {
        $ret = parent::getSettings($masterComponentClass);
        $ret['ownModel'] = 'Kwf_Component_FieldModel';
        $ret['extConfig'] = 'Kwf_Component_Abstract_ExtConfig_Form';
        $ret['throwContentChangedOnOwnMasterModelUpdate'] = true;
        $ret['throwHasContentChangedOnMasterRowColumnsUpdate'] = array('url');
        return $ret;
    }

    public function getTemplateVars(Kwf_Component_Renderer_Abstract $renderer)
    {
        $ret = parent::getTemplateVars($renderer);

        if ($this->getRow()->own_url) {
            if (preg_match(Kwc_Advanced_Youtube_Component::REGEX, $this->getRow()->url, $matches)) {
                $ret['config']['videoId'] = $matches[0];
            }
        }

        return $ret;
    }
}
