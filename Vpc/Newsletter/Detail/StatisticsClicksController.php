<?php
class Vpc_Newsletter_Detail_StatisticsClicksController extends Vps_Controller_Action_Auto_Grid
{
    protected $_buttons = array('xls');
    protected function _initColumns()
    {
        parent::_initColumns();

        $this->_columns->add(new Vps_Grid_Column('recipient', trlVps('Recipient'), 200));
        $this->_columns->add(new Vps_Grid_Column_Datetime('date', trlVps('Date'), 100));
        $this->_columns->add(new Vps_Grid_Column('ip', trlVps('IP-Address'), 100));
    }

    protected function _fetchData()
    {
        $component = Vps_Component_Data_Root::getInstance()
            ->getComponentById($this->_getParam('componentId'))
            ->getChildComponent('-mail');
        $recipientSources = Vpc_Abstract::getSetting($component->componentClass, 'recipientSources');
        $sql = "
            SELECT recipient_id, recipient_model_shortcut, ip, click_date
            FROM vpc_mail_redirect_statistics
            WHERE redirect_id='" . $this->_getParam('id') . "'
            ORDER BY click_date ASC
        ";
        $ret = array();
        foreach (Vps_Registry::get('db')->fetchAll($sql) as $row) {
            $model = Vps_Model_Abstract::getInstance($recipientSources[$row['recipient_model_shortcut']]);
            $recipient = $model->getRow($row['recipient_id']);
            $name = $recipient ? $recipient->email : '';
            $ret[] = array(
                'recipient' => $name,
                'date' => $row['click_date'],
                'ip' => $row['ip']
            );
        }
        return $ret;
    }
}