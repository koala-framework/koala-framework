<?php
class Vpc_Abstract_Composite_TabsAdmin extends Vpc_Abstract_Composite_Admin
{
    public function getExtConfig()
    {
        $classes = Vpc_Abstract::getSetting($this->_class, 'childComponentClasses');

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
        $classes = Vpc_Abstract::getSetting($this->_class, 'childComponentClasses');
        Vpc_Admin::getInstance($classes['text'])->delete($componentId . '-text');
        Vpc_Admin::getInstance($classes['images'])->delete($componentId . '-images');

        parent::delete($componentId);
    }
}
