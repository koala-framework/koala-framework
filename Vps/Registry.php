<?php
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
        } else if ($index == 'config' && !parent::offsetExists($index)) {
            $v = Vps_Setup::createConfig();
            $this->offsetSet('config', $v);
            return $v;
        }
        return parent::offsetGet($index);
    }

    public function offsetExists($index)
    {
        if (in_array($index, array('db', 'dao', 'config'))) {
            return true;
        }
        return parent::offsetExists($index);
    }
}
