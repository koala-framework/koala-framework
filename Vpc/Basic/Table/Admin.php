<?php
class Vpc_Basic_Table_Admin extends Vpc_Admin
{
    public function setup()
    {
        $fields['columns'] = 'smallint(6) NOT NULL';
        $this->createFormTable('vpc_basic_table', $fields);

        $fields['columns'] = 'smallint(6) NOT NULL';
        $fields['rows'] = 'text NOT NULL';
        $this->createFormTable('vpc_basic_table', $fields);
    }

    public function getExtConfig()
    {
        $ret = array();

        $url = Vpc_Admin::getInstance($this->_class)->getControllerUrl('Settings');
        $icon = new Vps_Asset('wrench_orange');
        $ret['settings'] = array(
            'xtype' => 'vps.autoform',
            'controllerUrl' => $url,
            'title' => trlVps('Settings'),
            'icon' => $icon->__toString()
        );

        $url = Vpc_Admin::getInstance($this->_class)->getControllerUrl();
        $icon = new Vps_Asset('wrench');
        $ret['table'] = array(
            'xtype' => 'vps.autogrid',
            'controllerUrl' => $url,
            'title' => trlVps('Table'),
            'icon' => $icon->__toString()
        );

        return $ret;
    }
}
