<?php
class Kwf_Component_Fulltext_BasicHtml_Html_TestModel extends Kwf_Model_FnF
{
    protected $_primaryKey = 'component_id';
    protected $_data = array(
        array('component_id' => '1', 'content' => '<p>foo bar</p>'),
        array('component_id' => '2-html', 'content' => '<p>foo bar</p>'),
    );
}
