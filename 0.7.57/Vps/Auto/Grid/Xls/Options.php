<?php
class Vps_Auto_Grid_Xls_Options
{

    protected $_options;
    protected $_defaults = array('width' => 10.0);

    public function __construct(array $options = array())
    {
        foreach ($options as $option => $value) {
            $method = 'set'.ucfirst($option);
            $this->$method($value);
        }
    }

    public function __call($method, $arguments)
    {
        if (substr($method, 0, 3) == 'set') {
            if (!isset($arguments[0])) {
                throw new Vps_Exception("Missing argument 1 (value)");
            }
            $name = strtolower(substr($method, 3, 1)) . substr($method, 4);
            return $this->setOption($name, $arguments[0]);
        } else if (substr($method, 0, 3) == 'get') {
//         echo $method;
            $name = strtolower(substr($method, 3, 1)) . substr($method, 4);
            return $this->getOption($name);
        } else {
            throw new Vps_Exception("Invalid method called: '$method'");
        }
    }

    public function getOption($option)
    {
        if (isset($this->_options[$option])) {
            return $this->_options[$option];
        } else {
            return null;
        }
    }

    public function setOption($option, $value)
    {
        $this->_options[$option] = $value;
    }

    public function getDefaultOption($option)
    {
        if (isset($this->_defaults[$option])) {
            return $this->_defaults[$option];
        } else {
            return null;
        }
    }

}