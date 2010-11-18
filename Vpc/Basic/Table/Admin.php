<?php
class Vpc_Basic_Table_Admin extends Vpc_Admin
{
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

    public function duplicate($source, $target)
    {
        parent::duplicate($source, $target);
        if ($model = $source->getComponent()->getChildModel()) {
            $rows = $model->getRows($model->select()
                ->whereEquals('component_id', $source->dbId)
            );
            if ($rows) {
                foreach ($rows as $row) {
                    $newRow = $row->duplicate(array(
                        'component_id' => $target->dbId
                    ));
                }
            }
        }
    }
}
