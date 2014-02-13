<?php
class Kwc_Basic_Feed_Feed extends Kwc_Abstract_Feed_Component
{
    protected function _getRssEntries()
    {
        return Kwf_Model_Abstract::getInstance('Kwc_Basic_Feed_Model')
            ->getRows()->toArray();
    }
}
