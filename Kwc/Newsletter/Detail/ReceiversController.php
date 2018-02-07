<?php
class Kwc_Newsletter_Detail_ReceiversController extends Kwf_Controller_Action_Auto_Grid
{
    protected $_model = 'Kwc_Newsletter_QueueLogModel';
    protected $_buttons = array('csv');
    protected $_paging = 25;
    protected $_defaultOrder = array(
        'field' => 'send_date',
        'direction' => 'DESC'
    );
    private $_newsletterRow;

    protected function _initColumns()
    {
        parent::_initColumns();

        $this->_filters['text'] = array(
            'type'=>'TextField',
            'width' => 100
        );
        $this->_filters['status'] = array(
            'type' => 'ComboBox',
            'label' => trlKwf('Status') . ":",
            'data'   => array(
                array('sent', Kwc_Newsletter_Detail_QueueLogStatusData::getText('sent')),
                array('failed', Kwc_Newsletter_Detail_QueueLogStatusData::getText('failed')),
                array('usernotfound', Kwc_Newsletter_Detail_QueueLogStatusData::getText('usernotfound'))
            ),
            'width'  => 120
        );
        $this->_addPluginFilters();

        $columns = $this->_columns;
        $columns->add(new Kwf_Grid_Column_Datetime('send_date', trlKwf('Send date')));
        $columns->add(new Kwf_Grid_Column('status', trlKwf('Status')))
            ->setData(new Kwc_Newsletter_Detail_QueueLogStatusData());
        $columns->add(new Kwf_Grid_Column('email', trlKwf('Email'), 200))
            ->setData(new Kwc_Newsletter_Detail_QueueLogData());
        $columns->add(new Kwf_Grid_Column('gender', trlKwf('Gender'), 70))
            ->setData(new Kwc_Newsletter_Detail_QueueLogData())
            ->setRenderer('genderIcon');
        $columns->add(new Kwf_Grid_Column('title', trlKwf('Title'), 80))
            ->setData(new Kwc_Newsletter_Detail_QueueLogData());
        $columns->add(new Kwf_Grid_Column('firstname', trlKwf('First name'), 110))
            ->setData(new Kwc_Newsletter_Detail_QueueLogData());
        $columns->add(new Kwf_Grid_Column('lastname', trlKwf('Last name'), 110))
            ->setData(new Kwc_Newsletter_Detail_QueueLogData());
    }

    protected function _getSelect()
    {
        $ret = parent::_getSelect();
        $ret->whereEquals('newsletter_id', $this->_getNewsletterRow()->id);

        $ret = $this->_addPluginSelect($ret);
        return $ret;
    }

    protected function _addPluginSelect($select)
    {
        foreach (Kwf_Component_Data_Root::getInstance()->getPlugins('Kwc_Newsletter_PluginInterface') as $plugin) {
            $plugin->modifyReceiversSelect($select);
        }
        return $select;
    }

    protected function _addPluginFilters()
    {
        foreach (Kwf_Component_Data_Root::getInstance()->getPlugins('Kwc_Newsletter_PluginInterface') as $plugin) {
            foreach ($plugin->getFiltersForReceiversGrid() as $column => $filter) {
                $this->_filters[$column] = $filter;
            }
        }
    }

    private function _getNewsletterRow()
    {
        if (!$this->_newsletterRow) {
            $c = Kwf_Component_Data_Root::getInstance()->getComponentByDbId(
                $this->_getParam('componentId'), array('ignoreVisible' => true)
            );
            $this->_newsletterRow = $c->row;
        }
        return $this->_newsletterRow;
    }
}
