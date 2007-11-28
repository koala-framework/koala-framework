<?p
class Vps_Registry extends Zend_Regist

    public function offsetGet($inde
   
        if ($index == 'db' && !parent::offsetExists($index))
            $v = Vps_Setup::createDb(
            $this->offsetSet('db', $v
            return $
        } else if ($index == 'dao' && !parent::offsetExists($index))
            $v = Vps_Setup::createDao(
            $this->offsetSet('dao', $v
            return $
        } else if ($index == 'config' && !parent::offsetExists($index))
            $v = Vps_Setup::createConfig(
            $this->offsetSet('config', $v
            return $
       
        return parent::offsetGet($index
   

    public function offsetExists($inde
   
        if (in_array($index, array('db', 'dao', 'config')))
            return tru
       
        return parent::offsetExists($index
   

