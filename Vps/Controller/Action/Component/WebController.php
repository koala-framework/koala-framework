<?php
class Vps_Controller_Action_Component_WebController extends Vps_Controller_Action
{
    public function preDispatch()
    {
        // Seite bearbeiten-Button
        /*
        if ($this->getRequest()->getModuleName() != 'admin') {
            $acl = Zend_Registry::get('acl');

            $pageId = $this->getRequest()->getParam('pageId');
            $url = $this->getRequest()->getParam('url');
            if ($pageId != '') {
                $pageCollection = Vps_PageCollection_Abstract::getInstance();
                $page = $pageCollection->getPageById($pageId);
                $path = $pageCollection->getUrl($page);
                $acl->add(new Vps_Acl_Resource('page', 'Aktuelle Seite betrachten', $path));
                $acl->allow('admin', 'page');
            } else if ($url != '') {
                $pageCollection = Vps_PageCollection_Abstract::getInstance();
                $page = $pageCollection->findPageByPath($url);
                if ($page) {
                    $acl->add(new Vps_Acl_Resource('page', 'Aktuelle Seite bearbeiten', '/admin/page?id=' . $page->getId()));
                    $acl->allow('admin', 'page');
                }
            } else {
                $pageId = 0;
            }

            Zend_Registry::set('acl', $acl);
        }
*/

    }

    public function indexAction()
    {
        $benchmark = Vps_Benchmark::getInstance();
        $benchmark->startSequence('Seitenbaum');

        $requestUrl = $this->getRequest()->getPathInfo();

        $tc = new Vps_Dao_TreeCache();
        $where = array();
        if ($tc->showInvisible()) {
            $where["url_match_preview = ?"] = $requestUrl;
        } else {
            $where["url_match = ?"] = $requestUrl;
        }
        $row = $tc->fetchAll($where)->current();
        if (!$row ) {
            if (!$tc->showInvisible()) {
                $where = array();
                $where["? LIKE url_pattern"] = $requestUrl;
                $where["? NOT LIKE CONCAT(url_pattern, '/%')"] = $requestUrl;
                $row = $tc->fetchAll($where)->current();
            }
            if (!$row) {
                throw new Vps_Controller_Action_Web_FileNotFoundException('Page not found for path ' . $this->getRequest()->getPathInfo());
            }
            if ($row->url_match != $requestUrl) {
                header('Location: '.$row->url_match);
                exit;
            }
        }
        $templateVars = $row->getComponent()->getTemplateVars();
        
        $this->view->url = $requestUrl;
        $this->view->component = $templateVars;

        $benchmark->stopSequence('Seitenbaum');
        $result = $benchmark->getResults();
        $this->view->time = sprintf("%01.2f", $result['Seitenbaum']['duration']);
    }

    public function postDispatch()
    {
        $role = $this->_getUserRole();
        $acl = $this->_getAcl();

        $config = array();
        $config['menu'] = false;
        $config['fe'] = false;

        /*
        if ($acl->isAllowed($role, 'pages')) {
            //$config['menu'] = true;
        }

        if ($acl->isAllowed($role, 'fe')) {
            $session = new Zend_Session_Namespace('admin');
            if ($session->mode == 'fe' || $this->getRequest()->getParam('fe')) {
                $this->view->mode = 'fe';
                $config['fe'] = true;
            }
        }
        */

        if ($config['menu'] || $config['fe']) {
            $this->view->ext('Vps.Component.Frontend.Index', $config);
        }
    }

}
