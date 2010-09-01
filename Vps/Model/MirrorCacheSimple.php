<?php
class Vps_Model_MirrorCacheSimple extends Vps_Model_Proxy
{
    protected $_rowClass = 'Vps_Model_MirrorCacheSimple_Row';

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
    }

    public function getSourceModel()
    {
        return $this->_sourceModel;
    }

    public function initialSync()
    {
        $this->getProxyModel()->delete(array()); //alles löschen

        $format = self::_optimalImportExportFormat($this->getProxyModel(), $this->getSourceModel());
        $data = $this->_sourceModel->export($format);
        $this->getProxyModel()->import($format, $data);
    }
}
