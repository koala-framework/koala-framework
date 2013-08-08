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
        $domain = Kwf_Registry::get('config')->server->domain;
        Kwf_Registry::get('config')->test = array();
        Kwf_Registry::get('config')->test->foo = 'bar';

        Kwf_Registry::get('config')->kwc->domains = array();
        Kwf_Registry::get('config')->kwc->domains->at = array();
        Kwf_Registry::get('config')->kwc->domains->at->name = 'Österreich';
        Kwf_Registry::get('config')->kwc->domains->at->domain = "at.$domain";
        Kwf_Registry::get('config')->kwc->domains->at->language = 'de';
        Kwf_Registry::get('config')->kwc->domains->at->test = array();
        Kwf_Registry::get('config')->kwc->domains->at->test->foo = 'at';

        Kwf_Registry::get('config')->kwc->domains->si = array();
        Kwf_Registry::get('config')->kwc->domains->si->name = 'Slowenien';
        Kwf_Registry::get('config')->kwc->domains->si->domain = "si.$domain";
        Kwf_Registry::get('config')->kwc->domains->si->language = 'sl';
        Kwf_Registry::get('config')->kwc->domains->si->test = array();
        Kwf_Registry::get('config')->kwc->domains->si->test->foo = 'si';

        parent::setUp('Kwf_Component_BaseProperty_Root_Component');
        $this->_at = $this->_root->getChildComponent('-at');
        $this->_si = $this->_root->getChildComponent('-si');
    }

    public function tearDown()
    {
        unset(Kwf_Registry::get('config')->kwc->domains);
        unset(Kwf_Registry::get('config')->kwc->test);
        parent::tearDown();
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
        $this->assertEquals("at.$domain", $this->_at->getDomain());
        $this->assertEquals("si.$domain", $this->_si->getDomain());
    }
}
