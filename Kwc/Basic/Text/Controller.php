<?php
class Kwc_Basic_Text_Controller extends Kwf_Controller_Action_Auto_Kwc_Form
{
    public function jsonTidyHtmlAction()
    {
        $html = $this->_getParam('html');
        $html = preg_replace('#(<span\s+class\s*=\s*"?cursor"?\s*>)\s*(</span>)#is', '<span class="cursor">cursor</span>', $html);

        $row = $this->_form->getRow();
        $parser = new Kwc_Basic_Text_Parser($row->component_id, $row->getModel());
        $m = Kwf_Model_Abstract::getInstance(Kwc_Abstract::getSetting($this->_getParam('class'), 'stylesModel'));
        $parser->setMasterStyles($m->getMasterStyles());
        if ($this->_getParam('allowCursorSpan')) {
            $parser->setEnableCursorSpan(true);
        }
        $html = $row->tidy($html, $parser);
        $html = preg_replace('#(<span\s+class\s*=\s*"?cursor"?\s*>)\s*cursor\s*(</span>)#is', '<span class="cursor"></span>', $html);
        $this->view->html = $html;
    }

    public function jsonAddImageAction()
    {
        $this->_addChildComponent('image');
    }
    public function jsonAddLinkAction()
    {
        $this->_addChildComponent('link');
    }
    public function jsonAddDownloadAction()
    {
        $this->_addChildComponent('download');
    }
    private function _addChildComponent($type)
    {
        $row = $this->_form->getRow();
        if (Zend_Registry::get('db')) Zend_Registry::get('db')->beginTransaction();
        $childCompnentRow = $row->addChildComponentRow($type);
        $this->view->componentId = $row->component_id.'-'.substr($type, 0, 1).$childCompnentRow->nr;
        if (Zend_Registry::get('db')) Zend_Registry::get('db')->commit();
    }

    public function jsonStylesAction()
    {
        $ownStyles = false;
        $pattern = Kwc_Abstract::getSetting($this->_getParam('class'), 'stylesIdPattern');

        if ($pattern) {
            if (preg_match('#'.$pattern.'#', $this->_getParam('componentId'), $m)) {
                $ownStyles = $m[0];
            }
        }

        $m = Kwf_Model_Abstract::getInstance(Kwc_Abstract::getSetting($this->_getParam('class'), 'stylesModel'));
        $this->view->styles = $m->getStyles($ownStyles);
    }

    protected function _isAllowedComponent()
    {
        $actionName = $this->getRequest()->getActionName();
        if ($actionName == 'styles-content') return true;
        return parent::_isAllowedComponent();
    }
    public function stylesContentAction()
    {
        $this->_helper->viewRenderer->setNoRender();
        $c = Kwc_Basic_Text_StylesModel::getStylesContents(Kwc_Abstract::getSetting($this->_getParam('class'), 'stylesModel'));
        $this->getResponse()->setBody($c);
    }
}
