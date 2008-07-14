<?php
class Vpc_News_Directory_Admin extends Vpc_Admin
{
    public function getExtConfig()
    {
        $detail = Vpc_Abstract::getChildComponentClass($this->_class, 'detail');
        $classes = Vpc_Abstract::getChildComponentClasses($detail, 'child');
        $contentClass = $classes['content'];

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
            'componentPlugins' => $plugins
        ));
    }

    public function setup()
    {
        $detail = Vpc_Abstract::getChildComponentClass($this->_class, 'detail');
        Vpc_Admin::getInstance($detail)->setup();

        if (!$this->_tableExists('vpc_news')) {
            $this->_db->query("CREATE TABLE IF NOT EXISTS `vpc_news` (
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

    public function delete($componentId)
    {
        $detail = Vpc_Abstract::getChildComponentClass($this->_class, 'detail');
        Vpc_Admin::getInstance($detail)->delete($componentId);
    }
}
