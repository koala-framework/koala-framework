<?php
class Vpc_Simple_Image_IndexController extends Vps_Controller_Action_Auto_Form_Vpc
{
	protected $_fields = array (
		array (
			'type'           => 'TextField',
			'inputType'      => 'file',
			'fieldLabel'     => 'Datei',
			'name'           => 'file',
			'width'          => 150
		),
		array (
			'type'       => 'TextField',
			'fieldLabel' => 'Bezeichnung',
			'name'       => 'text',
			'width'      => 150
		),

	);

	protected $_buttons = array (
		'save' => true
	);
	protected $_tableName = 'Vpc_Simple_Image_IndexModel';
	private $_fileSize = '';
	private $_filename = false;
	private $_filenameBefore = '';

	public function indexAction() {
		$config = array (
		'componentId' => $this->component->getId());
		$this->view->ext('Vpc.Simple.Image.Index', $config);
	}

	public function preDispatch() {
		parent :: preDispatch();
		$this->_fileSize = $this->component->getSetting('filesize');
		$fileSize = $this->_fileSize;

		//Einstellungen für die Veränderbarkeit der Höhe und Breite
		if ($fileSize == 'free') {

			$this->_fields[] = array (
				'type'       => 'TextField',
				'fieldLabel' => 'Breite',
				'name'       => 'width',
				'width'      => 150
			);

			$this->_fields[] = array (
				'type'       => 'TextField',
				'fieldLabel' => 'Höhe',
				'name'       => 'height',
				'width'      => 150
			);
		} else if (is_array($fileSize)) {
				//$test = $this->component->getStaticSetting('directory');
				$sizes = $fileSize;
				//p($sizes);
				$newSizes = array ();
				foreach ($sizes as $data) {
					$newSizes[] = array (
						$data,
						$data
					);
				}

				$this->_fields[] = array (
					'type'          => 'ComboBox',
					'fieldLabel'    => 'verfügbare Maße',
					'name'          => 'scale',
					'width'         => 150,
					'store'         => array (
						             'data' => $newSizes),
					'hiddenName'    => 'scale',
					'editable'      => false,
					'triggerAction' => 'all'
				);
			}

		if ($this->component->getSetting('allow_color')) {
			$this->_fields[] = array (
				'type'       => 'TextField',
				'fieldLabel' => 'Farbe',
				'name'       => 'color',
				'width'      => 150
			);
			$this->_filename = true;
		}

			if ($this->component->getSetting('allow') != '' && $this->component->getSetting('allow') != array ()) {

			$styles = $this->component->getSetting('allow');
			//p($sizes);
			$newStyles = array ();
			foreach ($styles as $data) {
				$newStyles[] = array (
					$data,
					$data
				);
			}
			$this->_fields[] = array (
				'type'          => 'ComboBox',
				'fieldLabel'    => 'Einstellungen',
				'name'          => 'style',
				'width'         => 150,
				'store'         => array (
					               'data' => $newStyles),
				'hiddenName'    => 'style',
				'editable'      => false,
				'triggerAction' => 'all'
			);
		}

		if ($this->component->getSetting('enableName')) {
			$this->_fields[] = array (
				'type'       => 'TextField',
				'fieldLabel' => 'Dateiname',
				'name'       => 'file_name',
				'width'      => 150
			);
			$this->_filename = true;
		}
	}

	//scale und file werden beim laden ignoriert
	protected function _fetchFromRow($row, $dataIndex) {
		if ($dataIndex == 'scale')
			return null;
		if ($dataIndex == 'file')
			return null;
		return parent :: _fetchFromRow($row, $dataIndex);
	}

	protected function _beforeSave(Zend_Db_Table_Row_Abstract $row)
	{

		$scale = $this->getRequest()->getParam('scale');

		if (!is_array($this->_fileSize) && strpos($this->_fileSize, 'x'))
			$scale = $this->_fileSize;
		if (strpos($scale, 'x')) {
			$x = strpos($scale, 'x');
			$row->height = substr($scale, $x +1);
			$row->width = substr($scale, 0, $x);
		}

		if (!$_FILES['file']['tmp_name'] && $this->_firstUpload()) {
			throw new Vps_ClientException("Please select a file");
		}

		if ($_FILES['file']['tmp_name'] != '' && !$this->component->getSetting('enableName')) {
			$this->_filename = false;
		}

		$filename = false;
		if ($this->_filename) {
			$filename = $row->file_name;
			$start = strripos($filename, '.');
			$fileextension = '';
			if ($start != 0) {
				$fileextension = substr($filename, $start);
				$filename = substr($filename, 0, $start);
			}
			$filter = new Zend_Filter_Alnum();
			$filename = $filter->filter($filename);
			$row->file_name = $filename . $fileextension;
		} else {
			$row->file_name = str_replace('.' . $this->_getExtension(), '', $_FILES['file']['name']);
		}
		if ($_FILES['file']['tmp_name']) {
			$row->vps_upload_id = $this->_fileUpload($filename);
		}
	}

