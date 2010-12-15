<?php
class Vpc_Directories_Item_Detail_Component extends Vpc_Abstract_Composite_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['hasModifyItemData'] = true;
        return $ret;
    }
    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['row'] = $this->getData()->row;
        $ret['item'] = $this->getData();
        $this->getData()->parent->getComponent()->callModifyItemData($ret['item']);
        return $ret;
    }


    public static function modifyItemData(Vps_Component_Data $item)
    {
    }

    public function getCacheVars()
    {
        $ret = parent::getCacheVars();
        if (isset($this->getData()->row)) {
            $row = $this->getData()->row;
            $model = $row->getModel();
            $primaryKey = $model->getPrimaryKey();
            $ret[] = array(
                'model' => $model,
                'id' => $row->$primaryKey
            );
        }
        return $ret;
    }
}
