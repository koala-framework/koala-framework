<?php
abstract class Kwf_Controller_Action_Auto_Form extends Kwf_Controller_Action_Auto_Abstract
{
    /**
     * @var Kwf_Form
     */
    protected $_form;
    protected $_fields = array(); //deprecated
    protected $_buttons = array();
    protected $_progressBar = null;

    protected $_formName;

    public function indexAction()
    {
        if ($this->_form->getProperties()) {
            $this->view->assign($this->_form->getProperties());
        }
        $this->view->controllerUrl = $this->getRequest()->getPathInfo();
        $this->view->xtype = 'kwf.autoform';
    }

    protected function _initFields()
    {
    }

    public function preDispatch()
    {
        parent::preDispatch();

        if (!isset($this->_form)) {
            if (isset($this->_formName)) {
                $this->_form = new $this->_formName();
            } else {
                $this->_form = new Kwf_Form();
            }
        }

        foreach ($this->_fields as $k=>$field) {
            if (!isset($field['type'])) throw new Kwf_Exception("no type for field no $k specified");
            $cls = 'Kwf_Form_Field_'.$field['type'];
            if (!class_exists($cls)) throw new Kwf_Exception("Invalid type: Form-Field-Class $cls does not exist.");
            $fieldObject = new $cls();
            unset($field['type']);
            foreach ($field as $propName => $propValue) {
                $fieldObject->setProperty($propName, $propValue);
            }
            $this->_form->fields[] = $fieldObject;
        }
        if (!$this->_form->getModel()) {
            if (isset($this->_table)) {
                $this->_form->setTable($this->_table);
            } else if (isset($this->_tableName)) {
                $this->_form->setTable(new $this->_tableName);
            } else if (isset($this->_modelName)) {
                $this->_form->setModel(new $this->_modelName);
            } else if (isset($this->_model)) {
                if (is_string($this->_model)) {
                    $this->_form->setModel(new $this->_model);
                } else {
                    $this->_form->setModel($this->_model);
                }
            }
        }

        $this->_initFields();
        $this->_form->initFields();
        $this->_form->trlStaticExecute();

        if (!$this->_form->fields->first() instanceof Kwf_Form_Container_Tabs) {
            $this->_form->setBodyStyle('padding: 10px;');
        }

        if (!$this->_form->getId()) {
            if (is_array($this->_form->getPrimaryKey())) {
                foreach ($this->_form->getPrimaryKey() as $key) {
                    $id[$key] = $this->_getParam($key);
                }
                $this->_form->setId($id);
            } else {
                $this->_form->setId($this->_getParam($this->_form->getPrimaryKey()));
            }
        }
    }

    public function jsonLoadAction()
    {
        if ($this->_form->getId()) { //nur laden wennn einen id über get daherkommt
            $row = $this->_form->getRow();

            if (!$this->_hasPermissions($row, 'load')) {
                throw new Kwf_Exception('You don\'t have the permission for this entry.');
            }
            $this->_beforeLoad($row);
            $this->view->data = $this->_form->load(null);
        }

        if ($this->getRequest()->getParam('meta')) {
            $this->_appendMetaData();
        }
    }

    protected function _appendMetaData()
    {
        $this->view->meta = array();
        $this->view->meta['helpText'] = $this->getHelpText();
        $this->view->meta['form'] = $this->_form->getMetaData();
        $this->view->meta['buttons'] = (object)$this->_buttons; //in objekt casten damit json kein [] sondern {} ausgibt
        $this->view->meta['permissions'] = (object)$this->_permissions; //in objekt casten damit json kein [] sondern {} ausgibt
    }

    public function jsonSaveAction()
    {
        ignore_user_abort(true);
        $db = Zend_Registry::get('db');
        if ($db) $db->beginTransaction();

        // zuvor war statt diesem kommentar das $row = $this->_form->getRow();
        // drin und wurde bei processInput und validate übergeben, aber die form
        // weiß selbst das model, deshalb passt NULL
        // Runtergeschoben wurde das $this->_form->getRow() weil bei der Kwf_User_Form
        // die row im processInput gefaket wird, da hier ->createUserRow() aufgerufen
        // wird anstatt ->createRow() und diese dann im _form->getRow() zurück kommt

        $postData = $this->_form->processInput(null, $this->getRequest()->getParams());
        $this->_beforeValidate($postData);
        $invalid = $this->_form->validate(null, $postData);
        if ($invalid) {
            $invalid = Kwf_Form::formatValidationErrors($invalid);
            throw new Kwf_ClientException(implode("<br />", $invalid));
        }

        $data = $this->_form->prepareSave(null, $postData);

        $row = $this->_form->getRow();

        $insert = false;

        $primaryKey = $this->_form->getPrimaryKey();
        $skip = false;
        if ($row && $primaryKey) {
            if (is_array($primaryKey)) $primaryKey = $primaryKey[1];

            if (!$row->$primaryKey){
                $insert = true;
            }

            if ($insert) {
                $sessionFormId = new Zend_Session_Namespace('avoid_reinsert_id');

                if ($this->_getParam('avoid_reinsert_id') &&
                    isset($sessionFormId->avoid[$this->_getParam('avoid_reinsert_id')])
                ) {
                    $skip = true;
                }
                if (!isset($this->_permissions['add']) || !$this->_permissions['add']) {
                    throw new Kwf_Exception('Add is not allowed.');
                }
                if (!$skip) $this->_beforeInsert($row);
            } else {
                if (!isset($this->_permissions['save']) || !$this->_permissions['save']) {
                    throw new Kwf_Exception('Save is not allowed.');
                }
            }

            if (!$skip) $this->_beforeSave($row);
        }
        if (!$skip) {
            //erst hier unten Berechtigungen überprüfen, damit beforeInsert usw vorher noch ausgeführt
            //wird und eventuelle Daten gesetzt werden
            if (!$this->_hasPermissions($row, 'save')) {
                throw new Kwf_Exception("Save is not allowed for this row.");
            }
            $data = $this->_form->save(null, $postData);

            $this->_form->afterSave(null, $postData);
            if ($row) {
                if ($insert) {
                    $this->_afterInsert($row);
                }
                $this->_afterSave($row);
            }
            if ($db) $db->commit();

            $this->view->data = $data;

            $sessionFormId = new Zend_Session_Namespace('avoid_reinsert_id');
            if (!isset($sessionFormId->avoid)) {
                $avoid = array();
            } else {
                $avoid = $sessionFormId->avoid;
            }
            $avoid[$this->_getParam('avoid_reinsert_id')] = $data;
            $sessionFormId->avoid = $avoid;
        } else {
            $this->view->data = $sessionFormId->avoid[$this->_getParam('avoid_reinsert_id')];
        }

    }

