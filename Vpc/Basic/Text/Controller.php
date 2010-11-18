<?php
class Vpc_Basic_Text_Controller extends Vps_Controller_Action_Auto_Vpc_Form
{
    public function jsonTidyHtmlAction()
    {
        $html = $this->_getParam('html');
        $row = $this->_form->getRow();
        $this->view->html = $row->tidy($html);
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
        Zend_Registry::get('db')->beginTransaction();
        $childCompnentRow = $row->addChildComponentRow($type);
        $this->view->componentId = $row->component_id.'-'.substr($type, 0, 1).$childCompnentRow->nr;
        Zend_Registry::get('db')->commit();
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
        $styles = $m->getStyles($ownStyles);
        $this->view->inlineStyles = $styles['inline'];
        $this->view->blockStyles = $styles['block'];
    }
}
