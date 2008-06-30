<?php
class Vpc_Basic_Text_Controller extends Vps_Controller_Action_Auto_Vpc_Form
{
    protected $_formName = 'Vpc_Basic_Text_Form';

    public function jsonTidyHtmlAction()
    {
        $html = $this->_getParam('html');
        $row = $this->_form->getRow();
        $this->view->html = $row->getRow()->tidy($html);
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
        $nr = $row->getRow()->getMaxChildComponentNr($type)+1;
        $this->view->component_id = $row->component_id.'-'.substr($type, 0, 1).$nr;

        $t = new Vpc_Basic_Text_ChildComponentsModel();
        $r = $t->createRow();
        $r->component_id = $row->component_id;
        $r->component = $type;
        $r->nr = $nr;
        $r->saved = 0;
        $r->save();
        Zend_Registry::get('db')->commit();
    }

    public function jsonStylesAction()
    {
        $ownStyles = false;
        $pattern = Vpc_Abstract::getSetting($this->class, 'stylesIdPattern');

        if ($pattern) {
            if (preg_match('#'.$pattern.'#', $this->_getParam('componentId'), $m)) {
                $ownStyles = $m[0];
            }
        }

        $t = new Vpc_Basic_Text_StylesModel();
        $styles = $t->getStyles($ownStyles);
        $this->view->inlineStyles = $styles['inline'];
        $this->view->blockStyles = $styles['block'];
    }
}
