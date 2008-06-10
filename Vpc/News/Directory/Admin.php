<?php
class Vpc_News_Directory_Admin extends Vpc_Admin
{
    public function getExtConfig()
    {
        $classes = Vpc_Abstract::getSetting($this->_class, 'childComponentClasses');
        $classes = Vpc_Abstract::getSetting($classes['detail'], 'childComponentClasses');
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
        $classes = Vpc_Abstract::getSetting($this->_class, 'childComponentClasses');
        Vpc_Admin::getInstance($classes['detail'])->setup();

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
        $classes = Vpc_Abstract::getSetting($this->_class, 'childComponentClasses');
        Vpc_Admin::getInstance($classes['detail'])->delete($componentId);
    }
}
