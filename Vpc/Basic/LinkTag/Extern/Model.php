<?php
class Vpc_Basic_LinkTag_Extern_Model extends Vpc_Basic_LinkTag_Abstract_Model
{
    protected $_table = 'vpc_basic_link_extern';

    protected $_default = array(
        'target'        => 'http://',
        'open_type'     => 'self',
        'width'         => '0',
        'height'        => '0',
        'menubar'       => '1',
        'toolbar'       => '1',
        'locationbar'   => '1',
        'statusbar'     => '1',
        'scrollbars'    => '1',
        'resizable'     => '1'
    );

    protected $_toStringField = 'target';
}
