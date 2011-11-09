<?php
abstract class Kwf_Controller_Action_Auto_Import extends Kwf_Controller_Action_Auto_Form
{
    protected $_buttons = array('save');
    protected $_permissions = array('add', 'save');

    public function indexAction()
    {
        parent::indexAction();
        $this->view->xtype = 'kwf.import';
    }

    protected function _initFields()
    {
        $this->_form->setModel(new Kwf_Model_FnF(array(
            'columns' => array('id', 'upload_id'),
            'primaryKey' => 'id',
            'referenceMap' => array(
                'import' => array(
                    'refModelClass' => 'Kwf_Uploads_Model',
                    'column' => 'upload_id'
                )
            )
        )));

        $this->_form->add(new Kwf_Form_Field_Static(trlKwf('Upload a file and click "Save" to import.')));
        $this->_form->add(new Kwf_Form_Field_File('import', 'Import'))
            ->setAllowBlank(false);
    }

    protected function _beforeSave(Kwf_Model_Row_Interface $row)
    {
        $model = Kwf_Model_Abstract::getInstance('Kwf_Uploads_Model');
        $uploadsRow = $model->getRow($row->upload_id);
        if (!$uploadsRow) throw new Kwf_Exception_Client(trlKwf('File not found.'));

        $source = $uploadsRow->getFileSource();
        $target = 'application/temp/xlsimport_' . date('YmdHis') . '.' . $uploadsRow->extension;
        copy($source, $target); // copy with extension for xlsimport
        $excel = PHPExcel_IOFactory::load($target);
        if (!$excel) throw new Kwf_Exception_Client(trlKwf('Could not read excel'));
        $excel = $excel->getActiveSheet();
        $message = $this->_import($excel);
        $this->view->message = null;
        if ($message) {
            if (is_string($message)) $this->view->message = nl2br($message);
        }
        $uploadsRow->delete();
        unlink($target);
    }

    protected abstract function _import($excel);
}