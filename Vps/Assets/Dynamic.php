<?php
/**
 * Dynamisches Asset, um Dependencies Cachen zu können den Dateinamen aber
 * dynamisch zu ermitteln.
 *
 * Wird verwendet von Vpc_Basic_Text
 **/
class Vps_Assets_Dynamic
{
    private $_callback;
    private $_type;
    public function __construct($type, $callback)
    {
        $this->_type = $type;
        $this->_callback = $callback;
    }
    public function getFile()
    {
        return call_user_func($this->_callback);
    }
    public function getType()
    {
        return $this->_type;
    }
}
