<?php
class Vps_Component_Generator_Model extends Vps_Model_Abstract 
{
    protected $_constraints = array(
        'generator' => 'page',
        'ignoreVisible' => true
    );
    
    public function fetchAll($where=null, $order=null, $limit=null, $start=null)
    {
        $rowset = array();
        if (!$where['parent']) {
            $rowset[] = array(
                'component' => $this->_default,
                'class' => 'root'
            );
        } else {
            foreach (Vpc_Abstract::getSetting($where['parent'], 'generators', false) as $generator) {
                if (is_array($generator['component'])) {
                    foreach ($generator['component'] as $component => $class) {
                        $rowset[] = array(
                            'component' => $class,
                            'class' => $generator['class']
                        );
                    }
                } else {
                    $rowset[] = array(
                        'component' => $generator['component'],
                        'class' => $generator['class']
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
}