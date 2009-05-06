<?php
class Vpc_Basic_Text_InlineStylesController extends Vps_Controller_Action_Auto_Grid
{
    protected $_buttons = array('add', 'delete');
    protected $_position = 'pos';
    protected $_modelName = 'Vpc_Basic_Text_StylesModel';

    public function init()
    {
        $class = $this->_getParam('componentClass');
        if (!Vpc_Abstract::getSetting($class, 'enableStyles') ||
            !Vpc_Abstract::getSetting($class, 'enableStylesEditor')
        ) {
            throw new Vps_Exception("Styles are disabled");
        }
        parent::init();
    }

    protected function _initColumns()
    {
        parent::_initColumns();
        $this->_columns->add(new Vps_Grid_Column('name', 'Name', 100));
    }

    protected function _getWhere()
    {
        $where = parent::_getWhere();
        $where[] = 'master=0';
        $where[] = "tag = 'span'";

        $pattern = Vpc_Abstract::getSetting($this->_getParam('componentClass'),
                                                            'stylesIdPattern');
        if ($pattern) {
            if (preg_match('#'.$pattern.'#', $this->_getParam('componentId'), $m)) {
                $where['ownStyles = ?'] = $m[0];
            }
        } else {
            $where[] = "ownStyles = ''";
        }
        return $where;
    }
    protected function _beforeDelete(Vps_Model_Row_Interface $row)
    {
        if ($this->_getUserRole() != 'admin' && $row->master) {
            throw new Vps_ClientException(trlVps("You can't delete master styles"));
        }
    }

}
