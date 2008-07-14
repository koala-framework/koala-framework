<?php
class Vpc_Abstract_Composite_TabsAdmin extends Vpc_Abstract_Composite_Admin
{
    public function getExtConfig()
    {
        $classes = Vpc_Abstract::getChildComponentClasses($this->_class, 'child');

        $config = parent::getExtConfig();

        foreach ($classes as $id=>$cls) {
            $c = Vpc_Admin::getInstance($cls)->getExtConfig();
            $config['tabs'][$c['componentName']] = $c;
            $config['tabs'][$c['componentName']]['componentIdSuffix'] = '-'.$id;
        }
        $config['activeTab'] = '0';
        $config['xtype'] = 'vps.tabpanel';

        return $config;
    }

    public function delete($componentId)
    {
        $classes = Vpc_Abstract::getChildComponentClasses($this->_class, 'child');
        foreach ($classes as $key => $class) {
            Vpc_Admin::getInstance($class)->delete($componentId . '-' . $key);
        }
        parent::delete($componentId);
    }
}
