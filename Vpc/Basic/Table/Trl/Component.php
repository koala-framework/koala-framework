<?php
class Vpc_Basic_Table_Trl_Component extends Vpc_Chained_Trl_Component
{
    public static function getSettings($masterComponentClass)
    {
        $ret = parent::getSettings($masterComponentClass);
        $ret['childModel'] = 'Vpc_Basic_Table_Trl_DataModel';
        $ret['extConfig'] = 'Vpc_Basic_Table_Trl_ExtConfig';
        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $model = $this->getChildModel();
        $rows = $model->getRows($model->select()
            ->whereEquals('component_id', $this->getData()->componentId)
            ->whereEquals('visible', 1)
            ->order('pos')
        );
        $ret['dataRows'] = array();
        foreach ($rows as $row) {
            if ($row->visible) $ret['dataRows'][] = $row;
        }
        return $ret;
    }

    public function getChildModel()
    {
        $chained = $this->getData()->chained;
        $tableModelClass = Vpc_Abstract::getSetting($chained->componentClass, 'childModel');
        return new Vpc_Basic_Table_Trl_AdminModel(array(
            'proxyModel' => new $tableModelClass(),
            'trlModel' => new Vpc_Basic_Table_Trl_DataModel(array(
                'columnCount' => $chained->getComponent()->getColumnCount())
            )
        ));
    }

    public static function getStaticCacheMeta()
    {
        $ret = parent::getCacheMeta();
        $ret[] = new Vps_Component_Cache_Meta_Static_Model('Vpc_Basic_Table_Trl_DataModel');
        return $ret;
    }
}
