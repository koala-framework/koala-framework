<?php
class Vpc_Basic_LinkTag_Lightbox_Admin extends Vpc_Basic_LinkTag_Abstract_Admin
{
    // wird bei linklist verwendet, damit url richtig ausgegeben wird
    public function componentToString($data)
    {
        return trlVps('Lightbox');
    }

    public function getCardForms()
    {
        $ret = array();
        $title = Vpc_Abstract::getSetting($this->_class, 'componentName');
        $title = str_replace('.', ' ', $title);
        $form = Vpc_Abstract_Form::createChildComponentForm($this->_class, 'child');
        if ($form) {
            $form->setIdTemplate('{0}-child-child');
            $ret[] = array(
                'form' => $form,
                'title' => $title,
            );
        }
        return $ret;
    }
}
