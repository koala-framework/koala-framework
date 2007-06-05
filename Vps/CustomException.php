<?php
class Vps_CustomException extends Vps_Exception {
    
    private $_line;
    private $_file;
    
    public function setLine($line)
    { 
        $this->_line = $line;
    }
    
    public function setFile($file)
    {
        $this->_file = $file;
    }
}
