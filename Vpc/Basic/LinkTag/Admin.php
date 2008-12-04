<?php
class Vpc_Basic_LinkTag_Admin extends Vpc_Admin
{
    public function setup()
    {
        $classes = Vpc_Abstract::getChildComponentClasses($this->_class, 'link');
        foreach ($classes as $class) {
            Vpc_Admin::getInstance($class)->setup();
        }

        $fields['component'] = "VARCHAR(255) NOT NULL";
        $this->createFormTable('vpc_basic_linktag', $fields);
    }
    public function onRowUpdate($row)
    {
        parent::onRowUpdate($row);
        if ($row instanceof Vpc_Basic_LinkTag_Row) {
            Vps_Component_Cache::getInstance()->remove(
                Vps_Component_Data_Root::getInstance()
                    ->getComponentsByDbId($row->component_id.'-link', array(
                        'ignoreVisible' => true)
                    )
            );
        }
    }
}
