<?php
class Vpc_Basic_LinkTag_Abstract_Admin extends Vpc_Admin
{
    public function getLinkTagForms()
    {
        $ret = array();
        $title = Vpc_Abstract::getSetting($this->_class, 'componentName');
        $title = str_replace('.', ' ', $title);
        $ret[] = array(
            'form' => Vpc_Abstract_Form::createComponentForm($this->_class, 'link'),
            'title' => $title,
        );
        return $ret;
    }
}
