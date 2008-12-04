<?php
class Vpc_News_Directory_Admin extends Vpc_Directories_Item_Directory_Admin
{
    public function getExtConfig()
    {
        $detail = Vpc_Abstract::getChildComponentClass($this->_class, 'detail');
        $classes = Vpc_Abstract::getChildComponentClasses($detail, 'child');

        $generators = Vpc_Abstract::getSetting($detail, 'generators');
        $contentClass = $generators['child']['component']['content'];

        $plugins = array();
        foreach ($classes as $class) {
            $plugin = Vpc_Admin::getComponentFile(
                $class, 'Plugin', 'js', true
            );
            if ($plugin) {
                $plugins[] = str_replace('_', '.', $plugin);
            }
        }

        return array_merge(parent::getExtConfig(), array(
            'xtype'=>'vpc.news',
            'contentClass' => $contentClass,
            'componentPlugins' => $plugins,
            'idTemplate' => 'news_{0}-content'
        ));
    }

    public function setup()
    {
        $detail = Vpc_Abstract::getChildComponentClass($this->_class, 'detail');
        Vpc_Admin::getInstance($detail)->setup();

        if (!$this->_tableExists('vpc_news')) {
            Vps_Registry::get('db')->query("CREATE TABLE IF NOT EXISTS `vpc_news` (
  `id` smallint(6) NOT NULL auto_increment,
  `component_id` varchar(255) collate utf8_unicode_ci NOT NULL,
  `visible` tinyint(4) NOT NULL,
  `title` varchar(255) collate utf8_unicode_ci NOT NULL,
  `teaser` text collate utf8_unicode_ci NOT NULL,
  `publish_date` date NOT NULL,
  `expiry_date` date default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;");
        }
    }

    public function addResources(Vps_Acl $acl)
    {
        parent::addResources($acl);
        $components = Vps_Component_Data_Root::getInstance()
                ->getComponentsBySameClass($this->_class, array('ignoreVisible'=>true));
        $name = Vpc_Abstract::getSetting($this->_class, 'componentName');
        if (strpos($name, '.') !== false) $name = substr($name, strrpos($name, '.') + 1);

        if (count($components) > 1) {
            $acl->add(new Vps_Acl_Resource_MenuDropdown('vpc_news',
                        array('text'=>$name, 'icon'=>'newspaper.png')), 'vps_component_root');
            foreach ($components as $c) {
                $acl->add(new Vps_Acl_Resource_Component_MenuUrl($c,
                        array('text'=>$c->getTitle(), 'icon'=>'newspaper.png'),
                        '/admin/component/edit/'.$c->componentClass.'?componentId='.$c->dbId), 'vpc_news');
            }
        } else if (count($components) == 1) {
            $c = $components[0];
            $acl->add(new Vps_Acl_Resource_Component_MenuUrl($c,
                    array('text'=>$name, 'icon'=>'newspaper.png'),
                    '/admin/component/edit/'.$c->componentClass.'?componentId='.$c->dbId), 'vps_component_root');

        }
    }
}
