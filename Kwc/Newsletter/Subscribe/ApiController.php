<?php
class Kwc_Newsletter_Subscribe_ApiController extends Kwf_Controller_Action
{
    protected $_model = 'Kwc_NewsletterCategory_Subscribe_CategoriesModel';
    protected $_subscribe;

    protected function _isAllowedComponent()
    {
        //allow for everyone, as anyone can subscribe in frontend form too
        //TODO: ip whitelist?
        return true;
    }

    public function preDispatch()
    {
        parent::preDispatch();
        if ($this->_getParam('subscribeComponentId')) {
            $nl = Kwf_Component_Data_Root::getInstance()
                ->getComponentByDbId($this->_getParam('subscribeComponentId'));
        } else {
            $select = array(
                'limit' => 1
            );
            if ($this->_getParam('subrootComponentId')) {
                $select['subroot'] = Kwf_Component_Data_Root::getInstance()
                    ->getComponentByDbId($this->_getParam('subrootComponentId'));
            }
            $nl = Kwf_Component_Data_Root::getInstance()->getComponentBySameClass($this->_getParam('class'), $select);
        }
        if (!$nl) {
            throw new Kwf_Exception('can\'t find newsletter subscribe component');
        }
        if ($nl->componentClass != $this->_getParam('class')) {
            throw new Kwf_Exception('Invalid componentClass');
        }
        $this->_subscribe = $nl;

        $this->_model = Kwf_Model_Abstract::getInstance($this->_model);
    }

    //public api to insert subscription
    //see Kwc_Newsletter_SubscribeRemote_Component
    public function jsonInsertAction()
    {
        $row = $this->_model->createRow();
        $row->gender = $this->_getParam('gender');
        $row->title = $this->_getParam('title');
        $row->firstname = $this->_getParam('firstname');
        $row->lastname = $this->_getParam('lastname');
        $row->email = $this->_getParam('email');
        $row->subscribe_date = date('Y-m-d H:i:s');

        $row->setLogSource(($url = $this->_getParam('url')) ? $url : $this->_subscribe->trlKwf('Subscribe API'));
        $row->setLogIp($this->_getParam('ip'));

        $inserted = $this->_insertSubscription($row);
        if ($inserted) {
            $this->view->message = $this->_subscribe->trlKwf('The subscription has been saved successfully.');
        } else {
            //atm this message is also shown when the user is already subscribed but not in the given category (which he is added to)
            $this->view->message = $this->_subscribe->trlKwf('You are already subscribed to new newsletter.');
        }
    }

    protected function _insertSubscription(Kwc_Newsletter_Subscribe_Row $row)
    {
        return $this->_subscribe->getComponent()->insertSubscription($row);
    }
}
