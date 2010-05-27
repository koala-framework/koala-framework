<?php
class Vpc_Basic_Table_Trl_Component extends Vpc_Chained_Trl_Component
{
    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $model = $this->getData()->chained->getComponent()->getChildModel();
        $dataSelect = new Vps_Model_Select();
        $dataSelect->order('pos', 'ASC');
        $dataSelect->whereEquals('component_id', $this->getData()->componentId);
        $ret['dataRows'] = $model->getRows($dataSelect);
        return $ret;
    }

    public function getCacheVars()
    {
        $ret = parent::getCacheVars();
        $ret['tableData']['componentId'] = $this->getData()->componentId;
        return $ret;
    }
}
