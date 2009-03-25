<?php
class Vps_Util_Model_Feed_Feeds extends Vps_Model_Abstract
{
    protected $_rowClass = 'Vps_Util_Model_Feed_Row_Feed';
    protected $_dependentModels = array(
        'Entries' => 'Vps_Util_Model_Feed_Entries'
    );

    public function getOwnColumns()
    {
        return array('url', 'title', 'link', 'description', 'format');
    }

    public function getPrimaryKey()
    {
        return 'url';
    }
    public function getRows($where=null, $order=null, $limit=null, $start=null)
    {
        $select = $this->select($where, $order, $limit, $start);
        $we = $select->getPart(Vps_Model_Select::WHERE_EQUALS);
        if ($we && isset($we['url'])) {
            $id = $we['url'];
        } else {
            $id = $select->getPart(Vps_Model_Select::WHERE_ID);
        }
        if ($id) {
            $dataKeys = array($id);
            return new $this->_rowsetClass(array(
                'dataKeys' => $dataKeys,
                'model' => $this
            ));
        } else {
            throw new Vps_Exception_NotYetImplemented();
        }
    }

    public function getRowByDataKey($key)
    {
        if (!isset($this->_rows[$key])) {
            $this->_rows[$key] = new $this->_rowClass(array(
                'url' => $key,
                'model' => $this
            ));
        }
        return $this->_rows[$key];
    }

}
