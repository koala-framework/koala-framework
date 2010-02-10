<?php
class Vpc_Paragraphs_Trl_Component extends Vpc_Chained_Trl_Component
{

    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = trlVps('Paragraphs');
        $ret['componentIcon'] = new Vps_Asset('page');
        $ret['generators']['chained']['class'] = 'Vpc_Paragraphs_Trl_Generator';
        $ret['previewWidth'] = 600;
        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['childModel'] = 'Vpc_Paragraphs_Linked_Model';
        $ret['paragraphs'] = $this->getData()
            ->getChildComponents(/*array('generator'=>'chained')*/);
        return $ret;
    }
}
