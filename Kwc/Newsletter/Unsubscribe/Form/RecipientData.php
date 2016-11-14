<?php
class Kwc_Newsletter_Unsubscribe_Form_RecipientData extends Kwf_Data_Abstract
{
    protected $_method;

    public function __construct($method)
    {
        if (!is_string($method)) {
            throw new Kwf_Exception("method must be of type string");
        }

        $this->_method = $method;
    }

    public function load($row, array $info = array())
    {
        $interfaces = class_implements($row);
        $methodAllowed = false;
        foreach ($interfaces as $k => $i) {
            if (is_instance_of($i, 'Kwc_Mail_Recipient_Interface')) {
                if (method_exists($i, $this->_method)) {
                    $methodAllowed = true;
                }
            }
        }

        if (!$methodAllowed) {
            throw new Kwf_Exception("This method is not allowed");
        }

        return $row->{$this->_method}();
    }
}
