<?php
class Kwc_Basic_LinkTag_Lightbox_Admin extends Kwc_Basic_LinkTag_Abstract_Admin
{
    // wird bei linklist verwendet, damit url richtig ausgegeben wird
    public function componentToString($data)
    {
        return trlKwf('Lightbox');
    }

    public function getCardForms()
    {
        $ret = array();
        $title = Kwc_Abstract::getSetting($this->_class, 'componentName');
        $title = str_replace('.', ' ', $title);
        $form = Kwc_Abstract_Form::createChildComponentForm($this->_class, 'child');
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
