<?php
class Vpc_Basic_ImageEnlarge_Admin extends Vpc_Abstract_Composite_Admin
{
    protected function _deleteCacheForRow($row)
    {
        parent::_deleteCacheForRow($row);
        if ($row instanceof Vpc_Abstract_Image_Row) {
            $id = $row->component_id;

            $enlargeChildId = '-linkTag';
            $c = Vpc_Abstract::getChildComponentClass($this->_class, 'child', 'linkTag');
            if (is_instance_of($c, 'Vpc_Basic_LinkTag_Component')) {
                $enlargeChildId .= '-link';
            }
            if (substr($id, -strlen($enlargeChildId)) == $enlargeChildId) {
                $id = substr($id, 0, -strlen($enlargeChildId));
            }
            $components = Vps_Component_Data_Root::getInstance()->getComponentsByDbId(
                $id, array('ignoreVisible' => true, 'componentClass'=>$this->_class)
            );
            foreach ($components as $c) {
                $cacheId = $c->componentClass.'_'.str_replace('-', '__', $c->dbId).'_default';
                Vps_Media::getOutputCache()->remove($cacheId);
            }
        }
    }
}
