<?php
class Vpc_Formular_FileUpload_Index extends Vpc_Formular_Field_Abstract
{
    protected $_defaultSettings = array('types_allowed' => '', 'name' => '', 'width' => '50', 'maxSize' => 2000);

    public function getTemplateVars()
    {
        $return['width'] = $this->getSetting('width');
        $return['name'] = $this->getSetting('name');
        $return['id'] = $this->getDbId().$this->getComponentKey();
        $return['template'] = 'Formular/FileUpload.html';
        return $return;
    }

    public function validateField($mandatory)
    {
        $file = $_FILES[$this->getName()];

        if ($file['error'] != 0 && $mandatory == 1) return 'Feld '.$this->getName().' ist ein Pflichtfeld, bitte ausfüllen';

        if ($file['error'] != 0 && $file['error'] != 4) return 'Beim Feld '.$this->getName().' ist ein Fehler aufgetreten';

        if ($this->getSetting('maxSize') < ($file['size']/1024)) return 'Es dürfen Daten max. bis '.$this->getSetting('maxSize').' verwendet werden';

        if ($file['error'] != 4) {
			//zerlegt den Dateinamen
			$file = $file['name'];
			$start = strripos($file, '.');
			$fileextension = substr($file, $start+1);

			//zerlegt den Datenbankeintrag
			$extensionsString = $this->getSetting('types_allowed');
			$extenstions = array();
			$delims = ',';
			$word = strtok($extensionsString, $delims);
			while (is_string($word)){
			    if ($word){
			        $extensions[] = trim($word);
			    }
			    $word = strtok($delims);
            }
            if (!in_array($fileextension, $extensions)) return 'ungültiges Format in Feld '.$this->getName().', zulässige Formate: '.$this->getSetting('types_allowed');
        }
        return true;

    }

    public function processInput()
    {
      //  nothing
    }
}