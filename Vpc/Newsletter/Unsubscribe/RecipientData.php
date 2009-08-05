<?php
class Vpc_Newsletter_Unsubscribe_RecipientData extends Vps_Data_Abstract
{
    protected $_method;

    public function __construct($method)
    {
        if (!is_string($method)) {
            throw new Vps_Exception("method must be of type string");
        }

        $this->_method = $method;
    }

    public function load($row)
    {
        $interfaces = class_implements($row);
        $methodAllowed = false;
        foreach ($interfaces as $k => $i) {
            if (is_instance_of($i, 'Vpc_Mail_Recipient_Interface')) {
                if (method_exists($i, $this->_method)) {
                    $methodAllowed = true;
                }
            }
        }

        if (!$methodAllowed) {
            throw new Vps_Exception("This method is not allowed");
        }

        return $row->{$this->_method}();
    }
}
