<?php
class Kwc_Legacy_Columns_Update_20150309Legacy37315 extends Kwf_Update
{
    public function update()
    {
        // daten von kwc_columns in kwc_composite_list kopieren

        $columnsModel = new Kwf_Model_Db_Proxy(array('table' => 'kwc_columns'));
        $listModel = new Kwf_Model_Db_Proxy(array(
            'table' => 'kwc_composite_list',
            'siblingModels' => array(new Kwf_Model_Field(array('fieldName'=>'data')))
        ));

        foreach ($columnsModel->getRows() as $colRow) {
            $listRow = $listModel->createRow();
            $listRow->component_id = $colRow->component_id;
            $listRow->pos = $colRow->pos;
            $listRow->visible = 1;
            $listRow->width = $colRow->width;
            $listRow->save();

            // ersetzungen für master
            $action = new Kwf_Update_Action_Component_ConvertComponentIds(array(
                'search' => $colRow->component_id.'-'.$colRow->id,
                'replace' => $listRow->component_id.'-'.$listRow->id
            ));
            $action->checkSettings();
            $action->update();

            // ersetzungen für trl
            $action = new Kwf_Update_Action_Component_ConvertComponentIds(array(
                'search' => $colRow->component_id.'-'.$colRow->id,
                'replace' => $listRow->component_id.'-'.$listRow->id,
                'pattern' => 'root-%\_'.$colRow->component_id.'-'.$colRow->id.'%'
            ));
            $action->checkSettings();
            $action->update();
        }
    }
}
