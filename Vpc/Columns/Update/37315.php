<?php
class Vpc_Columns_Update_37315 extends Vps_Update
{
    public function update()
    {
        // daten von vpc_columns in vpc_composite_list kopieren

        $columnsModel = new Vps_Model_Db_Proxy(array('table' => 'vpc_columns'));
        $listModel = new Vps_Model_Db_Proxy(array(
            'table' => 'vpc_composite_list',
            'siblingModels' => array(new Vps_Model_Field(array('fieldName'=>'data')))
        ));

        foreach ($columnsModel->getRows() as $colRow) {
            $listRow = $listModel->createRow();
            $listRow->component_id = $colRow->component_id;
            $listRow->pos = $colRow->pos;
            $listRow->visible = 1;
            $listRow->width = $colRow->width;
            $listRow->save();

            // ersetzungen für master
            $action = new Vps_Update_Action_Component_ConvertComponentIds(array(
                'search' => $colRow->component_id.'-'.$colRow->id,
                'replace' => $listRow->component_id.'-'.$listRow->id
            ));
            $action->checkSettings();
            $action->update();

            // ersetzungen für trl
            $action = new Vps_Update_Action_Component_ConvertComponentIds(array(
                'search' => $colRow->component_id.'-'.$colRow->id,
                'replace' => $listRow->component_id.'-'.$listRow->id,
                'pattern' => 'root-%\_'.$colRow->component_id.'-'.$colRow->id.'%'
            ));
            $action->checkSettings();
            $action->update();
        }
    }
}
