<?php
class Vps_Controller_Action_Component_Media extends Vps_Controller_Action_Media
{
/*
    public function vpcAction()
    {
        $id = $this->_getParam('componentId');
        $class = $this->_getParam('class');
        $tablename = Vpc_Abstract::getSetting($class, 'tablename');
        $table = new $tablename();
        $row = $table->findRow($id);
        $this->cacheAction($row->vps_upload_id);
    }
*/
    protected function _createChecksum($password)
    {
        return md5($password . $this->_getParam('componentId'));
    }

    protected function _getCacheFilename()
    {
        $id = $this->_getParam('componentId');
        $filename = $this->_getParam('filename');
        if ($this->_getParam('class')) {
            $extra = '.' . $filename;
        } else {
            $parts = explode('.', $filename);
            $extra = sizeof($parts) == 3 ? '.' . $parts[1] : '';
        }
        return $id . $extra;
    }

    protected function _createCacheFile($source, $target)
    {
        $id = $this->_getParam('componentId');
        $class = $this->_getParam('class');
        /*
        if (!$class) {
            $pageCollection = Vps_PageCollection_TreeBase::getInstance();
            $class = get_class($pageCollection->findComponent($id));
        }
        */
        $tablename = Vpc_Abstract::getSetting($class, 'tablename');
        $table = new $tablename();
        $row = $table->findRow($id);
        $row->createCacheFile($class, $source, $target);
    }
}