<?php
class Kwc_Basic_LinkTag_Event_Admin extends Kwc_Basic_LinkTag_News_Admin
{
    protected $_prefix = 'event';
    protected $_prefixPlural = 'events';

    public function getCardForms()
    {
        $ret = array();
        $news = Kwf_Component_Data_Root::getInstance()
            ->getComponentsByClass('Kwc_Events_Directory_Component');
        foreach ($news as $new) {
            $form = Kwc_Abstract_Form::createComponentForm($this->_class, 'child');
            $form->fields['event_id']->setBaseParams(array('eventsComponentId'=>$new->dbId));
            $form->fields['event_id']->setFieldLabel($new->getPage()->name);
            $ret[$new->dbId] = array(
                'form' => $form,
                'title' => $new->getTitle()
            );
        }
        return $ret;
    }
}
