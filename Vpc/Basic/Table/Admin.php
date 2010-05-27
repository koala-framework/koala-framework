<?php
class Vpc_Basic_Table_Admin extends Vpc_Admin
{
    public function setup()
    {
        $fields['columns'] = 'smallint(6) NOT NULL';
        $fields['data'] = 'text NOT NULL';
        $this->createFormTable('vpc_basic_table', $fields);

        // die hier braucht id als primary
        $tablename = 'vpc_basic_table_data';
        if (!$this->_tableExists($tablename)) {
            $sql = "CREATE TABLE IF NOT EXISTS `$tablename` (
              `id` int(11) NOT NULL auto_increment,
              `component_id` varchar(255) NOT NULL,
              `data` text NOT NULL,
              `pos` int(11) NOT NULL,
              PRIMARY KEY  (`id`),
              KEY `component_id` (`component_id`)
            )";
            Vps_Registry::get('db')->query($sql);
        }
    }
    public function getExtConfig()
    {
        $ret = array();

        $url = Vpc_Admin::getInstance($this->_class)->getControllerUrl();
        $icon = new Vps_Asset('wrench');
        $ret['table'] = array(
            'xtype' => 'vps.autogrid',
            'controllerUrl' => $url,
            'title' => trlVps('Table'),
            'icon' => $icon->__toString()
        );

        $url = Vpc_Admin::getInstance($this->_class)->getControllerUrl('Settings');
        $icon = new Vps_Asset('wrench_orange');
        $ret['settings'] = array(
            'xtype' => 'vps.autoform',
            'controllerUrl' => $url,
            'title' => trlVps('Settings'),
            'icon' => $icon->__toString()
        );

        return $ret;
    }
}
