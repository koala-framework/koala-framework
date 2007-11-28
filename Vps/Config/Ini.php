<?ph
class Vps_Config_Ini extends Zend_Config_In

    private $_filename
   
    /*
     * Liest die Datei aus und schreibt den Inhalt in einen Arra
     
     * @param string $filenam
     *
    public function __construct($filename
    {  
        $this->_filename = $filename
        $data = parse_ini_file($filename, true)
        Zend_Config::__construct($data, true)
    
   
    /*
     * Setzt den Wert für ein Element in einer Sectio
     * es wird überprüft ob Section oder Element leer sind, falls j
     * wird nichts angeleg
     
     * @param string $sectio
     * @param string $elemen
     * @param string $valu
     *
    public function setValue($section, $element, $value
    {     
        if (!is_string($value)) 
            throw new Vps_Exception('Value must be a string, ' . gettype($value) . ' given')
        
       
        if ($section != '' && $element != '') 
           if (!$this->$section) 
		       $this->$section = array($element => $value)
		   } else 
		       $this->$section->$element = $value
	       
       
    
   
    /*
     * Der ini File wird neu geschrieben
     
     *
    function write()  
        $content = ""
		foreach ($this->_data as $section => $elements) 
			$content .= "[$section] \n"
			foreach ($elements as $key => $element) 
    			$content .= "$key = $element\n"
			
			$content .= "\n";
		}       
		
		$handle = fopen($this->_filename, 'w')
		fwrite($handle, $content)
		fclose($handle)
    
   
    /*
     * überprüft ob der Wert und section vorhanden sind
     
     * @param string $sectio
     * @param string $elemen
     * @return true wenn der key bereits existier
     *
    public function checkKeyExists ($section, $element)
       
        if ($this->$section->$element == '')
            return false
        } else 
            return true
        
    
   
