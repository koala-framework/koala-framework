<?php
class Kwc_Trl_Simple_Test_Component extends Kwc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['test2'] = array(
            'class' => 'Kwf_Component_Generator_Page_Static',
            'component' => 'Kwc_Trl_Simple_Test_Test2_Component',
            'name' => 'test2'
        );
        return $ret;
    }

    public function getTemplateVars(Kwf_Component_Renderer_Abstract $renderer = null)
    {
        $ret = parent::getTemplateVars($renderer);
        $ret['componentClass'] = get_class($this);
        $ret['test2'] = $this->getData()->getChildComponent('_test2');
        return $ret;
    }

    public function getFoo()
    {
        return 'foo';
    }
}
