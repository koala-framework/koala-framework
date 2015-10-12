<?php
/**
 * @group Component_BaseProperty
 */
class Kwf_Component_BaseProperty_Test extends Kwc_TestAbstract
{
    private $_at;
    private $_si;

    public function setUp()
    {
        parent::setUp('Kwf_Component_BaseProperty_Root_Component');
        $this->_at = $this->_root->getChildComponent('-at');
        $this->_si = $this->_root->getChildComponent('-si');
    }

    public function testProperty()
    {
        $this->assertEquals('bar', $this->_root->getBaseProperty('test.foo'));
        $this->assertEquals('at', $this->_at->getBaseProperty('test.foo'));
        $this->assertEquals('si', $this->_si->getBaseProperty('test.foo'));
    }

    public function testLanguage()
    {
        $this->assertEquals('en', $this->_root->getLanguage());
        $this->assertEquals('de', $this->_at->getLanguage());
        $this->assertEquals('sl', $this->_si->getLanguage());
    }

    public function testDomain()
    {
        $domain = Kwf_Registry::get('config')->server->domain;
        $this->assertEquals($domain, $this->_root->getDomain());
        $this->assertEquals("at.example.com", $this->_at->getDomain());
        $this->assertEquals("si.example.com", $this->_si->getDomain());
    }
}
