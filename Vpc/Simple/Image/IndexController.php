<?php
class Vpc_Simple_Image_IndexController extends Vps_Controller_Action_Auto_Form_Vpc
{
	protected $_fields = array (
		array (
			'type' => 'TextField',
			'inputType' => 'file',
			'fieldLabel' => 'Datei',
			'name' => 'file',
			'width' => 150
		),
		array (
			'type' => 'TextField',
			'fieldLabel' => 'Bezeichnung',
			'name' => 'text',
			'width' => 150
		),
	);

	protected $_buttons = array (
		'save' => true
	);
	protected $_tableName = 'Vpc_Simple_Image_FileDataModel';
	protected $_primaryKey = 'id';
	private $_fileSize = '';
	private $_filename = false;

	public function indexAction()
	{
		$cfg['dialogControllerUrl'] = '/component/edit/Vpc_Simple_Image_Index/' . $this->component->getId() . '/FileData/';
		$cfg['fileUpload'] = true;
		$this->view->ext('Vps.Auto.Form', $cfg);
	}

	public function init()
	{
		$components = new Vps_Config_Ini('application/components.ini');
		$component = $components->Vpc_Simple_Image_Index;
		$this->_fileSize = $component->fileSize;

		//Einstellungen für die Veränderbarkeit der Höhe und Breite
		if ($component->fileSize == 'free') {

			$this->_fields[] = array (
				'type' => 'TextField',
				'fieldLabel' => 'Breite',
				'name' => 'width',
				'width' => 150
			);

			$this->_fields[] = array (
				'type' => 'TextField',
				'fieldLabel' => 'Höhe',
				'name' => 'height',
				'width' => 150
			);
		} else if ($component->fileSize instanceof Zend_Config) {
				//$test = $this->component->getStaticSetting('directory');
				$sizes = $component->fileSize;
				$sizes = $sizes->toArray();
				//p($sizes);
				$newSizes = array ();
				foreach ($sizes as $data) {
					$newSizes[] = array (
						$data,
						$data
					);
				}

				$this->_fields[] = array (
					'type' => 'ComboBox',
					'fieldLabel' => 'verfügbare Maße',
					'name' => 'scale',
					'width' => 150,
					'store' => array (
						'data' => $newSizes
					),
					'hiddenName' => 'scale',
					'editable' => false,
					'triggerAction' => 'all'
				);
			}

		if ($component->enableName) {
			$this->_fields[] = array (
				'type' => 'TextField',
				'fieldLabel' => 'Dateiname',
				'name' => 'file_name',
				'width' => 150
			);
			$this->_filename = true;
		}

		parent :: init();
	}

	//scale und file werden beim laden ignoriert
	protected function _fetchFromRow($row, $dataIndex)
	{
		if ($dataIndex == 'scale')
			return null;
		if ($dataIndex == 'file')
			return null;
		return parent :: _fetchFromRow($row, $dataIndex);
	}

	protected function _beforeSave(Zend_Db_Table_Row_Abstract $row)
	{
		$scale = $this->getRequest()->getParam('scale');

		if (!$this->_fileSize instanceof Zend_Config && strpos($this->_fileSize, 'x'))
			$scale = $this->_fileSize;
		if (strpos($scale, 'x')) {
			$x = strpos($scale, 'x');
			$row->height = substr($scale, $x +1);
			$row->width = substr($scale, 0, $x);
		}

		if (!$_FILES['file']['tmp_name'] && $this->_firstUpload()) {
			throw new Vps_ClientException("Please select a file");
		}
		$filename = false;
		if ($this->_filename) {
			$filename = $row->file_name;
			$start = strripos($filename, '.');
			$fileextension = '';
			if ($start != 0){
				$fileextension = substr($filename, $start);
				$filename = substr($filename, 0, $start);

			}
			$filter = new Zend_Filter_Alnum();
			$filename = $filter->filter($filename);
			$row->file_name = $filename.$fileextension;
		} else {
			$row->file_name = $_FILES['file']['name'];
		}
		if ($_FILES['file']['tmp_name']) {
			$row->vps_upload_id = $this->_fileUpload($filename);
		}
	}

	//es wird überprüft ob zu einer Komponente schon eine Datei hochgeladen wurde
	private function _firstUpload()
	{
		$tablename = 'Vpc_Simple_Image_FileDataModel';
		$this->_table = new $tablename;
		$row = $this->_table->find($this->component->getDbId(), $this->component->getComponentKey())->current();
		if ($row->vps_upload_id == 0)
			return true;
		else
			return false;
	}

	//Datei wird hochgeladen und Typ wird überprüft
	private function _fileUpload($filename)
	{
		$newFile = $_FILES['file'];
		$file_name = $newFile['name'];
		$start = strripos($newFile['name'], '.');
		$fileextension = substr($newFile['name'], $start +1);


		//throw new Vps_ClientException($filename);
		if ($filename)
			$file_name = $this->_newFileName($filename, $fileextension);

		$this->_filename = $file_name;

		//---- Überprüfung ob korrekter Dateityp

		$extensionsString = $this->component->getSetting('typesAllowed');
		$extenstions = array ();
		$delims = ',';
		$word = strtok($extensionsString, $delims);
		while (is_string($word)) {
			if ($word) {
				$extensions[] = trim($word);
			}
			$word = strtok($delims);
		}
		if (!in_array($fileextension, $extensions)) {
			throw new Vps_ClientException("Sie haben ein ungültiges Dateiformat ausgewählt");
		}
		///----

		$tablename = 'Vpc_Simple_Image_IndexModel';
		$this->_table = new $tablename;
		$id = $this->_table->insert(array (
			'path' => $this->component->getSetting('directory'
		) . $file_name));

		$config = Zend_Registry :: get('config');
		move_uploaded_file($newFile['tmp_name'], $config->uploads . $this->component->getSetting('directory') . $file_name);

		return $id;
	}

	//dateiname wird auf extension hin überprüft
	private function _newFileName($filename, $fileextension)
	{
		$start = strripos($filename, '.');
		$fileextensionOld = substr($filename, $start +1);
		if ($fileextensionOld != $fileextension){
			return $filename . '.' . $fileextension;
		} else {
			return $filename;

		}

	}

}