<?php
class Kwf_Update_20160824ComponentLinkViewCache extends Kwf_Update
{
    protected $_tags = array('kwc');

    public function update()
    {
        Kwf_Component_Cache::getInstance()->deleteViewCache(array(
            'type' => 'componentLink'
        ));
    }
}
