<?php
class Vpc_Basic_Text_Controller extends Vps_Controller_Action_Auto_Vpc_Form
{
    protected $_formName = 'Vpc_Basic_Html_Form';
    
    public function jsonTidyHtmlAction()
    {
        $html = $this->_getParam('html');

        $html = preg_replace('#(<o:p.*>)#', '', $html);

        $stripProps = array('style', 'class', 'width', 'valign');
        foreach ($stripProps as $i) {
            $html = preg_replace('#(<.+)'.preg_quote($i).'="[^"]*"(.*>)#', '\\1\\2', $html);
        }
        $row = $this->_form->getRow();

        $row->content_edit = $row->tidy($html);
        $row->save();
        $this->view->html = $row->content_edit;
    }

    public function jsonAddImageAction()
    {
        $classes = Vpc_Abstract::getSetting($this->class, 'childComponentClasses');

        $row = $this->_form->getRow();
        $this->view->page_id = $row->page_id;
        $this->view->component_key = $row->component_key.'-i'.
                                        ($row->getMaxChildComponentNr('image')+1);
        $imageClass = Vpc_Abstract::getSetting($this->class, 'imageClass');
        $row->content_edit .= "<img src=\"/media/0/$classes[image]/{$this->view->page_id}{$this->view->component_key}/\" />";
        $row->save();
    }
    public function jsonAddLinkAction()
    {
        $row = $this->_form->getRow();
        $this->view->page_id = $row->page_id;
        $this->view->component_key = $row->component_key.'-l'.
                                        ($row->getMaxChildComponentNr('link')+1);
        $row->content_edit .= "<a href=\"{$this->view->page_id}{$this->view->component_key}\" />";
        $row->save();
    }
    public function jsonAddDownloadAction()
    {
        $row = $this->_form->getRow();
        $this->view->page_id = $row->page_id;
        $this->view->component_key = $row->component_key.'-d'.
                                        ($row->getMaxChildComponentNr('download')+1);
        $row->content_edit .= "<a href=\"{$this->view->page_id}{$this->view->component_key}\" />";
        $row->save();
    }
}
