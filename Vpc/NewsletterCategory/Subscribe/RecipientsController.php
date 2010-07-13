<?php
class Vpc_NewsletterCategory_Subscribe_RecipientsController extends Vpc_Newsletter_Subscribe_RecipientsController
{
    protected function _initColumns()
    {
        $this->_model = Vps_Model_Abstract::getInstance('Vpc_NewsletterCategory_Subscribe_Model');
        parent::_initColumns();

        $pool = Vps_Model_Abstract::getInstance('Vps_Util_Model_Pool');
        $categories = $pool->getRows($pool->select()
            ->whereEquals('pool', 'Newsletterkategorien')
            ->order('pos')
        );

        // filter by category
        $categorySelects = array(array('all', '- '.trlVps('All').' -'));
        foreach ($categories as $row) {
            $categorySelects[] = array($row->id, $row->value);
        }
        $this->_filters['pool_id'] = array(
            'type'=>'ComboBox',
            'label' => trlVps('Categorie').':',
            'width'=>110,
            'skipWhere' => true,
            'data' => $categorySelects,
            'default' => 'all'
        );

        foreach ($categories as $c) {
            $this->_columns->add(
                new Vps_Grid_Column_Checkbox('poolcheck'.$c->id, $c->value, 70)
            )
            ->setData(new Vpc_NewsletterCategory_Detail_RecipientCategoryData($c->id));
        }
    }

    protected function _getSelect()
    {
        $select = parent::_getSelect();

        if ($this->_getParam('query_pool_id') && $this->_getParam('query_pool_id') != 'all') {
            $model = Vps_Model_Abstract::getInstance('Vpc_NewsletterCategory_Subscribe_SubscriberToPool');
            $rows = $model->getRows($model->select()
                ->whereEquals('pool_id', $this->_getParam('query_pool_id'))
            );
            $ids = array();
            foreach ($rows as $row) {
                $ids[] = $row->subscriber_id;
            }
            if (!count($ids)) return null;
            $select->whereEquals('id', $ids);
        }
        return $select;
    }
}