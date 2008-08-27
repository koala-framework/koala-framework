<?php
class Vps_Model_Select
{
    const WHERE = 'where';
    const WHERE_EQUALS = 'whereEquals';
    const WHERE_ID = 'id';
    const ORDER = 'order';
    const LIMIT_COUNT = 'limitCount';
    const LIMIT_OFFSET = 'limitOffset';
    const OTHER = 'other';

    protected $_parts = array();
    protected $_processed = array();
    protected $_checkProcessed = true;

    public function __construct($where = array())
    {
        foreach ($where as $key => $val) {
            $method = "where".ucfirst($key);
            $this->$method($val);
        }
    }

    public function whereEquals($field, $value)
    {
        $this->_checkNotProcessed(self::WHERE_EQUALS);
        $this->_parts[self::WHERE_EQUALS][$field] = $value;
        return $this;
    }

    public function where($cond, $value = null, $type = null)
    {
        $this->_checkNotProcessed(self::WHERE);
        $this->_parts[self::WHERE][] = array($cond, $value, $type);
        return $this;
    }

    public function whereId($id)
    {
        $this->_checkNotProcessed(self::WHERE_ID);
        $this->_parts[self::WHERE_ID] = $id;
        return $this;
    }

    public function order($field)
    {
        $this->_checkNotProcessed(self::ORDER);
        $this->_parts[self::ORDER] = $field;
        return $this;
    }

    public function limit($count, $offset = null)
    {
        $this->_checkNotProcessed(self::LIMIT_COUNT);
        if ($offset) $this->_checkNotProcessed(self::LIMIT_OFFSET);
        $this->_parts[self::LIMIT_COUNT] = $count;
        if ($offset) $this->_parts[self::LIMIT_OFFSET] = $offset;
        return $this;
    }

    protected function _checkNotProcessed($part)
    {
        if (in_array($part, $this->_processed)) {
            throw new Vps_Exception("Part '$part' is already processed");
        }
    }

    public function getParts()
    {
        return $this->_parts;
    }

    public function getPart($part)
    {
        if (!isset($this->_parts[$part])) return null;
        return $this->_parts[$part];
    }

    public function hasPart($part)
    {
        return isset($this->_parts[$part]);
    }

    public function setPart($type, $part)
    {
        $this->_parts[$type] = $part;
        return $this;
    }

    public function unsetPart($type)
    {
        unset($this->_parts[$type]);
    }

    public function processed($part)
    {
        $this->_processed[] = $part;
        return $this;
    }
    public function getUnprocessedParts()
    {
        $ret = array();
        foreach ($this->_parts as $type=>$part) {
            if (!in_array($type, $this->_processed)) {
                $ret[$type] = $part;
            }
        }
        return $ret;
    }

    public function setCheckProcessed($check)
    {
        $this->_checkProcessed = $check;
        return $this;
    }
    public function getCheckProcessed()
    {
        return $this->_checkProcessed;
    }

    public function resetProcessed()
    {
        $this->_processed = array();
        return $this;
    }
    public function checkAndResetProcessed()
    {
        if ($this->getCheckProcessed()) {
            if ($this->getUnprocessedParts()) {
                $p = implode(', ', array_keys($this->getUnprocessedParts()));
                throw new Vps_Exception("Can't process all parts of the select as some are not supported: $p");
            }
            $this->resetProcessed();
        }
    }

    public function __call($method, $arguments)
    {
        $this->_checkNotProcessed(self::OTHER);
        $this->_parts[self::OTHER][] = array('method' => $method, 'arguments' => $arguments);
        return $this;
    }

    public function toDebug()
    {
        $out = array();
        foreach ($this->_parts as $type=>$p) {
            if (in_array($type, $this->_processed)) {
                $type = " p ".$type;
            } else {
                $type = "up ".$type;
            }
            $out[$type] = $p;
        }
        $ret = print_r($out, true);
        $ret = preg_replace('#^Array#', get_class($this). ' checkProcessed:'.($this->_checkProcessed?'true':'false')." (p=processed, up=unprocessed)", $ret);
        $ret = "<pre>$ret</pre>";
        return $ret;
    }
}
