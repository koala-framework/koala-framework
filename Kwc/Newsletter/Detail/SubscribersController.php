<?php
class Kwc_Newsletter_Detail_SubscribersController extends Kwf_Controller_Action_Auto_Grid
{
    protected $_paging = 10;

    public function init()
    {
        $key = $this->_getParam('subscribeModelKey');
        $mailComponent = $this->_getMailComponent();
        $rs = $mailComponent->getComponent()->getRecipientSources();
        if (isset($rs[$key])) {
            $this->_model = $rs[$key]['model'];
        } else {
            $rs = reset($rs);
            $this->_model = $rs['model'];
        }
        parent::init();
    }

    protected function _getSelect()
    {
        $ret = parent::_getSelect();
        if ($this->_model->hasColumn('newsletter_component_id')) {
            if ($this->_getParam('newsletterComponentId')) {
                // check if newsletterComponentId is allowed for user
                $acl = Kwf_Registry::get('acl')->getComponentAcl();
                $c = Kwf_Component_Data_Root::getInstance()->getComponentById($this->_getParam('newsletterComponentId'), array('ignoreVisible'=>true, 'limit'=>1));
                if (!$acl->isAllowed(Kwf_Registry::get('userModel')->getAuthedUser(), $c)) throw new Kwf_Exception_AccessDenied();
                $ret->whereEquals('newsletter_component_id', $this->_getParam('newsletterComponentId'));
            } else {
                $c = Kwf_Component_Data_Root::getInstance()->getComponentByDbId($this->_getParam('componentId'), array('ignoreVisible'=>true, 'limit'=>1));
                $ret->whereEquals('newsletter_component_id', $c->parent->dbId);
            }
        }
        $mailComponent = $this->_getMailComponent();
        $rs = $mailComponent->getComponent()->getRecipientSources();
        foreach(array_keys($rs) as $key) {
            if (isset($rs[$key]['select']) && ($rs[$key]['model'] == get_class($this->_getModel()))) {
                $ret->merge($rs[$key]['select']);
            }
        }
        return $ret;
    }

    protected function _initColumns()
    {
        parent::_initColumns();
        $columns = $this->_columns;
        $columns->add(new Kwf_Grid_Column('id'));
        $columns->add(new Kwf_Grid_Column('firstname'));
        $columns->add(new Kwf_Grid_Column('lastname'));
        $columns->add(new Kwf_Grid_Column('email'));
    }

    protected function _getMailComponent()
    {
        $mailComponent = Kwf_Component_Data_Root::getInstance()->getComponentByDbId(
            $this->_getParam('componentId') . '_mail',
            array('ignoreVisible' => true)
        );
        return $mailComponent;
    }
}
