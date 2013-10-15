<?php
/**
 */
class Kwf_Component_Cache_Mysql_Test extends Kwc_TestAbstract
{
    public function setUp()
    {
        if (Kwf_Cache_Simple::getBackend() == 'apc') $this->markTestSkipped("Test doesn't work with apc in cli.");
        parent::setUp('Kwf_Component_Cache_Mysql_Root_Component');
        Kwf_Component_Cache::setInstance(new Kwf_Component_Cache_Mysql_Cache());
    }

    public function testRenderAgain()
    {
        Kwf_Component_Cache::setInstance(new Kwf_Component_Cache_Mysql_Cache1());
        $row = Kwf_Model_Abstract::getInstance('Kwf_Component_Cache_Mysql_Root_Model')->getRow('root');

        $this->assertEquals('foo', $this->_root->render());

        $row->value = 'bar';
        $row->save();
        $this->_process();
        $this->assertEquals('bar', $this->_root->render());

        $row->value = 'bar2';
        $row->save();
        $this->_process();
        $this->assertEquals('bar2', $this->_root->render());
    }

    public function testExceptionBeforeDbUpdate()
    {
        Kwf_Component_Cache::setInstance(new Kwf_Component_Cache_Mysql_Cache2());
        $row = Kwf_Model_Abstract::getInstance('Kwf_Component_Cache_Mysql_Root_Model')->getRow('root');

        $this->assertEquals('foo', $this->_root->render());

        $row->value = 'bar';
        $row->save();
        try {
            $this->_process();
        } catch (Kwf_Exception $e) {
        }
        $this->assertEquals('bar', $this->_root->render());

        $row->value = 'bar2';
        $row->save();
        $this->_process();
        $this->assertEquals('bar2', $this->_root->render());
    }

    public function testExceptionAfterDbUpdate()
    {
        Kwf_Component_Cache::setInstance(new Kwf_Component_Cache_Mysql_Cache3());
        $row = Kwf_Model_Abstract::getInstance('Kwf_Component_Cache_Mysql_Root_Model')->getRow('root');

        $this->assertEquals('foo', $this->_root->render());

        $row->value = 'bar';
        $row->save();
        try {
            $this->_process();
        } catch (Kwf_Exception $e) {
        }
        $this->assertEquals('bar', $this->_root->render());

        $row->value = 'bar2';
        $row->save();
        $this->_process();
        $this->assertEquals('bar2', $this->_root->render());
    }

    public function testExceptionBeforeAfterFullPageDelete()
    {
        Kwf_Component_Cache::setInstance(new Kwf_Component_Cache_Mysql_Cache4());
        $row = Kwf_Model_Abstract::getInstance('Kwf_Component_Cache_Mysql_Root_Model')->getRow('root');

        $this->assertEquals('master foo', $this->_root->render(null, true));

        $row->value = 'bar';
        $row->save();
        try {
            $this->_process();
        } catch (Kwf_Exception $e) {
        }
        $this->assertEquals('master bar', $this->_root->render(null, true));

        $row->value = 'bar2';
        $row->save();
        $this->_process();
        $this->assertEquals('master bar2', $this->_root->render(null, true));
    }
}
