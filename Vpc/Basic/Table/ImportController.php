<?php
class Vpc_Basic_Table_ImportController extends Vps_Controller_Action_Auto_Vpc_Form
{
    protected $_permissions = array('add', 'save');

    protected function _initFields()
    {
        $this->_form->setModel(new Vps_Model_FnF(array(
            'columns' => array('id', 'upload_id', 'adjust_columns'),
            'primaryKey' => 'id',
            'referenceMap' => array(
                'import' => array(
                    'refModelClass' => 'Vps_Uploads_Model',
                    'column' => 'upload_id'
                )
            )
        )));

        $this->_form->add(new Vps_Form_Field_Static(trlVps('Upload a file and click "Save" to import.')));
        $this->_form->add(new Vps_Form_Field_File('import', 'Import'));
        $this->_form->add(new Vps_Form_Field_Checkbox('adjust_columns', trlVps('Adjust columns')))
            ->setBoxLabel(trlVps('Add columns if necessary'))
            ->setDefaultValue(1);
    }

    protected function _beforeSave(Vps_Model_Row_Interface $row)
    {
        $model = Vps_Model_Abstract::getInstance('Vps_Uploads_Model');
        $ulRow = $model->getRow($row->upload_id);

        $import = $this->_import($ulRow, $row);

        unlink($ulRow->getFileSource());
        $ulRow->delete();
    }

    private function _import($fileRow, $formRow)
    {
        if ($fileRow->extension != 'xls') {
            throw new Vps_Exception_Client(trlVps('Uploaded file is not of type "xls".'));
        }
        $xls = PHPExcel_IOFactory::load($fileRow->getFileSource())->getActiveSheet();

        $settingsRow = Vps_Model_Abstract::getInstance('Vpc_Basic_Table_Model')
            ->getRow($this->_getParam('componentId'));
        $model = Vps_Model_Abstract::getInstance('Vpc_Basic_Table_ModelData');

        $xlsRows = $xls->toArray();

        $importColumns = 0;
        $importRows = 0;

        // zu importierende rows und cols ermitteln
        $tmpRows = 0;
        foreach ($xlsRows as $xlsRow) {
            $tmpRows++;
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
        }

        if ($formRow->adjust_columns && $settingsRow->columns < $importColumns) {
            $settingsRow->columns = $importColumns;
            $settingsRow->save();
        }

        // durchlaufen und importieren
        // rows fangen bei 1 zu zählen an, cols bei 0
        for ($r=1; $r<=$importRows; $r++) {
            $newRow = $model->createRow();
            $newRow->component_id = $settingsRow->component_id;
            $newRow->css_style = null;

            for ($c=0; $c<$importColumns; $c++) {
                $colname = 'column'.($c+1);
                $newRow->{$colname} = $xlsRows[$r][$c];
            }
            $newRow->save();
        }
    }
}