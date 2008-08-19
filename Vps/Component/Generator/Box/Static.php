<?php
class Vps_Component_Generator_Box_Static extends Vps_Component_Generator_Static implements Vps_Component_Generator_Box_Interface
{
    protected function _acceptKey($key, $constraints, $parentData)
    {
        $ret = parent::_acceptKey($key, $constraints, $parentData);
        if ($ret && isset($constraints['inherit'])) {
            $ret = !isset($this->_settings['inherit']) || ($this->_settings['inherit'] == $constraints['inherit']);
        }
        return $ret;
    }
    
    protected function _formatConfig($parentData, $key)
    {
        $ret = parent::_formatConfig($parentData, $key);
        $c = $this->_settings;
        $ret['priority'] = isset($c['priority']) ? $c['priority'] : 0;
        $ret['inherit'] = !isset($c['inherit']) || $c['inherit'];
        $ret['box'] = isset($c['box']) ? $c['box'] : $key;
        $ret['multi'] = isset($c['multi']) ? $c['multi'] : false;
        return $ret;
    }
    
    public function getChildData($parentData, $constraints = array())
    {
        if (isset($this->_settings['unique'])) {
            $component = $parentData;
            while ($component && $component->componentClass != $this->_class) {
                if ($component->componentClass != $this->_class) {
                    foreach ($component->getRecursiveChildComponents(array('page' => false, 'box' => true)) as $c) {
                        if ($c->getParent()->componentClass == $this->_class) {
                            $component = $c->getParent();
                        }
                    }
                }
                if ($component->componentClass != $this->_class) {
                    $component = $component->getParentPage();
                }
            }
            if ($component) {
                $parentData = $component;
            } else {
                $box = $this->_settings['generator'];
                throw new Vps_Exception("Couldn't find unique box '$box'");
            }
        }
        return parent::getChildData($parentData, $constraints);
    }
}
