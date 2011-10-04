<?php
class Vps_Controller_Action_Auto_Filter_TextColumn
    extends Vps_Controller_Action_Auto_Filter_Text
{
    protected $_type = 'TextColumn';

    protected function _init()
    {
        parent::_init();
        $this->_mandatoryProperties['data'] = null;
    }

    public function formatSelect($select, $params = array())
    {
        if (!isset($params['queryTextColumn_text']) || !$params['queryTextColumn_text']) return $select;

        $params['query'] = $params['queryTextColumn_text'];
        if (!empty($params['queryTextColumn_column'])) {
            $queryFieldsBefore = $this->getQueryFields();
            $this->setQueryFields(array($params['queryTextColumn_column']));
            $ret = parent::formatSelect($select, $params);
            $this->setQueryFields($queryFieldsBefore);
        } else {
            $ret = parent::formatSelect($select, $params);
        }
        return $ret;
    }

    public function setFilterFields($rawData)
    {
        $data = array();
        foreach ($rawData as $key => $val) {
            if (!is_array($val)) {
                $val = array($key, $val);
            }
            $data[] = $val;
        }
        $this->setProperty('data', $data);
        return $this;
    }

    public function getName()
    {
        return 'textColumn';
    }

    public function getParamName()
    {
        return 'queryTextColumn';
    }
}


