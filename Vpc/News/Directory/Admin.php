<?php
class Vpc_News_Directory_Admin extends Vpc_Directories_Item_Directory_Admin
{

    public function getExtConfig()
    {
        $ret = parent::getExtConfig();

        $detail = Vpc_Abstract::getChildComponentClass($this->_class, 'detail');
        $contentClass = Vpc_Abstract::getChildComponentClass($detail, 'child', 'content');

        $ret['items']['idTemplate'] = 'news_{0}-content';
        $ret['items']['contentClass'] = $contentClass;
        $ret['items']['componentPlugins'] = $this->_getChildComponentPlugins(array($detail, $this->_class));
        $ret['items']['componentConfigs'] = array();
        $ret['items']['contentEditComponents'] = array();
        $cfg = Vpc_Admin::getInstance($contentClass)->getExtConfig();
        foreach ($cfg as $k=>$c) {
            $ret['items']['componentConfigs'][$contentClass.'-'.$k] = $c;
            $ret['items']['contentEditComponents'][] = array(
                'componentClass' => $contentClass,
                'type' => $k
            );
        }
        $cfgKeys = array_keys($cfg);
        $ret['items']['contentType'] = $cfgKeys[0];
        return $ret;
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
        $this->_addResourcesBySameClass($acl);
    }
}
