<?php
class Vpc_Basic_LinkTag_CommunityVideo_Admin extends Vpc_Basic_LinkTag_Abstract_Admin
{
    public function getLinkTagForms()
    {
        $ret = array();
        $title = Vpc_Abstract::getSetting($this->_class, 'componentName');
        $title = str_replace('.', ' ', $title);
        $form = Vpc_Abstract_Form::createChildComponentForm('Vpc_Basic_LinkTag_CommunityVideo_Component', 'video');
        $form->setIdTemplate('{0}-link-video');
        $ret[] = array(
            'form' => $form,
            'title' => $title,
        );
        return $ret;
    }
}
