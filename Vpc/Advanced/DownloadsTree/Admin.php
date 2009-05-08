<?php
class Vpc_Advanced_DownloadsTree_Admin extends Vpc_Admin
{
    public function addResources(Vps_Acl $acl)
    {
        parent::addResources($acl);
        $this->_addResourcesBySameClass($acl);
    }

    public function getExtConfig()
    {
        $ret = parent::getExtConfig();
        $ret['xtype'] = 'vpc.advanced.downloadstree';
        return $ret;
    }

    public function setup()
    {
        parent::setup();

        $sql = "
        CREATE TABLE IF NOT EXISTS `vpc_downloadstree_downloads` (
          `id` int(10) unsigned NOT NULL auto_increment,
          `project_id` int(10) unsigned NOT NULL,
          `text` varchar(200) NOT NULL,
          `vps_upload_id` int(10) NOT NULL,
          `visible` tinyint(4) NOT NULL,
          `date` date NOT NULL,
          PRIMARY KEY  (`id`),
          KEY `project_id` (`project_id`),
          KEY `vps_upload_id` (`vps_upload_id`)
        ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ;
        ";
        Vps_Registry::get('db')->query($sql);

        $sql = "
        CREATE TABLE IF NOT EXISTS `vpc_downloadstree_projects` (
          `id` int(10) unsigned NOT NULL auto_increment,
          `parent_id` int(10) unsigned default NULL,
          `component_id` varchar(200) NOT NULL,
          `pos` smallint(6) NOT NULL,
          `visible` tinyint(4) NOT NULL,
          `text` varchar(200) NOT NULL,
          PRIMARY KEY  (`id`),
          KEY `parent_id` (`parent_id`),
          KEY `component_id` (`component_id`),
          KEY `visible` (`visible`)
        ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ;
        ";
        Vps_Registry::get('db')->query($sql);

        $sql = "
        ALTER TABLE `vpc_downloadstree_downloads`
          ADD CONSTRAINT `vpc_downloadstree_downloads_ibfk_3` FOREIGN KEY (`project_id`) REFERENCES `vpc_downloadstree_projects` (`id`),
          ADD CONSTRAINT `vpc_downloadstree_downloads_ibfk_4` FOREIGN KEY (`vps_upload_id`) REFERENCES `vps_uploads` (`id`);
        ";
        Vps_Registry::get('db')->query($sql);

        $sql = "
        ALTER TABLE `vpc_downloadstree_projects`
          ADD CONSTRAINT `vpc_downloadstree_projects_ibfk_1` FOREIGN KEY (`parent_id`) REFERENCES `vpc_downloadstree_projects` (`id`);
        ";
        Vps_Registry::get('db')->query($sql);
    }
}
