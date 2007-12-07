<?php
class Vps_Controller_Action_Welcomemedia  extends Vps_Controller_Action_Media
{
    public function indexAction()
    {
        $request = $this->getRequest();
        $t = new Vps_Dao_Welcome();
        $row = $t->find(1)->current();
        $request->setParam('uploadId', $row->vps_upload_id);
        $this->cacheAction();
    }
    protected function _createCacheFile($source, $target, $type)
    {
        Vps_Media_Image::scale($source, $target, array(300, 100));
    }

    protected function _getCacheFilename()
    {
        return 'welcome';
    }
}
