<?php
/**
 * @group Component_Output
 */
class Vps_Component_OutputTest extends PHPUnit_Framework_TestCase
{
    protected $_output;
    protected static $_templates = array();
    
    public function setUp()
    {
        Vps_Component_Data_Root::setComponentClass('Vps_Component_Output_Root');
        $this->_root = Vps_Component_Data_Root::getInstance();
        $this->_output = new Vps_Component_Output();
        $cache = $this->getMock('Vps_Component_Cache',
            array('save', '_preload'), array(), '', false);
        $cache->expects($this->any())
             ->method('_preload')
             ->will($this->returnCallback(array('Vps_Component_OutputTest', 'callback')));
        $this->_output->setCache($cache);
    }
    
    public static function callback($ids) {
        $templates = self::$_templates;
        $ret = array();
        foreach ($ids as $key => $val) {
            if (isset($templates[$key])) {
                $ret[$key] = $templates[$key];
            }
        }
        return $ret;
    }
    
    public function testCache()
    {
        // Nur root
        self::$_templates = array(
            'root__master' => 'foo'
        );
        $this->_output->getCache()->emptyPreload();
        $this->assertEquals('foo', $this->_output->renderMaster($this->_root));
        
        // Root nicht als MasterTemplate
        self::$_templates = array(
            'root' => 'bar'
        );
        $this->_output->getCache()->emptyPreload();
        $this->assertEquals('bar', $this->_output->render($this->_root));

        // Root und Child
        self::$_templates = array(
            'root__master' => 'foo {nocache: Vps_Component_Output_Child root-child root} bar',
            'root__child' => 'child'
        );
        $this->_output->getCache()->emptyPreload();
        $this->assertEquals('foo child bar', $this->_output->renderMaster($this->_root));
        $this->assertEquals('child', $this->_output->render($this->_root->getChildComponent('-child')));

        // Root mit ChildChild
        self::$_templates = array(
            'root__master' => 'foo {nocache: Vps_Component_Output_Child root-child root} root',
            'root__child' => 'bar {nocache: Vps_Component_Output_ChildChild root-child-child root}',
            'root__child__child' => 'child'
        );
        $this->_output->getCache()->emptyPreload();
        $this->assertEquals('foo bar child root', $this->_output->renderMaster($this->_root));

        // Plugin im Root
        self::$_templates = array(
            'root__master' => 'foo',
        );
        $this->_output->getCache()->emptyPreload();
        $plugins = array('Vps_Component_Output_Plugin');
        $this->assertEquals('plugin(foo)', $this->_output->renderMaster($this->_root, true, $plugins));
        
        // 2 Plugins im Child
        self::$_templates = array(
            'root__master' => 'foo {nocache: Vps_Component_Output_Child root-child root Vps_Component_Output_Plugin Vps_Component_Output_Plugin}',
            'root__child' => 'bar'
        );
        $this->_output->getCache()->emptyPreload();
        $this->assertEquals('foo plugin(plugin(bar))', $this->_output->renderMaster($this->_root));

        // Plugin im Child und im Child-Child
        self::$_templates = array(
            'root__master' => 'foo {nocache: Vps_Component_Output_Child root-child root Vps_Component_Output_Plugin}',
            'root__child' => 'bar {nocache: Vps_Component_Output_ChildChild root-child-child root Vps_Component_Output_Plugin}',
            'root__child__child' => 'child'
        );
        $this->_output->getCache()->emptyPreload();
        $this->assertEquals('foo plugin(bar plugin(child))', $this->_output->renderMaster($this->_root));
        
        // Plugin im Child und im Child-Child
        self::$_templates = array(
            'root__master' => 'foo {nocache: Vps_Component_Output_Child root-child root Vps_Component_Output_Plugin}',
            'root__child' => 'bar {nocache: Vps_Component_Output_ChildChild root-child-child root Vps_Component_Output_Plugin}',
            'root__child__child' => 'child'
        );
        $this->_output->getCache()->emptyPreload();
        $this->assertEquals('foo plugin(bar plugin(child))', $this->_output->renderMaster($this->_root));
        
        // IfHasContent
        self::$_templates = array(
            'root__master' => 'foo {content: Vps_Component_Output_Child root-child root}{content}',
            'root__child__hasContent' => 'bar',
        );
        $this->_output->getCache()->emptyPreload();
        $this->assertEquals('foo bar', $this->_output->renderMaster($this->_root));

        // IfHasContent Child
        self::$_templates = array(
            'root__master' => 'foo {content: Vps_Component_Output_Child root-child root}{content}',
            'root__child__hasContent' => 'bar {content: Vps_Component_Output_Child root-child-child root}{content}',
            'root__child__child__hasContent' => 'foo',
        );
        $this->_output->getCache()->emptyPreload();
        $this->assertEquals('foo bar foo', $this->_output->renderMaster($this->_root));

        // IfHasContent Child mit Plugin
        self::$_templates = array(
            'root__master' => 'foo {content: Vps_Component_Output_Child root-child root}{content}',
            'root__child__hasContent' => 'bar {content: Vps_Component_Output_Child root-child-child root}{content}',
            'root__child__child__hasContent' => 'foo {nocache: Vps_Component_Output_ChildChild root-child-child root Vps_Component_Output_Plugin}',
            'root__child__child' => 'child',
        );
        $this->_output->getCache()->emptyPreload();
        $this->assertEquals('foo bar foo plugin(child)', $this->_output->renderMaster($this->_root));
    }

    public function testSaveCacheEnabled()
    {
        $this->markTestIncomplete();
        
        $this->_output->getCache()->expects($this->once())
                                    ->method('save');

        self::$_templates = array(
            //'root__master' => 'foo'
        );
        $this->assertEquals('foo', $this->_output->renderMaster($this->_root));

    }

    public function testSaveCacheDisabled()
    {
        $this->markTestIncomplete();

        $this->_output->useCache(false);

        $this->_output->getCache()->expects($this->never())
                                    ->method('save');

        self::$_templates = array(
            //'root__master' => 'foo'
        );
        $this->assertEquals('foo', $this->_output->renderMaster($this->_root));
    }
}
