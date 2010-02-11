<?php
class Vpc_Paragraphs_Trl_Component extends Vpc_Chained_Trl_Component
{

    public static function getSettings($masterComponentClass)
    {
        $ret = parent::getSettings($masterComponentClass);
        $ret['componentIcon'] = new Vps_Asset('page');
        $ret['generators']['paragraphs']['class'] = 'Vpc_Paragraphs_Trl_Generator';
        $ret['childModel'] = 'Vpc_Paragraphs_Trl_Model';
        $ret['previewWidth'] = 600;
        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['paragraphs'] = $this->getData()
            ->getChildComponents(array('generator'=>'paragraphs'));
        return $ret;
    }
}
