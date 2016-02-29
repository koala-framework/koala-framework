<?php
class Kwf_Component_RootPlugin_MaskComponentLink_Plugin2
    extends Kwf_Component_Data_RootPlugin_PostRender
{
    public function processOutput($output)
    {
        $output = $this->_unmask($output, array('foo' => 'a'));
        $output = $this->_mask($output, array('foo' => 'b'));
        return $output;
    }
}
