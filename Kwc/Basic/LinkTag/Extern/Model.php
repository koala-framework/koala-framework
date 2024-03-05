<?php
class Kwc_Basic_LinkTag_Extern_Model extends Kwc_Basic_LinkTag_Abstract_Model
{
    protected $_table = 'kwc_basic_link_extern';

    protected $_default = array(
        'target'        => 'http://',
        'open_type'     => 'self',
        'width'         => '0',
        'height'        => '0',
    );

    protected $_toStringField = 'target';
}
