<?php
class Kwc_Root_TrlRoot_Master_FlagImage_Trl_Component extends Kwc_Abstract_Composite_Trl_Component
{
    public static function getSettings($masterComponentClass = null)
    {
        $ret = parent::getSettings($masterComponentClass);
        $ret['generators']['image'] = array(
            'class' => 'Kwf_Component_Generator_Static',
            'component' => $masterComponentClass,
        );
        return $ret;
    }

    public function getTemplateVars(Kwf_Component_Renderer_Abstract $renderer)
    {
        $ret = parent::getTemplateVars($renderer);
        $ret['image'] = $this->getData()->getChildComponent('-image');
        return $ret;
    }

    public function hasContent()
    {
        $ret = parent::hasContent();
        if ($ret) return $ret;

        return $this->getData()->getChildComponent('-image')->hasContent();
    }
}
