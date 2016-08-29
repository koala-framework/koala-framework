<?php
class Kwf_Update_20160824ComponentLinkViewCache extends Kwf_Update
{
    protected $_tags = array('kwc');

    public function update()
    {
        //required for addition of Kwf_Component_Data::getLinkClass
        Kwf_Component_Cache::getInstance()->deleteViewCache(array(
            'type' => 'componentLink'
        ));
    }
}
