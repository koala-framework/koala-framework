<?php
class Vps_Controller_Action_Pages extends Vps_Controller_Action
{
    public function actionAction()
    {
        $view = new Vps_View_Smarty('../library/Vps/Controller/Action');
        $view->assign('expandedPath', $this->getRequest()->getParam('path'));
        $body = $view->render('Pages.html');

        $this->getResponse()->appendBody($body);
    }

    public function ajaxGetNodesAction()
    {
        $pageCollection = Vps_PageCollection_Abstract::getInstance();
        $pageCollection->setCreateDynamicPages(false);

        $expandedPath = $this->getRequest()->getParam('expandedPath');
        $ids = $pageCollection->getIdsForPath($expandedPath);

        $path = $this->getRequest()->getParam('node');
        if ($path == 'source') { $path = ''; }
        $path .= '/';

        $data = array();
        $parentPage = $pageCollection->getPageByPath($path);
        if ($parentPage instanceof Vps_Component_Interface) {
            foreach ($pageCollection->getChildPages($parentPage) as $page) {
                $d['text'] = $pageCollection->getPath($page);
                $d['id'] = $pageCollection->getPath($page);
                $d['leaf'] = false;
                if (sizeof($pageCollection->getChildPages($page)) > 0) {
                    if (in_array($page->getId(), $ids)) {
                        $d['expanded'] = true;
                    } else {
                        $d['expanded'] = false;
                    }
                } else {
                    $d['expanded'] = true;
                }
                $d['cls'] = 'file';
                $data[] = $d;
            }
        }
        $this->getResponse()->setBody(Zend_Json::encode($data));
    }

}
