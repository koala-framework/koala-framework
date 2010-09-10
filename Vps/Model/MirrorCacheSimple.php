<?php
class Vps_Model_MirrorCacheSimple extends Vps_Model_Proxy
{
    protected $_rowClass = 'Vps_Model_MirrorCacheSimple_Row';

    /**
     * @var Vps_Model_Interface
     */
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
        $stepSize = 100;

        $format = self::_optimalImportExportFormat($this->getProxyModel(), $this->getSourceModel());
        $count = $this->_sourceModel->countRows();

        $progress = null;
        if (php_sapi_name() == 'cli') {
            $c = new Zend_ProgressBar_Adapter_Console();
            $c->setElements(array(Zend_ProgressBar_Adapter_Console::ELEMENT_PERCENT,
                                    Zend_ProgressBar_Adapter_Console::ELEMENT_BAR,
                                    Zend_ProgressBar_Adapter_Console::ELEMENT_ETA,
                                    Zend_ProgressBar_Adapter_Console::ELEMENT_TEXT));
            $progress = new Zend_ProgressBar($c, 0, $count);
        }
        $startTime = microtime(true);

        $this->getProxyModel()->deleteRows(array()); //alles löschen

        for ($offset=0; $offset < $count; $offset += $stepSize) {
            $s = new Vps_Model_Select();
            $s->limit($stepSize, $offset);
            /*
            $data = $this->_sourceModel->export($format, $s);
            $this->getProxyModel()->import($format, $data);
            */
            //warning: slow code ahead
            foreach ($this->_sourceModel->getRows($s) as $row) {
                $data = $row->toArray();
                $newRow = $this->createRow($data);
                foreach ($this->getDependentModels() as $rule=>$depModel) {
                    if ($depModel instanceof Vps_Model_RowsSubModel_MirrorCacheSimple) {
                        //dieser code könne vielleicht im Vps_Model_RowsSubModel_MirrorCacheSimple liegen
                        $m = $depModel->getSourceModel();
                        $ref = $m->getReferenceByModelClass(get_class($this), null);
                        $select = new Vps_Model_Select();
                        $select->whereEquals($ref['column'], $row->{$this->getPrimaryKey()});
                        $childRows = $m->getRows($select);
                        foreach ($childRows as $childRow) {
                            $newCRow = $newRow->createChildRow($rule, $childRow->toArray());
                        }
                    }
                }
                $newRow->save();
            }
            $this->_sourceModel->cleanRows();
            $this->getProxyModel()->cleanRows();
            $this->cleanRows();

            if ($progress) {
                $text = round(($offset + $stepSize) / (microtime(true)-$startTime)).' rows/sec';
                $progress->next($stepSize, $text);
            }
        }
    }
}
