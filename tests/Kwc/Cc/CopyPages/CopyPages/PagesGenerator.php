<?php
class Kwc_Cc_CopyPages_CopyPages_PagesGenerator extends Kwc_Chained_CopyTarget_PagesGenerator
{
    protected function _getChainedGenerator()
    {
        return Kwf_Component_Generator_Abstract::getInstance('Kwc_Cc_CopyPages_Root', 'page');
    }
}
