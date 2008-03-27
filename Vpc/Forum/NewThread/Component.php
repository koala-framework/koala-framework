<?php
class Vpc_Forum_NewThread_Component extends Vpc_Forum_Posts_Write_Component
{
    private $_createdThread;
    protected function _init()
    {
        parent::_init();
        $c = $this->_createFieldComponent('Textbox', array('name'=>'subject', 'width'=>200));
        $c->store('name', 'subject');
        $c->store('fieldLabel', trlVps('Subject'));
        $c->store('isMandatory', true);
        array_unshift($this->_paragraphs, $c);
    }

    protected function _beforeSave($row)
    {
        $values = $this->_getValues();

        $t = new Vpc_Forum_Thread_Model();
        $thread = $t->createRow();
        $thread->subject = $values['subject'];
        $thread->component_id = $this->getParentComponent()->getDbId();
        if (Zend_Registry::get('userModel')->getAuthedUser()) {
            $thread->user_id = Zend_Registry::get('userModel')->getAuthedUser()->id;
        }
        $thread->save();
        $row->component_id = $this->getParentComponent()->getDbId()
                                        .'_'.$thread->id.'-posts';

        $this->_createdThread = $thread;
    }
    public function getThreadComponent()
    {
        if (!$this->_createdThread) return null;
        return $this->getParentComponent()->getPageFactory()
                        ->getChildPageByRow($this->_createdThread);
    }

    public function getGroupComponent()
    {
        return $this->getParentComponent();
    }

    public function getForumComponent()
    {
        return $this->getGroupComponent()->getForumComponent();
    }
}
