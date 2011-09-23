<?php
class Vpc_Basic_DownloadTag_Events extends Vpc_Abstract_Events
{
    public function onOwnRowUpdate(Vps_Component_Event_Row_Updated $event)
    {
        parent::onOwnRowUpdate($event);
        if ($event->isDirty('vps_upload_id')) {
            $cacheId = Vps_Media::createCacheId(
                $this->getData()->componentClass, $this->getData()->componentId, 'default'
            );
            Vps_Media::getOutputCache()->remove($cacheId);
        }
    }
}
