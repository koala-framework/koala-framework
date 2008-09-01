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
        if (!$where['parent'] || $where['parent'] === '0') {
            $rowset[] = array(
                'component' => Vps_Component_Data_Root::getComponentClass(),
                'class' => 'root'
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
}