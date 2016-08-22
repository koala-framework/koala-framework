<?php
class Kwc_Basic_Table_Trl_Component extends Kwc_Chained_Trl_Component
{
    public static function getSettings($masterComponentClass = null)
    {
        $ret = parent::getSettings($masterComponentClass);
        $ret['childModel'] = 'Kwc_Basic_Table_Trl_DataModel';
        $ret['extConfig'] = 'Kwc_Basic_Table_Trl_ExtConfig';
        return $ret;
    }

    public function getTemplateVars(Kwf_Component_Renderer_Abstract $renderer)
    {
        $ret = parent::getTemplateVars($renderer);
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
                $rowData['cssStyle'] = $row->getFrontendValue('css_style');
                for ($i = 1; $i <= $ret['columnCount']; $i++) {
                    $rowData['data']['column'.$i] = array('value'=>$row->getFrontendValue('column'.$i), 'cssClass'=>'');
                }
                $ret['dataRows'][] = $rowData;
            }
        }
        $rowStyles = $this->getSetting($this->getData()->chained->componentClass, 'rowStyles');
        $ret['dataRows'] = Kwc_Basic_Table_Component::addDefaultCssClasses($ret['dataRows'], $rowStyles);
        $ret['headerRows'] = array();
        if (isset($ret['dataRows'][0]['htmlTag']) && $ret['dataRows'][0]['htmlTag'] == 'th') {
            $ret['headerRows'] = array(array_shift($ret['dataRows']));
        }
        return $ret;
    }

    public function getChildModel()
    {
        $chained = $this->getData()->chained;
        $tableModelClass = Kwc_Abstract::getSetting($chained->componentClass, 'childModel');
        $trlModelClass = $this->_getSetting('childModel');
        return new Kwc_Basic_Table_Trl_Model(array(
            'proxyModel' => Kwf_Model_Abstract::getInstance($tableModelClass),
            'trlModel' => Kwf_Model_Abstract::getInstance($trlModelClass)
        ));
    }
}