    public function jsonDeleteAction()
    {
        if (!isset($this->_permissions['delete']) || !$this->_permissions['delete']) {
            throw new Kwf_Exception('Delete is not allowed.');
        }
        $row = $this->_form->getRow();
        if (!$this->_hasPermissions($row, 'delete')) {
            throw new Kwf_Exception("Delete is not allowed for this row.");
        }
        $db = Zend_Registry::get('db');
        if ($db) $db->beginTransaction();
        $this->_form->delete(null);
        if ($db) $db->commit();
    }

    protected function _beforeSave(Kwf_Model_Row_Interface $row)
    {
    }

    protected function _afterSave(Kwf_Model_Row_Interface $row)
    {
    }

    protected function _beforeInsert(Kwf_Model_Row_Interface $row)
    {
    }

    protected function _afterInsert(Kwf_Model_Row_Interface $row)
    {
    }

    protected function _beforeLoad(Kwf_Model_Row_Interface $row)
    {
    }

    protected function _beforeValidate(array $postData)
    {
    }

    protected function _hasPermissions($row, $action)
    {
        return true;
    }
    
    protected function _fillTheXlsFile($xls, $firstSheet)
    {
        // Should be implemented by inherited form class
    }
    
    protected function _getColumnLetterByIndex($idx)
    {
        $letters = array('A','B','C','D','E','F','G','H','I','J','K','L','M',
                         'N','O','P','Q','R','S','T','U','V','W','X','Y','Z');
        $maxLetterIndex = count($letters) - 1;
        if ($idx > $maxLetterIndex) {
            return $letters[floor(($idx) / count($letters))-1].$letters[($idx) % count($letters)];
        } else {
            return $letters[$idx];
        }
    }
    
    public function jsonXlsAction()
    {
        if (!isset($this->_permissions['xls']) || !$this->_permissions['xls']) {
            throw new Kwf_Exception("XLS is not allowed.");
        }
        
        ini_set('memory_limit', "768M");
        set_time_limit(600); // 10 minuten
            
        $row = $this->_form->getRow();
        $primaryKey = $this->_form->getPrimaryKey();
        
        require_once Kwf_Config::getValue('externLibraryPath.phpexcel').'/PHPExcel.php';
        $xls = new PHPExcel();
        $xls->getProperties()->setCreator(Kwf_Config::getValue('application.name'));
        $xls->getProperties()->setLastModifiedBy(Kwf_Config::getValue('application.name'));
        $xls->getProperties()->setTitle("KWF Excel Export");
        $xls->getProperties()->setSubject("KWF Excel Export");
        $xls->getProperties()->setDescription("KWF Excel Export");
        $xls->getProperties()->setKeywords("KWF Excel Export");
        $xls->getProperties()->setCategory("KWF Excel Export");
        
        $xls->setActiveSheetIndex(0);
        $sheet = $xls->getActiveSheet();
        
        $this->_progressBar = new Zend_ProgressBar(
            new Kwf_Util_ProgressBar_Adapter_Cache($this->_getParam('progressNum')),
                                                   0, count($this->_fields));
        
        if ($row && $primaryKey) {
            $this->_fillTheXlsFile($xls, $sheet);
        }
        
        // write the file
        $objWriter = PHPExcel_IOFactory::createWriter($xls, 'Excel5');
        $downloadkey = uniqid();
        $objWriter->save('temp/'.$downloadkey.'.xls');
        
        $this->_progressBar->finish();
        
        $this->view->downloadkey = $downloadkey;
    }
    
    public function downloadXlsFileAction()
    {
        if (!isset($this->_permissions['xls']) || !$this->_permissions['xls']) {
            throw new Kwf_Exception("XLS is not allowed.");
        }
        if (!file_exists('temp/'.$this->_getParam('downloadkey').'.xls')) {
            throw new Kwf_Exception('Wrong downloadkey submitted');
        }
        Kwf_Util_TempCleaner::clean();
        
        $file = array(
                      'contents' => file_get_contents('temp/'.$this->_getParam('downloadkey').'.xls'),
                      'mimeType' => 'application/octet-stream',
                      'downloadFilename' => 'form_'.date('Ymd-Hi').'.xls'
                      );
        Kwf_Media_Output::output($file);
        $this->_helper->viewRenderer->setNoRender();
    }    
}
