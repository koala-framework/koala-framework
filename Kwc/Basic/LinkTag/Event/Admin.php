<?php
class Kwc_Basic_LinkTag_Event_Admin extends Kwc_Basic_LinkTag_News_Admin
{
    protected $_prefix = 'event';
    protected $_prefixPlural = 'events';

    public function getDirectoryComponentClasses()
    {
        return Kwc_Abstract::getComponentClassesByParentClass('Kwc_Events_Directory_Component');
    }
}
