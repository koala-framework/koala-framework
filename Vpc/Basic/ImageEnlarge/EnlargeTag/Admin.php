<?php
class Vpc_Basic_ImageEnlarge_EnlargeTag_Admin extends Vpc_Abstract_Composite_Admin
{
    protected function _deleteCacheForRow($row)
    {
        parent::_deleteCacheForRow($row);
        if ($row instanceof Vpc_Abstract_Image_Row) {
            $components = Vps_Component_Data_Root::getInstance()->getComponentsByDbId(
                $row->component_id, array('ignoreVisible' => true)
            );
            foreach ($components as $c) {
                if (is_instance_of($c->componentClass, 'Vpc_Basic_ImageEnlarge_Component')) {
                    $c = $c->getChildComponent('-linkTag');
                    if (is_instance_of($c->componentClass, 'Vpc_Basic_LinkTag_Component')) {
                        $c = $c->getChildComponent('-link');
                    }
                    if ($c->componentClass == $this->_class) {
                        $cacheId = $c->componentClass.'_'.str_replace('-', '__', $c->dbId).'_default';
                        Vps_Media::getOutputCache()->remove($cacheId);
                        if (Vpc_Abstract::getSetting($this->_class, 'fullSizeDownloadable')) {
                            $cacheId = $c->componentClass.'_'.str_replace('-', '__', $c->dbId).'_original';
                            Vps_Media::getOutputCache()->remove($cacheId);
                        }
                    }
                }
            }
        }
    }
}
