<?php
class Vpc_Basic_LinkTag_Intern_PagesController extends Vps_Controller_Action_Component_PagesController
{
    protected function _isAllowedComponent()
    {
        return true;
    }
/*
    private $_pc;

    public function init()
    {
        $this->_pc = Vps_PageCollection_TreeBase::getInstance(false);
        parent::init();
    }

    public function jsonDataAction()
    {
        $id = $this->getRequest()->getParam('node');
        if ($id === '0') {

            parent::jsonDataAction();

        } else {

            $this->_openedNodes = array();
            $openedId = $this->_getParam('openedId');
            while ($openedId) {
                $this->_openedNodes[$openedId] = true;
                $page = $this->_pc->getPageById($openedId);
                if ($page) {
                    $page = $this->_pc->getParentPage($page);
                }
                $openedId = $page ? $page->getId() : null;
            }

            if ((int)$id == 0) {
                $type = $id;
                $page = null;
            } else {
                $type = null;
                $page = $this->_pc->getPageById($id);
            }
            p($page->getId());

            $this->_pc->overwriteGetUrl = false;

            $childPages = $this->_pc->getChildPages($page, $type);
            d(sizeof($childPages));
            $nodes = array();
            foreach ($childPages as $page) {
                $nodes[] = $this->_formatNode($page);
            }
            $this->view->nodes = $nodes;
        }

    }

    protected function _formatNode($page)
    {
        $id = $page->getId();
        $data = array();
        $data['id'] = $id;
        $data['text'] = $this->_pc->getName($page);
        $data['data'] = $this->_pc->getPageData($page);
        $data['leaf'] = false;
        $data['visible'] = true;
        $data['bIcon'] = $this->_icons['default'];
        $openedNodes = $this->_saveSessionNodeOpened(null, null);
        if (sizeof($this->_pc->getChildpages($page)) > 0) {
            if (isset($this->_openedNodes[$id])) {
                $data['expanded'] = true;
            } else {
                $data['expanded'] = false;
            }
        } else {
            $data['children'] = array();
            $data['expanded'] = true;
        }
        $data['uiProvider'] = 'Vps.Component.PagesNode';
        return $data;
    }
    */
}
