<?php
class Kwf_Component_Generator_Page_Static extends Kwf_Component_Generator_PseudoPage_Static
{
    protected $_idSeparator = '_';
    protected $_inherits = true;
    protected $_eventsClass = 'Kwf_Component_Generator_Page_Events_Static';

    protected function _formatConfig($parentData, $componentKey)
    {
        $data = parent::_formatConfig($parentData, $componentKey);
        $data['isPage'] = true;
        return $data;
    }

    public function getGeneratorFlags()
    {
        $ret = parent::getGeneratorFlags();
        $ret['page'] = true;
        $ret['showInLinkInternAdmin'] = true;
        return $ret;
    }
}
