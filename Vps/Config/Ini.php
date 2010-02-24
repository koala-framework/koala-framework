<?php
class Vps_Config_Ini extends Zend_Config_Ini
{
    private $_filename;

    /**
    * Liest die Datei aus und schreibt den Inhalt in einen Array
    *
    * @param string $filename
    */
    public function __construct($filename)
    {
        $this->_filename = $filename;
        $data = parse_ini_file($filename, true);
        Zend_Config::__construct($data, true);
    }

    /**
    * Setzt den Wert für ein Element in einer Section
    * es wird überprüft ob Section oder Element leer sind, falls ja
    * wird nichts angelegt
    *
    * @param string $section
    * @param string $element
    * @param string $value
    */
    public function setValue($section, $element, $value)
    {
        if (!is_string($value)) {
            throw new Vps_Exception('Value must be a string, ' . gettype($value) . ' given');
        }

        if ($section != '' && $element != '') {
            if (!$this->$section) {
                $this->$section = array($element => $value);
            } else {
                $this->$section->$element = $value;
            }
        }
    }

    /**
    * Der ini File wird neu geschrieben.
    *
    */
    function write()
    {
        $content = "";
        foreach ($this->_data as $section => $elements) {
            $content .= "[$section] \n";
            foreach ($elements as $key => $element) {
                $content .= "$key = $element\n";
            }
            $content .= "\n";
        }
            
        $handle = fopen($this->_filename, 'w');
        fwrite($handle, $content);
        fclose($handle);
    }

    /**
    * überprüft ob der Wert und section vorhanden sind
    *
    * @param string $section
    * @param string $element
    * @return true wenn der key bereits existiert
    */
    public function checkKeyExists ($section, $element)
    {
        if ($this->$section->$element == '') {
            return false;
        } else {
            return true;
        }
    }

    //kopie von zend um INI_SCANNER_RAW einzuf?gen; f?r Php 5.3
    //im neuen zend gibts _parseIniFile - nur das sollte ?berschrieben werden
    protected function _loadIniFile($filename)
    {
        set_error_handler(array($this, '_loadFileErrorHandler'));
        $loaded = parse_ini_file($filename, true, INI_SCANNER_RAW); // Warnings and errors are suppressed
        restore_error_handler();
        // Check if there was a error while loading file
        if ($this->_loadFileErrorStr !== null) {
            /**
             * @see Zend_Config_Exception
             */
            require_once 'Zend/Config/Exception.php';
            throw new Zend_Config_Exception($this->_loadFileErrorStr);
        }

        $iniArray = array();
        foreach ($loaded as $key => $data)
        {
            $pieces = explode($this->_sectionSeparator, $key);
            $thisSection = trim($pieces[0]);
            switch (count($pieces)) {
                case 1:
                    $iniArray[$thisSection] = $data;
                    break;

                case 2:
                    $extendedSection = trim($pieces[1]);
                    $iniArray[$thisSection] = array_merge(array(';extends'=>$extendedSection), $data);
                    break;

                default:
                    /**
                     * @see Zend_Config_Exception
                     */
                    require_once 'Zend/Config/Exception.php';
                    throw new Zend_Config_Exception("Section '$thisSection' may not extend multiple sections in $filename");
            }
        }

        return $iniArray;
    }
}
