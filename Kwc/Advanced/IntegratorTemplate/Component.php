<?php
class Kwc_Advanced_IntegratorTemplate_Component extends Kwc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = trlKwfStatic('Integrator Template');
        $ret['dataClass'] = 'Kwc_Advanced_IntegratorTemplate_Data';
        $ret['ownModel'] = 'Kwf_Component_FieldModel';
        $ret['extConfig'] = 'Kwc_Advanced_IntegratorTemplate_ExtConfig';
        $ret['generators']['embed'] = array(
            'class' => 'Kwf_Component_Generator_Page_Static',
            'component' => 'Kwc_Advanced_IntegratorTemplate_Embed_Component',
            'name' => '',
            'filename' => 'embed'
        );
        $ret['extConfig'] = 'Kwf_Component_Abstract_ExtConfig_Form';
        $ret['flags']['noIndex'] = true;
        $ret['editComponents'] = array('embed');
        return $ret;
    }
}
