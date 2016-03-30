<?php
interface Kwf_Component_PluginRoot_Interface_GeneratorProperty
{
    /*
     * array(
     *    'name' => 'example',
     *    'label' => 'Example',
     *    'values' => array('foo' => 'Foo'),
     *    'defaultValue' => 'foo'
     * ) or false
     */
    public function getGeneratorProperty(Kwf_Component_Generator_Abstract $generator);

    public function fetchGeneratorPropertyValue(Kwf_Component_Data $data);

    public function saveGeneratorPropertyValue(Kwf_Component_Data $data, $value);
}
