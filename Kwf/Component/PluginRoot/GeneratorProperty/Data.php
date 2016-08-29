<?php
class Kwf_Component_PluginRoot_GeneratorProperty_Data extends Kwf_Data_Abstract
{
    protected $_plugin;
    private $_form;

    public function __construct(Kwf_Component_PluginRoot_Interface_GeneratorProperty $plugin, $form = null)
    {
        $this->_form = $form;
        $this->_plugin = $plugin;
    }

    public function load($row, array $info = array())
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
        if (!$row->id) {
            //when row doesn't have an id yet (when adding page) we don't have a data and can't save the property value
            //this HACK moves saving into afterSave in that case
            if (!$this->_form) {
                throw new Kwf_Exception("No form set for Data, required for adding");
            }
            $this->_form->toSaveGeneratorProperty[] = array(
                'row' => $row,
                'value' => $value,
                'plugin' => $this->_plugin
            );
        } else {

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
}
