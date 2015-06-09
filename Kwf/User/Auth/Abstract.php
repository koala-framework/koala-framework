<?php
class Kwf_User_Auth_Abstract
{
    protected $_model;

    public function __construct($model)
    {
        $this->_model = $model;
    }

    public function getModel()
    {
        return $this->_model;
    }
}
