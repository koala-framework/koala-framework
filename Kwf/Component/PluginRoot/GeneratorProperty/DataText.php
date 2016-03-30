<?php
class Kwf_Component_PluginRoot_GeneratorProperty_DataText extends Kwf_Data_Abstract
{
    private $_plugin;
    public function __construct(Kwf_Component_PluginRoot_Interface_GeneratorProperty $plugin)
    {
        $this->_plugin = $plugin;
    }

    public function load($row, array $info)
    {
        if (isset($row->component_id)) {
            $id = $row->component_id.'-'.$row->id;
        } else {
            $id = $row->id;
        }
        $data = Kwf_Component_Data_Root::getInstance()->getComponentByDbId($id, array('ignoreVisible'=>true, 'limit'=>1));
        $value = $this->_plugin->fetchGeneratorPropertyValue($data);
        $params = $this->_plugin->getGeneratorProperty($data->generator);
        return $params['values'][$value];
    }
}
