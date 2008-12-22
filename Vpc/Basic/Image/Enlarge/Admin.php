<?php
class Vpc_Basic_Image_Enlarge_Admin extends Vpc_Basic_Image_Admin
{
    protected function _deleteCacheForRow($row)
    {
        parent::_deleteCacheForRow($row);
        if ($row instanceof Vpc_Basic_Image_Row) {
            $components = Vps_Component_Data_Root::getInstance()->getComponentsByDbId(
                $row->component_id, array('ignoreVisible' => true, 'componentClass'=>$this->_class)
            );
            foreach ($components as $c) {
                $small = $c->getChildComponent('-smallImage');
                if ($small) {
                    $cacheId = $small->componentClass.'_'.str_replace('-', '__', $c->dbId).'_default';
                    Vps_Media::getOutputCache()->remove($cacheId);
                }
            }
        }
    }
}
