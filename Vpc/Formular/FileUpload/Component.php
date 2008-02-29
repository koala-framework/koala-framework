<?php
class Vpc_Formular_FileUpload_Component extends Vpc_Formular_Field_Abstract
{
    public static function getSettings()
    {
        return array_merge(parent::getSettings(), array(
            'componentName' => 'Formular Fields.FileUpload',
            'tablename' => 'Vpc_Formular_FileUpload_Model',
            'default' => array(
                 'types_allowed' => '',
                 'width' => '50',
                 'max_size' => 2000
            )
        ));
    }

    public function getTemplateVars()
    {
        $return = parent::getTemplateVars();
        $return['width'] = $this->_getRow()->width;
        return $return;
    }

    public function processInput()
    {
        $file = $_FILES[$this->_getRow()->name];

        if ($file) {
            $t = new Vps_Dao_File();
            $uploadRow = $t->createRow();
        }
        $uploadRow->uploadFile($file);
        $this->_getRow()->upload_id = $uploadRow->id;
    }

    public function getValue()
    {
        return $this->_getRow()->upload_id;
    }

    public function validateField($mandatory)
    {
        $file = $_FILES[$this->_getRow()->name];

        if ($file['error'] == 4 && $mandatory) {
            return 'Feld ' . $this->getStore('fieldLabel') . ' ist ein Pflichtfeld, bitte ausfüllen';
        }

        if ($file['error'] != 0 && $file['error'] != 4) {
            return 'Beim Dateiupload ist ein Fehler aufgetreten';
        }

        if ($this->_getRow()->max_size < ($file['size']/1024)) {
            return 'Es dürfen Dateien bis max. '.$this->_getRow()->max_size.' kB hochgeladen werden';
        }

        if ($file['error'] != 4) {
            $extension = strtolower(substr($file['name'], strripos($file['name'], '.') + 1));
            if ($this->_getRow()->types_allowed) {
                $extensions = explode(',', $this->_getRow()->types_allowed);
                foreach ($extensions as $key => $val) {
                    $extensions[$key] = strtolower(trim($val));
                }

                if (!in_array($extension, $extensions)) {
                    return 'Ungültiges Format in Feld ' . $this->getStore('fieldLabel') . ', zulässige Formate: ' . $this->_getRow()->types_allowed;
                }
            }
        }

        return '';
    }
}
