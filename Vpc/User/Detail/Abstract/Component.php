<?php
abstract class Vpc_User_Detail_Abstract_Component extends Vpc_Abstract_Composite_Component
{
    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['row'] = $this->getData()->parent->row;
        return $ret;
    }

    public function getCacheVars()
    {
        $ret = parent::getCacheVars();
        $row = $this->getData()->parent->row;
        $model = $row->getModel();
        if ($model instanceof Vps_Model_Db) $model = $model->getTable();
        $ret[] = array(
            'model' => get_class($model),
            'id' => $row->id
        );
        return $ret;
    }
}
