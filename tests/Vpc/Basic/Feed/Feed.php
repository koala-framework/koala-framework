<?php
class Vpc_Basic_Feed_Feed extends Vpc_Abstract_Feed_Component
{
    protected function _getRssEntries()
    {
        return array(array(
            'title' => 'testtitle',
            'description' => 'testdescription',
            'link' => 'testlink'
        ));
    }
}
