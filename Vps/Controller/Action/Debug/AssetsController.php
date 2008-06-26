<?php
class Vps_Controller_Action_Debug_AssetsController extends Vps_Controller_Action
{
    public function jsonClearAssetsCacheAction()
    {
        foreach (new DirectoryIterator('application/cache/assets') as $file) {
            if ($file->isFile()) {
                unlink($file->getPathname());
            }
        }
    }

    public function jsonSetDebugAssetsAction()
    {
        $params = $this->getRequest()->getParams();
        $sessionAssets = new Zend_Session_Namespace('debug');
        if (isset($params['js'])) {
            $sessionAssets->assetsJs = $params['js'];
        }
        if (isset($params['css'])) {
            $sessionAssets->assetsCss = $params['css'];
        }
        if (isset($params['autoClearCache'])) {
            $sessionAssets->autoClearAssetsCache = $params['autoClearCache'];
        }
    }
}
