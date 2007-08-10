<?php
class Vpc_Simple_Image_Setup extends Vpc_Setup_Abstract
{
    public function setup()
    {
        $fields['file_name'] = 'varchar(255) NOT NULL';
        $fields['text'] = 'varchar(255) NOT NULL';
        $fields['width'] = 'int(11) NOT NULL';
        $fields['height'] = 'int(11) NOT NULL';
        $fields['style'] = 'varchar(255) NOT NULL';
        $fields['color'] = 'varchar(255) NOT NULL';
        $fields['vps_upload_id'] = 'int(11) NOT NULL';

        $this->createTable('component_simple_image', $fields);

        $tablename = 'vps_uploads';
        if (!$this->_tableExits($tablename)) {
	        $this->_db->query("CREATE TABLE `$tablename` (
							  `id` int(10) unsigned NOT NULL auto_increment,
  							  `path` varchar(255) NOT NULL,
							  PRIMARY KEY  (`id`)
							) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;");
	   }
    }
}
