<?php
class Vpc_Simple_Image_IndexController extends Vps_Controller_Action_Auto_Form_Vpc
{
    protected $_fields = array (
        array (
            'type'           => 'TextField',
            'inputType'      => 'file',
            'fieldLabel'     => 'File',
            'name'           => 'file',
            'width'          => 150
        ),
        array (
            'type'       => 'TextField',
            'fieldLabel' => 'Filename',
            'name'       => 'name',
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
        $this->view->ext('Vpc.Simple.Image.Index');
    }

    public function preDispatch() {
        parent :: preDispatch();

        //Einstellungen für die Veränderbarkeit der Höhe und Breite
        $size = $this->component->getSetting('size');
        if (empty($size)) {
            $this->_fields[] = array (
                'type'       => 'TextField',
                'fieldLabel' => 'Width',
                'name'       => 'width',
                'width'      => 150
            );
            $this->_fields[] = array (
                'type'       => 'TextField',
                'fieldLabel' => 'Height',
                'name'       => 'height',
                'width'      => 150
            );
        } else {
            $sizes = array();
            if (is_array($size[0])) {
                foreach ($size as $key => $val) {
                    $str = $val[0] . ' x ' . $val[1];
                    $sizes[] = array($str, $str);
                }
            } else {
                $str = $size[0] . ' x ' . $size[1];
                $sizes[] = array($str, $str);
            }
            $this->_fields[] = array (
                'type'          => 'ComboBox',
                'fieldLabel'    => 'Possible Sizes',
                'name'          => 'size',
                'width'         => 150,
                'forceSelection'=> true,
                'store'         => array('data' => $sizes),
                'editable'      => false,
                'triggerAction' => 'all'
            );
        }
        
        if ($this->component->getSetting('allow_color')) {
            $this->_fields[] = array (
                'type'       => 'TextField',
                'fieldLabel' => 'Color',
                'name'       => 'color',
                'width'      => 150
            );
            $this->_filename = true;
        }
        
        if ($this->component->getSetting('allow') != '' && $this->component->getSetting('allow') != array()) {
            $styles = $this->component->getSetting('allow');
            $newStyles = array ();
            foreach ($styles as $data) {
                $newStyles[] = array($data, $data);
            }
            $this->_fields[] = array (
                'type'          => 'ComboBox',
                'fieldLabel'    => 'Settings',
                'name'          => 'style',
                'width'         => 150,
                'store'         => array('data' => $newStyles),
                'hiddenName'    => 'style',
                'editable'      => false,
                'triggerAction' => 'all'
            );
        }
            
    }
    
    public function jsonLoadAction()
    {
        parent::jsonLoadAction();
        $this->view->urlbig = $this->component->getImageUrl();
        $this->view->url = $this->component->getImageUrl(Vpc_Simple_Image_Index::SIZE_THUMB);
    }

    //scale und file werden beim laden ignoriert
    protected function _fetchFromRow($row, $dataIndex)
    {
        switch ($dataIndex) {
            case 'size':
                $size = $this->component->getSetting('size');
                $sizes = array();
                if (is_array($size[0])) {
                    foreach ($size as $key => $val) {
                        if ($row->width == $val[0] && $row->height == $val[1]) {
                            return $val[0] . ' x ' . $val[1];
                        }
                    }
                    return $size[0][0] . ' x ' . $size[0][1];
                } else if (!empty($size)){
                    return $size[0] . ' x ' . $size[1];
                }
                return '';
            case 'file':
                return null;
            default:
                return parent::_fetchFromRow($row, $dataIndex);
        }
    }

    protected function _beforeSave(Zend_Db_Table_Row_Abstract $row)
    {
        if ($row->vps_upload_id == 0 && $_FILES['file']['error'] == UPLOAD_ERR_NO_FILE) {
            throw new Vps_ClientException('Please select a file');
        }
    
        $fileTable = new Vps_Dao_File();

        $sizes = $this->component->getSetting('size');
        if (!empty($sizes)) {
            $values = explode('x', $this->_getParam('size'));
            $row->width = (int)$values[0];
            $row->height = (int)$values[1];
        }

        if ($_FILES['file']['tmp_name']) {
            $extension = substr(strrchr($_FILES['file']['name'], '.'), 1);
            $extensions = $this->component->getExtensions();
            if (!in_array($extension, $extensions)) {
                throw new Vps_ClientException('File-extension not allowed. Allowed: ' . implode(', ', $extensions));
            }

            try {
                $fileTable->deleteFile($row->vps_upload_id);
                $row->vps_upload_id = $fileTable->uploadFile($_FILES['file'], $this->component->getSetting('directory'));
            } catch (Vps_Exception $e) {
                throw new Vps_ClientException($e->getMessage());
            }         
        }
        
        $fileTable->deleteCacheFile($row->vps_upload_id, $this->component->getId());
    }

}