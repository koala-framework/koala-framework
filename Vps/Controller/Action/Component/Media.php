<?php
class Vps_Controller_Action_Component_Media extends Vps_Controller_Action_Media
{
    public function originalAction()
    {
        $acl = $this->_getAcl();
        $role = $this->_getUserRole();
        if (!$acl->isAllowed($role, 'mediaoriginal')) {
            throw new Vps_Controller_Action_Web_Exception('Access to file not allowed.');
        }
        parent::originalAction();
    }

    protected function _createChecksum($password)
    {
        return md5($password . $this->_getParam('componentId'));
    }

    protected function _getCacheFilename()
    {
        $id = $this->_getParam('componentId');
        $filename = $this->_getParam('filename');
        $parts = explode('.', $filename);
        $extra = sizeof($parts) == 3 ? '.' . $parts[1] : '';
        return $id . $extra;
    }

    protected function _createCacheFile($source, $target)
    {
        $id = $this->_getParam('componentId');

        $pageCollection = Vps_PageCollection_TreeBase::getInstance();
        $component = $pageCollection->findComponent($id);

        if ($component instanceof Vpc_FileInterface) {
            $component->createCacheFile($source, $target);
        }
    }
}