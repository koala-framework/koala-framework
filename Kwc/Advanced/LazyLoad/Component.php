<?php
class Kwc_Advanced_LazyLoad_Component extends Kwc_Abstract
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['generators']['child'] = array(
            'class' => 'Kwf_Component_Generator_Static',
            'component' => null,
        );
        $ret['editComponents'] = array('child');
        return $ret;
    }

    public function getTemplateVars(Kwf_Component_Renderer_Abstract $renderer)
    {
        $ret = parent::getTemplateVars($renderer);
        $ret['loadId'] = $this->getData()->componentId.'-child';
        return $ret;
    }
}
