<?php
class Vpc_Basic_LinkTag_Event_Admin extends Vpc_Basic_LinkTag_News_Admin
{
    protected $_prefix = 'event';
    protected $_prefixPlural = 'events';

    public function getCardForms()
    {
        $ret = array();
        $news = Vps_Component_Data_Root::getInstance()
            ->getComponentsByClass('Vpc_Events_Directory_Component');
        foreach ($news as $new) {
            $form = Vpc_Abstract_Form::createComponentForm($this->_class, 'child');
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
