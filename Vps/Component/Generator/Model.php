<?php
class Vps_Component_Generator_Model extends Vps_Model_Abstract
{
    protected $_constraints = array(
        'pageGenerator' => true,
        'ignoreVisible' => true
    );

    public function fetchAll($where=null, $order=null, $limit=null, $start=null)
    {
        $rowset = array();
        if (!$where['parent']) {
            $rowset[] = array(
                'component' => Vps_Component_Data_Root::getComponentClass(),
                'class' => 'root',
                'name' => 'root'
            );
        } else {
            foreach (Vpc_Abstract::getSetting($where['parent'], 'generators', false) as $key => $generator) {
                if (is_array($generator['component'])) {
                    foreach ($generator['component'] as $component => $class) {
                        if ($class) {
                            $rowset[] = array(
                                'component' => $class,
                                'class' => $generator['class'],
                                'name' => $component
                            );
                        }
                    }
                } else if ($generator['class']) {
                    $rowset[] = array(
                        'component' => $generator['component'],
                        'class' => $generator['class'],
                        'name' => $key
                    );
                }
            }
        }
        return new $this->_rowsetClass(array(
            'data' => $rowset,
            'rowClass' => $this->_rowClass,
            'model' => $this
        ));
    }
    /**
     * Gibt den Komponenten-Baum aus (für CLI)
     **/
    public static function output()
    {
        $componentClass = Vps_Component_Data_Root::getComponentClass();
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
        throw new Vps_Exception("no unique identifier set");
    }
}