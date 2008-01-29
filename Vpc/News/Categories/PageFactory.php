<?php
class Vpc_News_Categories_PageFactory extends Vpc_Abstract_TablePageFactory
{
    protected $_tableName = 'Vps_Dao_Pool';
    protected $_componentClass = 'Vpc_News_Categories_Category_Component';
    protected $_additionalFactories = array();

    protected function _init()
    {
        parent::_init();
        $childComponentClasses = Vpc_Abstract::getSetting(
            get_class($this->_component), 'childComponentClasses'
        );
        $this->_componentClass = $childComponentClasses['details'];
    }

    protected function _getWhere()
    {
        $where = array(
            'pool = ?' => Vpc_Abstract::getSetting(get_class($this->_component), 'pool')
        );
        if (!$this->_showInvisible()) {
            $where['visible = 1'] = '';
        }

        return $where;
    }
}