	protected function _afterSave($row)
	{
		$pw_thumbnail = 'thumbi';
		$this->view->path = '/media/' . $this->component->getId() . '/' . MD5($pw_thumbnail . $this->component->getId()) . '/thumb.jpg?i=' . rand();
		$pw = 'jupidu';
		$filename = 'pic';
		if ($row->file_name != '') $filename = $row->file_name;
		$this->view->pathbig = '/media/' . $this->component->getId() . '/' . MD5($pw . $this->component->getId()) . '/' . $filename . '.' . $this->_getExtension();
		$extensions = $this->_getExtensions();
		$this->_deleteFromCache($extensions);
	}
	public function jsonLoadAction()
	{
		parent :: jsonLoadAction();
		$pw_thumbnail = 'thumbi';
		$path = '/media/' . $this->component->getId() . '/' . MD5($pw_thumbnail . $this->component->getId()) . '/thumb.jpg?i=' . rand();
		if (!$this->_firstUpload())
			$this->view->path = $path;
		else
			$this->view->path = '';
		$pw = 'jupidu';
		$filename = 'pic';
		if ($this->view->data['file_name'] != '') $filename = $this->view->data['file_name'];
		$this->view->pathbig = '/media/' . $this->component->getId() . '/' . MD5($pw . $this->component->getId()) . '/'.$filename.'.'.$this->_getExtension();
	}

	//es wird überprüft ob zu einer Komponente schon eine Datei hochgeladen wurde
	private function _firstUpload()
	{
		$tablename = 'Vpc_Simple_Image_IndexModel';
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

		$extensions = $this->_getExtensions();
		if (!in_array($fileextension, $extensions)) {
			throw new Vps_ClientException("Sie haben ein ungültiges Dateiformat ausgewählt");
		}
		///----
		$cnt = 1;
		$file_name_temp = $file_name;

		$config = Zend_Registry :: get('config');
		while (file_exists($config->uploads . $this->component->getSetting('directory') . $file_name_temp)) {
			$file_name_temp = $file_name;
			$file_name_temp = str_replace('.' . $fileextension, $cnt . '.' . $fileextension, $file_name_temp);
			$cnt++;
		}
		$file_name = $file_name_temp;

		$tablename = 'Vpc_Simple_Image_FileDataModel';
		$this->_table = new $tablename;
		$id = $this->_table->insert(array (
			'path' => $this->component->getSetting('directory'
		) . $file_name));

		move_uploaded_file($newFile['tmp_name'], $config->uploads . $this->component->getSetting('directory') . $file_name);

		return $id;
	}

	//liefert Array mit allen möglichen Extensions
	private function _getExtensions()
	{
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
		return $extensions;
	}

	//liefert die Extension des files
	private function _getExtension()
	{
		$extensions = $this->_getExtensions();
		foreach ($extensions as $data) {
			$filename = $this->component->getId() . '.' . $data;
			if (file_exists('./public/media/' . $filename)) {
				return $data;
			}
		}
	}

	//dateiname wird auf extension hin überprüft
	private function _newFileName($filename, $fileextension)
	{
		$start = strripos($filename, '.');
		$fileextensionOld = substr($filename, $start +1);
		if ($fileextensionOld != $fileextension) {
			return $filename . '.' . $fileextension;
		} else {
			return $filename;

		}
	}

	//Löscht Bild und thumbnail aus dem cache
	private function _deleteFromCache($extensions)
	{
		foreach ($extensions as $data) {
			$filename = $this->component->getId() . '.' . $data;
			if (file_exists('./public/media/' . $filename)) {
				unlink('./public/media/' . $filename);
			}
			if (file_exists('./public/thumbnail/' . $filename)) {
				unlink('./public/thumbnail/' . $filename);
			}
		}

	}

}