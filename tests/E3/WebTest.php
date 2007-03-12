<?php
require_once 'E3/Web.php';
require_once 'PHPUnit/Framework/TestCase.php';


class E3_WebTest extends PHPUnit_Framework_TestCase
{
    protected $_web;

    public function setUp()
    {
        $this->_web = E3_Web::getInstance();
    }

    public function testPageRetrieveByPath()
    {
    	$page = $this->_web->getPageByPath("/foo/bar");
        $this->assertTrue(is_a($page, "E3_Component_Abstract"));
    }

}

