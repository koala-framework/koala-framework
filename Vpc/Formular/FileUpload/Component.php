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
                 'maxSize' => 2000
            )
        ));
    }

    public function getTemplateVars()
    {
        $return = parent::getTemplateVars();
        $return['width'] = $this->getSetting('width');
        $return['template'] = 'Formular/FileUpload.html';
        return $return;
    }

    public function validateField($mandatory)
    {
        $file = $_FILES[$this->getSetting('name')];

        if ($file['error'] == 4 && $mandatory) {
            return 'Feld ' . $this->getStore('description') . ' ist ein Pflichtfeld, bitte ausfüllen';
        }

        if ($file['error'] != 0 && $file['error'] != 4) {
            return 'Beim Dateiupload ist ein Fehler aufgetreten';
        }

        if ($this->getSetting('maxSize') < ($file['size']/1024)) {
            return 'Es dürfen Dateien bis max. '.$this->getSetting('maxSize').' kB hochgeladen werden';
        }

        if ($file['error'] != 4) {
            $extension = strtolower(substr($file['name'], strripos($file['name'], '.') + 1));
            $extensions = explode(',', $this->getSetting('types_allowed'));
            foreach ($extensions as $key => $val) {
                $extensions[$key] = strtolower(trim($val));
            }
            if (!in_array($extension, $extensions)) {
                return 'Ungültiges Format in Feld ' . $this->getStore('description') . ', zulässige Formate: ' . $this->getSetting('types_allowed');
            }
        }

        return '';
    }
}
