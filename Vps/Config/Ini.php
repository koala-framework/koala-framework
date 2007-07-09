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
    function write()  {
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
    
}