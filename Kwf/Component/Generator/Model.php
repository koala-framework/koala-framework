<?php
class Kwf_Component_Generator_Model extends Kwf_Model_Abstract
{
    protected $_rowClass = 'Kwf_Model_Row_Data_Abstract';
    protected $_rowsetClass = 'Kwf_Component_Generator_Model_Rowset';
    
    protected $_constraints = array(
        'pageGenerator' => true,
        'ignoreVisible' => true
    );

    public function getPrimaryKey() {
        return 'id';
    }
    
    public function getColumns() {
        return array();
    }
    
    public function getRows($where=null, $order=null, $limit=null, $start=null)
    {
        $rowset = array();
        if ($where->getPart('whereNull') && in_array('parent_id', $where->getPart('whereNull'))) {
            $rowset[] = array(
                'component' => Kwf_Component_Data_Root::getComponentClass(),
                'class' => 'root',
                'name' => 'root'
            );
        } else {
            $equals = $where->getPart('whereEquals');
            $parent = $equals['parent_id'];
            foreach (Kwc_Abstract::getSetting($parent, 'generators', false) as $key => $generator) {
                foreach ($generator['component'] as $component => $class) {
                    $rowset[] = array(
                        'component' => $class,
                        'class' => $generator['class'],
                        'name' => $component
                    );
                }
            }
        }
        
        return new $this->_rowsetClass(array(
            'model' => $this,
            'dataKeys' => $rowset
        ));
    }
    
    public function getRowByDataKey($row)
    {
        $key = $row['component'];
        if (!isset($this->_rows[$key])) {
            $this->_rows[$key] = new $this->_rowClass(array(
                'data' => $row,
                'model' => $this
            ));
        }
        return $this->_rows[$key];
    }

    /**
     * Gibt den Komponenten-Baum aus (für CLI)
     **/
    public static function output()
    {
        $componentClass = Kwf_Component_Data_Root::getComponentClass();
        $model = new self();

        $maxComponentLength = self::_maxComponentLength($componentClass, $model);

        self::_output(null, $model, $maxComponentLength);
    }
    /**
     * Gibt den Komponenten-Baum aus (für CLI)
     **/
    private static function _output($componentClass, $model, $maxComponentLength)
    {
        static $stack = array();
        array_push($stack, $componentClass);
        $rows = $model->fetchAll(array('parent' => $componentClass));
        foreach ($rows as $row) {
            echo str_repeat(' ', count($stack)*2).$row->component;
            echo str_repeat(' ', $maxComponentLength-strlen($row->component));
            echo " ($row->name)";
            if (in_array($row->component, $stack)) {
                echo " (--recursion--)\n";
            } else {
                echo "\n";
                self::_output($row->component, $model, $maxComponentLength);
            }
        }
        array_pop($stack);
    }
    private static function _maxComponentLength($componentClass, $model)
    {
        static $stack = array();
        array_push($stack, $componentClass);
        $ret = 0;
        $rows = $model->fetchAll(array('parent' => $componentClass));
        foreach ($rows as $row) {
            if (!in_array($row->component, $stack)) {
                $ret = max($ret, strlen($row->component)+count($stack)*2);
            }
        }
        array_pop($stack);
        return $ret;
    }

    public function getUniqueIdentifier()
    {
        throw new Kwf_Exception("no unique identifier set");
    }
    
}
