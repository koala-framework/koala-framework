<?php
class Kwc_Articles_Directory_ViewsController extends Kwf_Controller_Action_Auto_Grid
{
    protected $_model = 'Kwc_Articles_Directory_ViewsModel';
    protected $_defaultOrder = array('field'=>'date', 'direction'=>'DESC');
    protected $_paging = 50;

    protected function _initColumns()
    {
        $data = array();
        $model = $this->_getModel()->getReferencedModel('Article');
        $select = $model->select()
            ->whereEquals('visible', 1)
            ->order('date', 'DESC');
        foreach ($model->getRows($select) as $row) {
            $data[] = array($row->id, $row->date . ': ' . $row->title);
        }
        $this->_filters['article_id'] = array(
            'type' => 'ComboBox',
            'label' => trlKwf('Article') . ':',
            'width' => 200,
            'listWidth' => 400,
            'data' => $data
        );

        $userIds = array();
        $export = $this->_getModel()->export(Kwf_Model_Abstract::FORMAT_ARRAY, null, array('columns' => array('user_id')));
        foreach ($export as $e) $userIds[] = $e['user_id'];
        $data = array();
        $model = Kwf_Registry::get('userModel');
        $select = $model->select()
            ->whereEquals('id', array_unique($userIds))
            ->order('lastname', 'ASC');
        foreach ($model->getRows($select) as $row) {
            $role = $row->role == 'partnernet' ? '' : ' - ' . $row->role;
            $data[] = array($row->id, $row->name . ' (' . $row->email . $role . ')');
        }
        $this->_filters['user_id'] = array(
            'type' => 'ComboBox',
            'label' => trlKwf('User') . ':',
            'width' => 200,
            'listWidth' => 400,
            'data' => $data
        );

        $this->_columns->add(new Kwf_Grid_Column_Date('date', trlKwf('Date'), 100));
        $this->_columns->add(new Kwf_Grid_Column('article', trlKwf('Article'), 300))
            ->setData(new Kwf_Data_Table_Parent('Article', 'title'));
        $this->_columns->add(new Kwf_Grid_Column('user', trlKwf('User'), 200))
            ->setData(new Kwf_Data_Table_Parent('User', 'name'));
    }

    protected function _getSelect()
    {
        $ret = parent::_getSelect();
        $ret->group(array('user_id', 'article_id'));
        return $ret;
    }
}
