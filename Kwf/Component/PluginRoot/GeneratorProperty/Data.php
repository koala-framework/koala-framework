<?php
class Kwf_Component_PluginRoot_GeneratorProperty_Data extends Kwf_Data_Abstract
{
    protected $_plugin;
    public function __construct(Kwf_Component_PluginRoot_Interface_GeneratorProperty $plugin)
    {
        $this->_plugin = $plugin;
    }

    public function load($row)
    {
        if (isset($row->component_id)) {
            $id = $row->component_id.'-'.$row->id;
        } else {
            $id = $row->id;
        }
        $data = Kwf_Component_Data_Root::getInstance()->getComponentByDbId($id, array('ignoreVisible'=>true, 'limit'=>1));
        if (!$data) {
            throw new Kwf_Exception("Didn't get data for $id");
        }
        return $this->_plugin->fetchGeneratorPropertyValue($data);
    }

    public function save(Kwf_Model_Row_Interface $row, $value)
    {
        if (isset($row->component_id)) {
            $id = $row->component_id.'-'.$row->id;
        } else {
            $id = $row->id;
        }
        $data = Kwf_Component_Data_Root::getInstance()->getComponentByDbId($id, array('ignoreVisible'=>true, 'limit'=>1));
        if (!$data) {
            throw new Kwf_Exception("Didn't get data for $id");
        }
        return $this->_plugin->saveGeneratorPropertyValue($data, $value);
    }
}
