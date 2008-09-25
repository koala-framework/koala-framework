<?php
class Vpc_Forum_Thread_Preview_Admin extends Vpc_Admin
{
    protected function _onRowAction($row)
    {
        parent::_onRowAction($row);
        if ($row instanceof Vpc_Posts_Directory_Row) {
            $posts =  Vps_Component_Data_Root::getInstance()
                ->getComponentsByDbId($row->component_id);
            foreach ($posts as $p) {
                $preview = $p->parent->getChildComponent('-preview');
                if ($preview && $preview->componentClass == $this->_class) {
                    Vps_Component_Cache::getInstance()->remove($preview);
                }
            }
        }
    }
}
