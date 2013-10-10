<?php
class Kwc_Basic_LinkTag_Youtube_Component extends Kwc_Basic_LinkTag_Abstract_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = trlKwfStatic('Youtube in Lightbox');
        $ret['ownModel'] = 'Kwf_Component_FieldModel';
        $ret['dataClass'] = 'Kwc_Basic_LinkTag_Youtube_Data';
        $ret['generators']['video'] = array(
            'class' => 'Kwf_Component_Generator_Page_Static',
            'component' => 'Kwc_Basic_LinkTag_Youtube_Video_Component'
        );
        return $ret;
    }
}
