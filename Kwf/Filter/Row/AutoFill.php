<?php
class Vps_Filter_Row_AutoFill extends Vps_Filter_Row_Abstract
{
    protected $_template;

    public function __construct($template)
    {
        $this->_template = $template;
    }

    public function filter($row)
    {
        $value = $this->_template;
        preg_match_all("/{(.*)}/U", $this->_template, $matches);
        foreach ($matches[0] as $key => $match) {
            $fn = $matches[1][$key];
            $value = str_replace($match, $row->$fn, $value);
        }
        return $value;
    }

    public function filterAfterSave()
    {
        return strpos($this->_template, '{id}') !== false;
    }
}
