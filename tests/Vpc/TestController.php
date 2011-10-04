<?php
/**
 * Controller der für eigene root-komponenten verwendet werden kann.
 * für selenium-tests.
 * Url: /vps/vpctest/Vpc_Basic_Text_Root/url
 */
class Vpc_TestController extends Vps_Controller_Action
{
    public function indexAction()
    {
        Zend_Registry::get('config')->debug->componentCache->disable = true;
        Zend_Registry::set('db', false);
        Vps_Test_SeparateDb::setDbFromCookie(); // setzt es nur wenn es das cookie wirklich gibt

        //FnF models setzen damit tests nicht in echte tabellen schreiben
        Vps_Component_Cache::setInstance(Vps_Component_Cache::CACHE_BACKEND_FNF);

        if (class_exists('APCIterator')) {
            $prefix = Vps_Cache::getUniquePrefix();
            apc_delete_file(new APCIterator('user', '#^'.$prefix.'#'));
        } else {
            apc_clear_cache('user');
        }
        Vps_Component_Data_Root::setComponentClass($this->_getParam('root'));
        $root = Vps_Component_Data_Root::getInstance();
        $root->setFilename('vps/vpctest/'.$this->_getParam('root'));

        $url = $this->_getParam('url');

        $urlParts = explode('/', $url);
        if (is_array($urlParts) && $urlParts[0] == 'media') {
            if (sizeof($urlParts) != 7) {
                throw new Vps_Exception_NotFound();
            }
            $class = $urlParts[1];
            $id = $urlParts[2];
            $type = $urlParts[3];
            $checksum = $urlParts[4];
            // time() wäre der 5er, wird aber nur wegen browsercache benötigt
            $filename = $urlParts[6];

            if ($checksum != Vps_Media::getChecksum($class, $id, $type, $filename)) {
                throw new Vps_Exception_AccessDenied('Access to file not allowed.');
            }
            Vps_Media_Output::output(Vps_Media::getOutput($class, $id, $type));
        }

        $domain = 'http://'.Zend_Registry::get('config')->server->domain;
        $data = $root->getPageByUrl($domain.'/'.$url, null);
        if (!$data) {
            throw new Vps_Exception_NotFound();
        }
        $root->setCurrentPage($data);
        $data->getComponent()->sendContent();

        $this->_helper->viewRenderer->setNoRender(true);
    }
}
