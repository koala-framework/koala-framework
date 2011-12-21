<?php
class Kwc_NewsletterCategory_Subscribe_RecipientsController extends Kwc_Newsletter_Subscribe_RecipientsController
{
    protected function _initColumns()
    {
        $this->_model = Kwf_Model_Abstract::getInstance('Kwc_NewsletterCategory_Subscribe_Model');
        parent::_initColumns();

        $model = Kwf_Model_Abstract::getInstance('Kwc_NewsletterCategory_CategoriesModel');
        $categories = $model->getRows($model->select()->order('pos'));

        // filter by category
        $categorySelects = array(array('all', '- '.trlKwf('All').' -'));
        foreach ($categories as $row) {
            $categorySelects[] = array($row->id, $row->category);
        }
        $this->_filters['category_id'] = array(
            'type'=>'ComboBox',
            'label' => trlKwf('Categorie').':',
            'width'=>110,
            'skipWhere' => true,
            'data' => $categorySelects,
            'default' => 'all'
        );

        foreach ($categories as $c) {
            $this->_columns->add(
                new Kwf_Grid_Column_Checkbox('categorycheck'.$c->id, $c->category, 70)
            )
            ->setData(new Kwc_NewsletterCategory_Detail_RecipientCategoryData($c->id));
        }
    }

    protected function _getSelect()
    {
        $select = parent::_getSelect();

        if ($this->_getParam('query_category_id') && $this->_getParam('query_category_id') != 'all') {
            $childSelect = new Kwf_Model_Select();
            $childSelect->whereEquals('category_id', $this->_getParam('query_category_id'));
            $select->where(new Kwf_Model_Select_Expr_Child_Contains('ToCategory', $childSelect));
        }
        return $select;
    }
}