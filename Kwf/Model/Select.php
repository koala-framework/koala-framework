<?php
/**
 * @package Model
 */
class Kwf_Model_Select
{
    const WHERE = 'where';
    const WHERE_EQUALS = 'whereEquals';
    const WHERE_NOT_EQUALS = 'whereNotEquals';
    const WHERE_ID = 'whereId';
    const WHERE_NULL = 'whereNull';
    const WHERE_EXPRESSION = 'whereExpression';
    const ORDER = 'order';
    const LIMIT_COUNT = 'limitCount';
    const LIMIT_OFFSET = 'limitOffset';
    const EXPR = 'expr';
    const OTHER = 'other';
    const UNION = 'union';
    const IGNORE_DELETED = 'ignoreDeleted';

    const ORDER_RAND = 'orderRand';


    protected $_parts = array();

    public function __construct($where = array())
    {
        if (is_string($where)) {
            $where = array($where);
        }

        foreach ($where as $key => $val) {
            if (is_int($key)) {
                $this->where($val);
                continue;
            }
            if ($key != 'limit' && $key != 'order' && $key != self::IGNORE_DELETED) {
                $method = "where".ucfirst($key);
            } else {
                $method = $key;
            }
            if (method_exists($this, $method)) {
                $this->$method($val);
            } else if (is_null($val)) {
                $this->whereNull($key);
            } else {
                $this->where($key, $val);
            }
        }
    }

    public function copyParts(array $parts, Kwf_Model_Select $sourceSelect)
    {
        foreach ($parts as $p) {
            if (isset($sourceSelect->_parts[$p])) {
                $this->_parts[$p] = $sourceSelect->_parts[$p];
            }
        }
    }

    //vielleicht mal umstellen auf:
    /*
interface Kwf_Model_Select_Expr_Interface {}
class Kwf_Model_Select_Expr_CompareField_Abstract implements Kwf_Model_Select_Expr_Interface
{
    __construct($field, $value)
    getField
    getValue
}
class Kwf_Model_Select_Expr_Not implements Kwf_Model_Select_Expr_Interface
{
    __construct(Kwf_Model_Select_Expr_Interface $expr);
}
class Kwf_Model_Select_Expr_Equal extends Kwf_Model_Select_Expr_CompareField_Abstract {}
class Kwf_Model_Select_Expr_Lower extends Kwf_Model_Select_Expr_CompareField_Abstract {}
class Kwf_Model_Select_Expr_Higher extends Kwf_Model_Select_Expr_CompareField_Abstract {}
class Kwf_Model_Select_Expr_NotEquals implements Kwf_Model_Select_Expr_Not
{
    __construct($field, $value)
    {
        parent::__construct(new Kwf_Model_Select_Expr_Equal($field, $value);
    }
}
class Kwf_Model_Select_Expr_LowerEquals implements Kwf_Model_Select_Expr_Or
{
    __construct($field, $value)
    {
        parent::__construct(array(new Kwf_Model_Select_Expr_Lower($field, $value), new Kwf_Model_Select_Expr_Equal($field, $value));
    }
}
    return $this->where(new Kwf_Model_Select_Expr_Equal($field, $value));
    return $this->where(new Kwf_Model_Select_Expr_Not(new Kwf_Model_Select_Expr_Equal($field, $value)));
    return $this->where(new Kwf_Model_Select_Expr_Or(array(new Kwf_Model_Select_Expr_Equal($field, $value),
                                                        new Kwf_Model_Select_Expr_Higher($field, $value)));
    */
    public function whereEquals($field, $value = null)
    {
        if (is_array($field)) {
            foreach ($field as $f=>$v) {
                $this->whereEquals($f, $v);
            }
            return $this;
        }
        if (is_null($value)) {
            throw new Kwf_Exception("value is required");
        }
        $this->_parts[self::WHERE_EQUALS][$field] = $value;
        return $this;
    }

    public function whereNotEquals($field, $value = null)
    {
        if (is_array($field)) {
            foreach ($field as $f=>$v) {
                $this->whereNotEquals($f, $v);
            }
            return $this;
        }
        if (is_null($value)) {
            throw new Kwf_Exception("value is required");
        }
        $this->_parts[self::WHERE_NOT_EQUALS][$field] = $value;
        return $this;
    }

    public function whereNull($field)
    {
        if (strpos($field, '?') !==false) {
            throw new Kwf_Exception("You don't want '?' in the field '$field'");
        }
        $this->_parts[self::WHERE_NULL][] = $field;
        return $this;
    }

