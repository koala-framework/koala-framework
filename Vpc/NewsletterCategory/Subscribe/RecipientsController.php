<?php
class Vpc_NewsletterCategory_Subscribe_RecipientsController extends Vpc_Newsletter_Subscribe_RecipientsController
{
    protected function _initColumns()
    {
        $this->_model = Vps_Model_Abstract::getInstance('Vpc_NewsletterCategory_Subscribe_Model');
        parent::_initColumns();

        $model = Vps_Model_Abstract::getInstance('Vpc_NewsletterCategory_CategoriesModel');
        $categories = $model->getRows($model->select()->order('pos'));

        // filter by category
        $categorySelects = array(array('all', '- '.trlVps('All').' -'));
        foreach ($categories as $row) {
            $categorySelects[] = array($row->id, $row->category);
        }
        $this->_filters['category_id'] = array(
            'type'=>'ComboBox',
            'label' => trlVps('Categorie').':',
            'width'=>110,
            'skipWhere' => true,
            'data' => $categorySelects,
            'default' => 'all'
        );

        foreach ($categories as $c) {
            $this->_columns->add(
                new Vps_Grid_Column_Checkbox('categorycheck'.$c->id, $c->category, 70)
            )
            ->setData(new Vpc_NewsletterCategory_Detail_RecipientCategoryData($c->id));
        }
    }

    protected function _getSelect()
    {
        $select = parent::_getSelect();

        if ($this->_getParam('query_category_id') && $this->_getParam('query_category_id') != 'all') {
            $model = Vps_Model_Abstract::getInstance('Vpc_NewsletterCategory_Subscribe_SubscriberToCategory');
            $rows = $model->getRows($model->select()
                ->whereEquals('category_id', $this->_getParam('query_category_id'))
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