<?php
class Vpc_Basic_Text_Controller extends Vps_Controller_Action_Auto_Vpc_Form
{
    public function jsonTidyHtmlAction()
    {
        $html = $this->_getParam('html');
        $html = preg_replace('#(<span\s+class\s*=\s*"?cursor"?\s*>)\s*(</span>)#is', '<span class="cursor">cursor</span>', $html);

        $row = $this->_form->getRow();
        $parser = new Vpc_Basic_Text_Parser();
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
        $pattern = Vpc_Abstract::getSetting($this->_getParam('class'), 'stylesIdPattern');

        if ($pattern) {
            if (preg_match('#'.$pattern.'#', $this->_getParam('componentId'), $m)) {
                $ownStyles = $m[0];
            }
        }

        $m = Vps_Model_Abstract::getInstance(Vpc_Abstract::getSetting($this->_getParam('class'), 'stylesModel'));
        $this->view->styles = $m->getStyles($ownStyles);
    }
}
