<?php
class Vps_Model_RowsSubModel_MirrorCacheSimple extends Vps_Model_RowsSubModel_Proxy
{
    protected $_rowClass = 'Vps_Model_RowsSubModel_MirrorCacheSimple_Row';

    protected $_sourceModel;

    public function __construct(array $config = array())
    {
        if (isset($config['sourceModel'])) $this->_sourceModel = $config['sourceModel'];
        parent::__construct($config);
    }

    protected function _init()
    {
        parent::_init();
        if (is_string($this->_sourceModel)) {
            $this->_sourceModel = Vps_Model_Abstract::getInstance($this->_sourceModel);
        }
        if (!($this->_proxyModel instanceof Vps_Model_RowsSubModel_Interface)) {
            throw  new Vps_Exception("Proxy model doesn't implement Vps_Model_RowsSubModel_Interface");
        }
    }

    public function getSourceModel()
    {
        return $this->_sourceModel;
    }

    public function createRow(array $data=array())
    {
        throw new Vps_Exception('getRows is not possible for Vps_Model_RowsSubModel_MirrorCacheSimple');
    }

    public function getRows($where=null, $order=null, $limit=null, $start=null)
    {
        throw new Vps_Exception('getRows is not possible for Vps_Model_RowsSubModel_MirrorCacheSimple');
    }
}
