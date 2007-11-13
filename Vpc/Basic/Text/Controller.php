<?php
class Vpc_Basic_Text_Controller extends Vps_Controller_Action_Auto_Vpc_Form
{
    public function _initFields()
    {
        $field = new Vps_Auto_Field_HtmlEditor('content', 'Content');
        $field->setData(new Vps_Auto_Data_Vpc_ComponentIds('content'));

        $ignoreSettings = array('tablename', 'componentName', 'imageClass', 'linkClass', 'default');
        foreach (call_user_func(array($this->class, 'getSettings')) as $key => $val) {
            if (!in_array($key, $ignoreSettings)) {
                $method = 'set' . ucfirst($key);
                $field->$method($val);
            }
        }
        $class = Vpc_Abstract::getSetting($this->class, 'linkClass');
        $field->setLinkComponentConfig(Vpc_Admin::getConfig($class));
        $class = Vpc_Abstract::getSetting($this->class, 'imageClass');
        $field->setImageComponentConfig(Vpc_Admin::getConfig($class));

        $field->setControllerUrl(Vpc_Admin::getInstance($this->class)->getControllerUrl());

        $this->_form->add($field);
    }

    protected function _beforeSave(Zend_Db_Table_Row_Abstract $row)
    {
        $row->content = $this->_tidy($row->content);
        $row->content_edit = '';
        parent::_beforeSave($row);
    }

    public function jsonTidyHtmlAction()
    {
        $html = $this->_getParam('html');

        $html = preg_replace('#(<o:p.*>)#', '', $html);

        $stripProps = array('style', 'class', 'width', 'valign');
        foreach ($stripProps as $i) {
            $html = preg_replace('#(<.+)'.preg_quote($i).'="[^"]*"(.*>)#', '\\1\\2', $html);
        }
        $this->view->html = $this->_tidy($html);
    }

    private function _tidy($html)
    {
        $config = array(
                    'indent'         => true,
                    'output-xhtml'   => true,
                    'clean'          => true,
                    'wrap'           => 200,
                    'doctype'        => 'omit',
                    'drop-proprietary-attributes' => true,
                    'drop-font-tags' => true,
                    'word-2000'      => true,
                    'show-body-only' => true,
                    'bare'           => true,
                    'enclose-block-text'=>true,
                    'enclose-text'   => true,
                    'join-styles'    => false,
                    'logical-emphasis' => true,
                    'lower-literals' => true,
                    'output-bom'     => false,
                    'char-encoding'  =>'utf8',
                    'newline'        =>'LF'
                    );
        $tidy = new tidy;
        $tidy->parseString($html, $config, 'utf8');
        $tidy->cleanRepair();
        return $tidy->value;
    }

    public function jsonAddImageAction()
    {
        $row = $this->_form->getRow();
        $this->view->page_id = $row->page_id;
        $nrs = array_merge($row->getTypeChildComponentNrs('image', $row->content),
                    $row->getTypeChildComponentNrs('image', $row->content_edit));
        if ($nrs) {
            $nr = max($nrs);
        } else {
            $nr = 0;
        }
        $this->view->component_key = $row->component_key.'-i'.($nr+1);
        $imageClass = Vpc_Abstract::getSetting($this->class, 'imageClass');
        $row->content_edit .= "<img src=\"/media/0/$imageClass/{$this->view->page_id}{$this->view->component_key}/\" />";
        $row->save();
    }
    public function jsonAddLinkAction()
    {
        $row = $this->_form->getRow();
        $this->view->page_id = $row->page_id;
        $nrs = array_merge($row->getTypeChildComponentNrs('link', $row->content),
                    $row->getTypeChildComponentNrs('link', $row->content_edit));
        if ($nrs) {
            $nr = max($nrs);
        } else {
            $nr = 0;
        }
        $this->view->component_key = $row->component_key.'-l'.($nr+1);
        $row->content_edit .= "<a href=\"{$this->view->page_id}{$this->view->component_key}\" />";
        $row->save();
    }
}
