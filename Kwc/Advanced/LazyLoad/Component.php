<?php
class Kwc_Advanced_LazyLoad_Component extends Kwc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['child'] = array(
            'class' => 'Kwf_Component_Generator_Static',
            'component' => null,
        );
        $ret['editComponents'] = array('child');
        return $ret;
    }

    public function getTemplateVars(Kwf_Component_Renderer_Abstract $renderer = null)
    {
        $ret = parent::getTemplateVars($renderer);
        $ret['loadId'] = $this->getData()->componentId.'-child';
        return $ret;
    }
}
