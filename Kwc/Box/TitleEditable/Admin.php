<?php
class Kwc_Box_TitleEditable_Admin extends Kwc_Abstract_Admin
{
    public function getPagePropertiesForm($config)
    {
        $c = $config['component'];
        if ($config['mode'] == 'add' || $c->isPage) {
            //pages can have their own title
            return new Kwc_Box_TitleEditable_Form(null, $this->_class);
        } else if (Kwc_Abstract::getFlag($c->componentClass, 'subroot') || $c->componentId == 'root') {
            //subroots/root can have title for child pages
            $ret = new Kwc_Box_TitleEditable_Form(null, $this->_class);
            $ret->fields['title']->setHelpText(null);
            $ret->fields['title']->setWidth(350);
            $ret->fields['title']->setComment(trlKwf('for child pages'));
            return $ret;
        }

        //others have no title
        return null;
    }
}
