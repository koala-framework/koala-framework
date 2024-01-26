<?php
class Kwc_Basic_Table_ImportController extends Kwf_Controller_Action_Auto_Import
{
    protected function _import($excel)
    {
        $model = Kwf_Model_Abstract::getInstance('Kwc_Basic_Table_ModelData');

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
                $newRow->component_id = $this->_getParam('componentId');
                $newRow->css_style = null;
                $newRow->visible = 1;

                for ($c=0; $c<$importColumns; $c++) {
                    $colname = 'column'.($c+1);
                    $newRow->{$colname} = htmlentities($xlsRows[$r][$c]);
                }
                $newRow->save();
            }
        }
        return true;
    }
}
