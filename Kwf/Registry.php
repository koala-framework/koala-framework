<?php
class Kwf_Registry extends Zend_Registry
{
    public function offsetGet($index)
    {
        if ($index == 'db' && !$this->_isStored($index)) {
            $v = Kwf_Setup::createDb();
            $this->offsetSet('db', $v);
            return $v;
        } else if ($index == 'config' && !$this->_isStored($index)) {
            $v = Kwf_Config_Web::getInstance();
            $this->offsetSet('config', $v);
            return $v;
        } else if ($index == 'dao' && !$this->_isStored($index)) {
            $v = Kwf_Setup::createDao();
            $this->offsetSet('dao', $v);
            return $v;
        } else if ($index == 'acl' && !$this->_isStored($index)) {
            $v = Kwf_Acl::getInstance();
            $this->offsetSet('acl', $v);
            return $v;
        } else if ($index == 'userModel' && !$this->_isStored($index)) {
            $v = self::get('config')->user->model;
            if ($v) {
                $v = Kwf_Model_Abstract::getInstance($v);
            }
            $this->offsetSet('userModel', $v);
            return $v;
        } else if ($index == 'trl' && !$this->_isStored($index)) {
            $v = Kwf_Trl::getInstance();
            $this->offsetSet('trl', $v);
            return $v;
        }
        return parent::offsetGet($index);
    }

    public function offsetExists($index)
    {
        if (in_array($index, array('db', 'config', 'dao', 'acl', 'userModel', 'trl'))) {
            return true;
        }
        return $this->_isStored($index);
    }

    public function offsetUnset($index)
    {
        if (in_array($index, array('db', 'dao', 'userModel'))) {
            if (!$this->_isStored($index)) {
                //not yet set, ignore
                return;
            }
        }
        return parent::offsetUnset($index);
    }

    //Zend_Registry::offsetExists calls array_key_exists() on $this, which is deprecated since PHP 7.4
    private function _isStored($index)
    {
        return ArrayObject::offsetExists($index);
    }
}
Zend_Registry::setClassName('Kwf_Registry');
