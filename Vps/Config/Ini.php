<?php
require_once 'Zend/Config/Ini.php';

class Vps_Config_Ini extends Zend_Config_Ini
{
    private function _fixValues(&$data)
    {
        foreach ($data as $k=>$i) {
            if (is_array($i)) {
                $this->_fixValues($data[$k]);
            } else {
                if ($i === 'false') $data[$k] = false;
                if ($i === 'true') $data[$k] = true;
            }
        }
    }

    //kopie von zend um INI_SCANNER_RAW einzuf?gen; f?r Php 5.3
    //im neuen zend gibts _parseIniFile - nur das sollte ?berschrieben werden
    protected function _loadIniFile($filename)
    {
        if (version_compare(PHP_VERSION, '5.3.0') >= 0) {
            $loaded = parse_ini_file($filename, true, INI_SCANNER_RAW); // Warnings and errors are suppressed
            $this->_fixValues($loaded);
        } else {
            $loaded = parse_ini_file($filename, true); // Warnings and errors are suppressed
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
