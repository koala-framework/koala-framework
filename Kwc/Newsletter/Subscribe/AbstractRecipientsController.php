<?php
abstract class Kwc_Newsletter_Subscribe_AbstractRecipientsController extends Kwf_Controller_Action_Auto_Grid
{
    protected function _getSelect()
    {
        $select = parent::_getSelect();
        if (is_null($select)) return null;
        $order = $this->_defaultOrder;
        if ($this->getRequest()->getParam('sort')) {
            $order['field'] = $this->getRequest()->getParam('sort');
        }
        if ($this->_getParam("direction") && $this->_getParam('direction') != 'undefined') {
            $order['direction'] = $this->_getParam('direction');
        }
        $select->order($order);
        return $select;
    }

    private function _getRecipientsSelect()
    {
        $select = $this->_getSelect();
        if (is_null($select)) return null;
        if (!is_null($this->_getParam('ids'))) {
            if ($this->_getParam('ids')) {
                $select->whereEquals('id', explode(',', $this->_getParam('ids')));
            } else {
                throw new Kwf_Exception_Client(trlKwf('Please select recipients.'));
            }
        }
        return $select;
    }

    public function jsonRemoveRecipientsAction()
    {
        set_time_limit(60*10);
        ini_set('memory_limit', '384M');

        $component = Kwf_Component_Data_Root::getInstance()->getComponentByDbId(
            $this->_getParam('componentId'), array('ignoreVisible'=>true)
        );

        $select = $this->_getRecipientsSelect();
        if (is_null($select)) return null;
        $count = $this->_model->countRows($select);
        $progressBar = new Zend_ProgressBar(
            new Kwf_Util_ProgressBar_Adapter_Cache($this->_getParam('progressNum')),
            0,
            $count
        );

        $limit = 1000;
        $offset = 0;
        $this->view->before = $component->getComponent()->countQueue();
        do {
            $select->limit($limit, $offset);
            $ids = array();
            $result = $this->_model->export(
                Kwf_Model_Abstract::FORMAT_ARRAY, $select, array('columns' => array('id'))
            );
            foreach ($result as $row) {
                $ids[] = $row['id'];
            }
            $component->getComponent()->removeFromQueue(get_class($this->_model), $ids);
            $select->unsetPart('limit');
            $offset += $limit;
            $progressBar->next($limit, $offset." / $count");
        } while ($count > $offset);
        $this->view->after = $component->getComponent()->countQueue();
        $this->view->removed = $this->view->before - $this->view->after;

        $progressBar->finish();
    }

    public function jsonSaveRecipientsAction()
    {
        set_time_limit(60*10);
        ini_set('memory_limit', '384M');

        $component = Kwf_Component_Data_Root::getInstance()->getComponentByDbId(
            $this->_getParam('componentId'), array('ignoreVisible'=>true)
        );

        $select = $this->_getRecipientsSelect();
        if (is_null($select)) return null;

        $count = $this->_model->countRows($select);
        $progressBar = new Zend_ProgressBar(
            new Kwf_Util_ProgressBar_Adapter_Cache($this->_getParam('progressNum')),
            0,
            $count
        );

        $limit = 1000;
        $offset = 0;
        $this->view->before = $component->getComponent()->countQueue();
        $this->view->rtrExcluded = array();
        do {
            $select->limit($limit, $offset);
            $result = $component->getComponent()->importToQueue($this->_model, $select);
            $this->view->rtrExcluded = array_merge(
                $this->view->rtrExcluded, $result['rtrExcluded']
            );
            $select->unsetPart('limit');
            $offset += $limit;
            $progressBar->next($limit, $offset." / $count");
        } while ($count > $offset);
        $this->view->after = $component->getComponent()->countQueue();
        $this->view->added = $this->view->after - $this->view->before;

        $progressBar->finish();
    }
}