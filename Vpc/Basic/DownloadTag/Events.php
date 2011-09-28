<?php
class Vpc_Basic_DownloadTag_Events extends Vpc_Abstract_Events
{
    public function onOwnRowUpdate(Vps_Component_Event_Row_Updated $event)
    {
        parent::onOwnRowUpdate($event);
        if ($event->isDirty('vps_upload_id')) {
            $components = Vps_Component_Data_Root::getInstance()
                ->getComponentsByDbId($event->row->component_id);
            foreach ($components as $component) {
                Vps_Media::getOutputCache()->remove(Vps_Media::createCacheId(
                    $component->componentClass, $component->componentId, 'default'
                ));
            }
        }
    }
}