    public function where($cond, $value = null, $type = null)
    {
        if ($cond instanceof  Kwf_Model_Select_Expr_Interface ) {
            $this->_parts[self::WHERE_EXPRESSION][] = $cond;
            return $this;
        }
        if (strpos($cond, '?') !==false && is_null($value)) {
            throw new Kwf_Exception("Can't use '$cond' with value 'null'");
        }

        $this->_parts[self::WHERE][] = array($cond, $value, $type);
        return $this;
    }

    public function whereId($id)
    {
        if (is_null($id)) {
            throw new Kwf_Exception('value is required');
        }
        $this->_parts[self::WHERE_ID] = $id;
        return $this;
    }

    public function order($field, $dir = 'ASC')
    {
        if (is_array($field)) {
            if (!isset($field['field'])) {
                foreach ($field as $f) {
                    $this->order($f);
                }
            } else {
                if (isset($field['dir'])) {
                    throw new Kwf_Exception("'dir' key doesn't exist anymore, it was renamed to 'direction'");
                }
                if (!isset($field['direction'])) $field['direction'] = 'ASC';
                $this->_parts[self::ORDER][] = $field;
            }
        } else {
            $this->_parts[self::ORDER][] = array('field'=>$field, 'direction'=>$dir);
        }
        return $this;
    }

    public function limit($count, $offset = null)
    {
        if (is_array($count)) {
            $offset = $count['start'];
            $count = $count['limit'];
        }
        $this->_parts[self::LIMIT_COUNT] = $count;
        if ($offset) $this->_parts[self::LIMIT_OFFSET] = $offset;
        return $this;
    }

    public function expr($field)
    {
        $this->_parts[self::EXPR][] = $field;
        return $this;
    }

    public function union(Kwf_Model_Select $select)
    {
        $this->_parts[self::UNION][] = $select;
    }

    public function merge(Kwf_Model_Select $other)
    {
        $mergeArrayParts = array(self::WHERE, self::WHERE_EXPRESSION, self::WHERE_NULL,
            self::WHERE_EQUALS, self::WHERE_NOT_EQUALS, self::OTHER);
        foreach ($other->_parts as $part=>$value) {
            if (in_array($part, $mergeArrayParts) && isset($this->_parts[$part])) {
                $this->_parts[$part] = array_merge($this->_parts[$part], $value);
            } else {
                $this->_parts[$part] = $value;
            }
        }
    }

    public function getParts()
    {
        return $this->_parts;
    }

    public function getPartTypes()
    {
        return array_keys($this->_parts);
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

    public function __call($method, $arguments)
    {
        $this->_parts[self::OTHER][] = array('method' => $method, 'arguments' => $arguments);
        return $this;
    }

    public function toDebug()
    {
        $out = '';
        foreach ($this->_parts as $type=>$p) {
            if (is_array($p)) {
                $out .= "\n";
                $out .= "$type => array(\n";
                foreach ($p as $k=>$i) {
                    $out .= '    ';
                    if ($i instanceof Kwf_Model_Select_Expr_Interface) {
                        if (!is_int($k)) $out .= $k.' => ';
                        $out .= _pArray($i, '    ');
                    } else {
                        $out .= $k.' => '.Kwf_Debug::_btArgString($i);
                    }
                    $out .= ",\n";
                }
                $out .= "),\n";
            } else {
                $out .= "\n";
                $out .= "$type => ".Kwf_Debug::_btArgString($p).", ";
            }
        }
        $out = trim($out, ', ');
        $ret = '<pre>'.get_class($this).'('.$out."\n)</pre>";
        return $ret;
    }

    public function toArray()
    {
        $parts = array();
        foreach ($this->_parts as $k=>$i) {
            if (is_array($i)) {
                foreach ($i as $ak=>$ai) {
                    if ($ai instanceof Kwf_Model_Select_Expr_Interface) {
                        $ai = $ai->toArray();
                    }
                    $i[$ak] = $ai;
                }
            }
            $parts[$k] = $i;
        }
        return array(
            'selectObjVersion' => 100, //version
            'parts' => $parts
        );
    }

    public static function fromArray(array $data)
    {
        if ($data['selectObjVersion'] != 100) throw new Kwf_Exception("invalid select version");
        $ret = new Kwf_Model_Select();
        foreach ($data['parts'] as $k=>$i) {
            if (is_array($i)) {
                foreach ($i as $ak=>$ai) {
                    if (is_array($ai) && isset($ai['exprType'])) {
                        $ai = Kwf_Model_Select_Expr::fromArray($ai);
                    }
                    $i[$ak] = $ai;
                }
            }
            $ret->_parts[$k] = $i;
        }
        return $ret;
    }
    
    public function ignoreDeleted($value = true)
    {
        $this->_parts[self::IGNORE_DELETED] = $value;
        return $this;
    }
}
