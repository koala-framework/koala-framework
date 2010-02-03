<?php
class Vpc_Abstract_Composite_TabsAdmin extends Vpc_Abstract_Composite_Admin
{
    public function getExtConfig()
    {
        $classes = Vpc_Abstract::getChildComponentClasses($this->_class, 'child');

        $config = array(
            'title' => trlVps('Edit {0}', $this->_getSetting('componentName')),
            'icon' => $this->_getSetting('componentIcon')->__toString(),
            'activeTab' => 0,
            'xtype' => 'vps.tabpanel'
        );

        foreach ($classes as $id=>$cls) {
            $c = array_values(Vpc_Admin::getInstance($cls)->getExtConfig());
            foreach ($c as $i) {
                //TODO: hier nicht den titel als index verwenden, das stinkt
                $config['tabs'][$i['title']] = $i;
                $config['tabs'][$i['title']]['componentIdSuffix'] = '-'.$id;
            }
        }
        return array('tabs' => $config);
    }
}
