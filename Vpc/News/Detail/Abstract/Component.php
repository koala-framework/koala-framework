<?php
abstract class Vpc_News_Detail_Abstract_Component extends Vpc_Abstract_Composite_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['content'] = array(
            'class' => 'Vps_Component_Generator_Static',
            'component' => 'Vpc_News_Detail_Paragraphs_Component'
        );
        return $ret;
    }

    public function getTemplateVars()
    {
        $return = parent::getTemplateVars();
        //todo: 404 wenn news abgelaufen
        return $return;
    }

    public function getNewsComponent()
    {
        return $this->getData()->findParentComponent();
    }
}
