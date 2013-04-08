<?php
class Kwc_Basic_Table_Trl_Component extends Kwc_Chained_Trl_Component
{
    public static function getSettings($masterComponentClass)
    {
        $ret = parent::getSettings($masterComponentClass);
        $ret['childModel'] = 'Kwc_Basic_Table_Trl_DataModel';
        $ret['extConfig'] = 'Kwc_Basic_Table_Trl_ExtConfig';
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
            if ($row->visible) {
                $rowData = array();
                $rowData['cssStyle'] = $row->cssStyle;
                for ($i = 1; $i <= $ret['columnCount']; $i++) {
                    $rowData['column'.$i] = $row->getFrontend('column'.$i);
                }
                $ret['dataRows'][] = $rowData;
            }
        }
        return $ret;
    }

    public function getChildModel()
    {
        $chained = $this->getData()->chained;
        $tableModelClass = Kwc_Abstract::getSetting($chained->componentClass, 'childModel');
        $trlModelClass = $this->_getSetting('childModel');
        return new Kwc_Basic_Table_Trl_AdminModel(array(
            'proxyModel' => Kwf_Model_Abstract::getInstance($tableModelClass),
            'trlModel' => Kwf_Model_Abstract::getInstance($trlModelClass)
        ));
    }

    public static function getStaticCacheMeta($componentClass)
    {
        $ret = parent::getCacheMeta();
        $ret[] = new Kwf_Component_Cache_Meta_Static_Model('Kwc_Basic_Table_Trl_DataModel');
        return $ret;
    }
}
