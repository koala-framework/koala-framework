<?php
class Kwc_Form_ShowText_Component extends Kwc_Form_Field_Abstract
{
    public static function getSettings($param = null)
    {
        return array_merge(parent::getSettings($param), array(
            'default' => array(
                'value' => ''
            )
        ));
    }

    public function getTemplateVars(Kwf_Component_Renderer_Abstract $renderer)
    {
        $return = parent::getTemplateVars($renderer);
        $return['value'] = $this->_getRow()->value;
        return $return;
    }
}
