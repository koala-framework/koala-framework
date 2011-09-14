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
        } else if ($index == 'config' && !parent::offsetExists($index)) {
            require_once 'Vps/Config/Web.php';
            $v = Vps_Config_Web::getInstance(Vps_Setup::getConfigSection());
            $this->offsetSet('config', $v);
            return $v;
        } else if ($index == 'configMtime' && !parent::offsetExists($index)) {
            $v = Vps_Config_Web::getInstanceMtime(Vps_Setup::getConfigSection());
            $this->offsetSet('configMtime', $v);
            return $v;
        } else if ($index == 'dao' && !parent::offsetExists($index)) {
            $v = Vps_Setup::createDao();
            $this->offsetSet('dao', $v);
            return $v;
        } else if ($index == 'acl' && !parent::offsetExists($index)) {
            $class = Vps_Registry::get('config')->aclClass;
            if (!$class) {
                $validCommands = array('shell', 'export', 'copy-to-test'); //für ältere branches
                if (php_sapi_name() != 'cli' || !isset($_SERVER['argv'][1]) || !in_array($_SERVER['argv'][1], $validCommands)) {
                    throw new Vps_Exception("'aclClass' has to exist in web-config and the web must have an own acl-class for media output rights check (NOT CREATED IN BOOTSTRAP!)");
                }
                $class = 'Vps_Acl';
            }
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
        if (in_array($index, array('db', 'config', 'configMtime', 'dao', 'acl', 'userModel', 'trl', 'hlp'))) {
            return true;
        }
        return parent::offsetExists($index);
    }
}
