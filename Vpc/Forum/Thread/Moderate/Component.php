<?php
class Vpc_Forum_Thread_Moderate_Component extends Vpc_Abstract_Composite_Component 
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['child']['component']['close'] = 'Vpc_Forum_Thread_Moderate_Close_Component';
        $ret['generators']['move'] = array(
            'class' => 'Vps_Component_Generator_Page_Static',
            'component' => 'Vpc_Forum_Thread_Moderate_Move_Component',
            'name' => trlVps('Move Thread'),
            'filename' => 'move'
        );
        $ret['viewCache'] = false;
        $ret['cssClass'] = 'webStandard webForm';
        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['move'] = $this->getData()->getChildComponent('_move');
        $ret['mayModerate'] = $this->getData()->getParentPage()->getComponent()->mayModerate();
        return $ret;
    }
}
