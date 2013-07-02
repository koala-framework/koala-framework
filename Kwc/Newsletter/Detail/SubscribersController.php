<?php
class Kwc_Newsletter_Detail_SubscribersController extends Kwf_Controller_Action_Auto_Grid
{
    protected $_paging = 10;

    public function init()
    {
        $this->setModel($this->_getParam('subscribeModel'));
        parent::init();
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

    public function jsonGetRecipientSourcesAction()
    {
        $mailComponent = Kwf_Component_Data_Root::getInstance()->getComponentByDbId(
            $this->_getParam('componentId') . '-mail',
            array('ignoreVisible' => true)
        );
        $recipientSources = $mailComponent->getComponent()->getRecipientSources();
        foreach($recipientSources as $key => $value) {
            if (is_array($value)) 
                $recipientSources[$key]['title'] = $mailComponent->trlStaticExecute($recipientSources[$key]['title']);
        }
        $this->view->sources = $recipientSources;
    }
}
