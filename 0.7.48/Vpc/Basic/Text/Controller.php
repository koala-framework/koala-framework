<?php
class Vpc_Basic_Text_Controller extends Vps_Controller_Action_Auto_Vpc_Form
{
    protected $_formName = 'Vpc_Basic_Text_Form';

    public function jsonTidyHtmlAction()
    {
        $html = $this->_getParam('html');
        $row = $this->_form->getRow();
        $row->content_edit = $row->tidy($html);
        $row->save();
        $this->view->html = $row->content_edit;
    }

    public function jsonAddImageAction()
    {
        $classes = Vpc_Abstract::getSetting($this->class, 'childComponentClasses');

        $row = $this->_form->getRow();
        $this->view->component_id = $row->component_id.'-i'.
                                        ($row->getMaxChildComponentNr('image')+1);
        $imageClass = Vpc_Abstract::getSetting($this->class, 'imageClass');
        $row->content_edit .= "<img src=\"/media/$classes[image]/{$this->view->component_id}/\" />";
        $row->save();
    }
    public function jsonAddLinkAction()
    {
        $row = $this->_form->getRow();
        $this->view->component_id = $row->component_id.'-l'.
                                        ($row->getMaxChildComponentNr('link')+1);
        $row->content_edit .= "<a href=\"{$this->view->component_id}\" />";
        $row->save();
    }
    public function jsonAddDownloadAction()
    {
        $row = $this->_form->getRow();
        $this->view->component_id = $row->component_id.'-d'.
                                        ($row->getMaxChildComponentNr('download')+1);
        $row->content_edit .= "<a href=\"{$this->view->component_id}\" />";
        $row->save();
    }
}
