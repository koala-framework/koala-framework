<?php
class Kwc_Newsletter_Detail_StatisticsClicksController extends Kwf_Controller_Action_Auto_Grid
{
    protected $_buttons = array('xls');
    protected function _initColumns()
    {
        parent::_initColumns();

        $this->_columns->add(new Kwf_Grid_Column('recipient', trlKwf('Recipient'), 200));
        $this->_columns->add(new Kwf_Grid_Column_Datetime('date', trlKwf('Date'), 100));
        $this->_columns->add(new Kwf_Grid_Column('ip', trlKwf('IP-Address'), 100));
    }

    protected function _fetchData()
    {
        $component = Kwf_Component_Data_Root::getInstance()
            ->getComponentById($this->_getParam('componentId'))
            ->getChildComponent('_mail');
        $recipientSources = Kwc_Abstract::getSetting($component->componentClass, 'recipientSources');
        $sql = "
            SELECT recipient_id, recipient_model_shortcut, ip, click_date
            FROM kwc_mail_redirect_statistics
            WHERE redirect_id='" . $this->_getParam('id') . "'
            ORDER BY click_date ASC
        ";
        $ret = array();
        foreach (Kwf_Registry::get('db')->fetchAll($sql) as $row) {
            $modelName = $recipientSources[$row['recipient_model_shortcut']]['model'];
            $model = Kwf_Model_Abstract::getInstance($modelName);
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
