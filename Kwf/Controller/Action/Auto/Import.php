<?php
abstract class Vps_Controller_Action_Auto_Import extends Vps_Controller_Action_Auto_Form
{
    protected $_buttons = array('save');
    protected $_permissions = array('add', 'save');

    public function indexAction()
    {
        parent::indexAction();
        $this->view->xtype = 'vps.import';
    }

    protected function _initFields()
    {
        $this->_form->setModel(new Vps_Model_FnF(array(
            'columns' => array('id', 'upload_id'),
            'primaryKey' => 'id',
            'referenceMap' => array(
                'import' => array(
                    'refModelClass' => 'Vps_Uploads_Model',
                    'column' => 'upload_id'
                )
            )
        )));

        $this->_form->add(new Vps_Form_Field_Static(trlVps('Upload a file and click "Save" to import.')));
        $this->_form->add(new Vps_Form_Field_File('import', 'Import'))
            ->setAllowBlank(false);
    }

    protected function _beforeSave(Vps_Model_Row_Interface $row)
    {
        $model = Vps_Model_Abstract::getInstance('Vps_Uploads_Model');
        $uploadsRow = $model->getRow($row->upload_id);
        if (!$uploadsRow) throw new Vps_Exception_Client(trlVps('File not found.'));

        $source = $uploadsRow->getFileSource();
        $excel = PHPExcel_IOFactory::load($source)->getActiveSheet();
        $message = $this->_import($excel);
        $this->view->message = null;
        if ($message) {
            if (is_string($message)) $this->view->message = nl2br($message);
            unlink($source);
        }
        $uploadsRow->delete();
    }

    protected abstract function _import($excel);
}