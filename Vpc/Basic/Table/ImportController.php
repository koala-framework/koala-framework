<?php
class Vpc_Basic_Table_ImportController extends Vps_Controller_Action_Auto_Import
{
    protected function _import($excel)
    {
        $settingsRow = Vps_Model_Abstract::getInstance('Vpc_Basic_Table_Model')
            ->getRow($this->_getParam('componentId'));
        $model = Vps_Model_Abstract::getInstance('Vpc_Basic_Table_ModelData');

        $xlsRows = $excel->toArray();

        $importColumns = 0;
        $importRows = null;

        // zu importierende rows und cols ermitteln
        $tmpRows = 0;
        foreach ($xlsRows as $xlsRow) {
            $tmpCols = 0;
            foreach ($xlsRow as $xlsCol) {
                $tmpCols++;
                if (!empty($xlsCol)) {
                    $importRows = $tmpRows;
                    if ($importColumns < $tmpCols) {
                        $importColumns = $tmpCols;
                    }
                }
            }
            $tmpRows++;
        }

        if (!is_null($importRows)) {
            // durchlaufen und importieren
            for ($r=0; $r<=$importRows; $r++) {
                $newRow = $model->createRow();
                $newRow->component_id = $settingsRow->component_id;
                $newRow->css_style = null;
                $newRow->visible = 1;

                for ($c=0; $c<$importColumns; $c++) {
                    $colname = 'column'.($c+1);
                    $newRow->{$colname} = $xlsRows[$r][$c];
                }
                $newRow->save();
            }
        }
        return true;
    }
}