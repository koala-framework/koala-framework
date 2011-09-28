<?php
require_once 'Zend/Registry.php';
class Vps_Registry extends Zend_Registry
{
    public function offsetGet($index)
    {
        if ($index == 'db' && !parent::offsetExists($index)) {
            $v = Vps_Setup::createDb();
            $this->offsetSet('db', $v);
            return $v;
        } else if ($index == 'dao' && !parent::offsetExists($index)) {
            $v = Vps_Setup::createDao();
            $this->offsetSet('dao', $v);
            return $v;
        } else if ($index == 'acl' && !parent::offsetExists($index)) {
            $class = Vps_Registry::get('config')->aclClass;
            $v = new $class();
            $this->offsetSet('acl', $v);
            return $v;
        } else if ($index == 'userModel' && !parent::offsetExists($index)) {
            $v = Vps_Model_Abstract::getInstance(self::get('config')->user->model);
            $this->offsetSet('userModel', $v);
            return $v;
        } else if ($index == 'trl' && !parent::offsetExists($index)) {
            $v = Vps_Trl::getInstance();
            $this->offsetSet('trl', $v);
            return $v;
        } else if ($index == 'hlp' && !parent::offsetExists($index)) {
            $v = new Vps_Hlp();
            $this->offsetSet('hlp', $v);
            return $v;
        }
        return parent::offsetGet($index);
    }

    public function offsetExists($index)
    {
        if (in_array($index, array('db', 'dao', 'acl', 'userModel', 'trl', 'hlp'))) {
            return true;
        }
        return parent::offsetExists($index);
    }
}
