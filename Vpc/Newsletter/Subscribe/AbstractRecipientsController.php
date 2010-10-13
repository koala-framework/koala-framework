<?php
abstract class Vpc_Newsletter_Subscribe_AbstractRecipientsController extends Vps_Controller_Action_Auto_Grid
{
    public function jsonSaveRecipientsAction()
    {
        set_time_limit(60*10);

        $component = Vps_Component_Data_Root::getInstance()->getComponentByDbId(
            $this->_getParam('componentId'), array('ignoreVisible'=>true)
        );

        $order = $this->_defaultOrder;
        if ($this->getRequest()->getParam('sort')) {
            $order['field'] = $this->getRequest()->getParam('sort');
        }
        if ($this->_getParam("direction") && $this->_getParam('direction') != 'undefined') {
            $order['direction'] = $this->_getParam('direction');
        }
        $select = $this->_getSelect();
        if (is_null($select)) return null;
        $select->order($order);
        $rowset = $this->_model->getRows($select);
        $count = count($rowset);

        $progressBar = new Zend_ProgressBar(
            new Vps_Util_ProgressBar_Adapter_Cache(
                $this->_getParam('progressNum')
            ), 0, $count * 1.1
        );
        $x = 0;
        foreach ($rowset as $row) {
            $x++;
            $component->getComponent()->addToQueue($row);
            $progressBar->next(1, "$x / $count");
        }

        $progressBar->next(1, trlVps('RTR-ECG-Check and saving data: please wait...'));
        $this->view->assign($component->getComponent()->saveQueue());
        $progressBar->finish();
    }
}