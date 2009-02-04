<?php
/**
 * @group Component_Output_Cache
 */
class Vps_Component_Output_CacheTest extends PHPUnit_Framework_TestCase
{
    protected $_output;
    protected static $_templates = array();
    protected static $_expectedCalls = 0;
    protected static $_calls = 0;

    public function setUp()
    {
        $this->_setup();
    }

    private function _setup($rootClass = 'Vps_Component_Output_C1_Root_Component')
    {
        Vps_Component_Data_Root::setComponentClass($rootClass);
        $this->_root = Vps_Component_Data_Root::getInstance();
        $this->_output = new Vps_Component_Output_Cache();
        $cache = $this->getMock('Vps_Component_Cache',
            array('save', '_preload'), array(), '', false);
        $cache->expects($this->any())
             ->method('_preload')
             ->will($this->returnCallback(array('Vps_Component_Output_CacheTest', 'callback')));
        $this->_output->setCache($cache);
    }

    public static function callback($ids) {
        if (empty($ids)) return array();
        self::$_calls++;
        $calls = self::$_calls;
        $expectedCalls = self::$_expectedCalls;
        if ($calls > $expectedCalls) throw new Vps_Exception("Too many preload Calls: expected $expectedCalls, calls: $calls");
        $templates = self::$_templates;
        $ret = array();
        foreach ($ids as $key => $val) {
            $ret[$key] = isset($templates[$key]) ? $templates[$key] : null;
        }
        return $ret;
    }

    public function testCachePreloaded()
    {
        $this->_output->getCache()->expects($this->never())
                                    ->method('save');
        $this->_output->getCache()->expects($this->never())
                                    ->method('load');

        // Nur root
        self::$_templates = array(
            'root__master' => 'foo'
        );
        self::$_calls = 0;
        self::$_expectedCalls = 2;
        $this->_output->getCache()->emptyPreload();
        $this->assertEquals('foo', $this->_output->renderMaster($this->_root));

        // Root nicht als MasterTemplate
        self::$_templates = array(
            'root' => 'bar'
        );
        self::$_calls = 0;
        self::$_expectedCalls = 1;
        $this->_output->getCache()->emptyPreload();
        $this->assertEquals('bar', $this->_output->render($this->_root));

        // Root und Child
        self::$_templates = array(
            'root__master' => 'foo {nocache: Vps_Component_Output_C1_Child_Component root-child} bar',
            'root__child' => 'child'
        );
        self::$_calls = 0;
        self::$_expectedCalls = 2;
        $this->_output->getCache()->emptyPreload();
        $this->assertEquals('foo child bar', $this->_output->renderMaster($this->_root));
        self::$_calls = 0;
        self::$_expectedCalls = 1;
        $this->assertEquals('child', $this->_output->render($this->_root->getChildComponent('-child')));

        // Root mit ChildChild
        self::$_templates = array(
            'root__master' => 'foo {nocache: Vps_Component_Output_C1_Child_Component root-child} root',
            'root__child' => 'bar {nocache: Vps_Component_Output_C1_ChildChild_Component root-child-child}',
            'root__child__child' => 'child'
        );
        self::$_calls = 0;
        self::$_expectedCalls = 3;
        $this->_output->getCache()->emptyPreload();
        $this->assertEquals('foo bar child root', $this->_output->renderMaster($this->_root));

        // Plugin im Root
        self::$_templates = array(
            'root__master' => 'foo',
        );
        self::$_calls = 0;
        self::$_expectedCalls = 1;
        $this->_output->getCache()->emptyPreload();
        $plugins = array('Vps_Component_Output_Plugin');
        $this->assertEquals('plugin(foo)', $this->_output->renderMaster($this->_root, $plugins));

        // 2 Plugins im Child
        self::$_templates = array(
            'root__master' => 'foo {nocache: Vps_Component_Output_C1_Child_Component root-child Vps_Component_Output_Plugin Vps_Component_Output_Plugin}',
            'root__child' => 'bar'
        );
        self::$_calls = 0;
        self::$_expectedCalls = 2;
        $this->_output->getCache()->emptyPreload();
        $this->assertEquals('foo plugin(plugin(bar))', $this->_output->renderMaster($this->_root));

        // Plugin im Child und im Child-Child
        self::$_templates = array(
            'root__master' => 'foo {nocache: Vps_Component_Output_C1_Child_Component root-child Vps_Component_Output_Plugin}',
            'root__child' => 'bar {nocache: Vps_Component_Output_C1_ChildChild_Component root-child-child Vps_Component_Output_Plugin}',
            'root__child__child' => 'child'
        );
        self::$_calls = 0;
        self::$_expectedCalls = 3;
        $this->_output->getCache()->emptyPreload();
        $this->assertEquals('foo plugin(bar plugin(child))', $this->_output->renderMaster($this->_root));

        // Plugin im Child und im Child-Child
        self::$_templates = array(
            'root__master' => 'foo {nocache: Vps_Component_Output_C1_Child_Component root-child Vps_Component_Output_Plugin}',
            'root__child' => 'bar {nocache: Vps_Component_Output_C1_ChildChild_Component root-child-child Vps_Component_Output_Plugin}',
            'root__child__child' => 'child'
        );
        self::$_calls = 0;
        self::$_expectedCalls = 3;
        $this->_output->getCache()->emptyPreload();
        $this->assertEquals('foo plugin(bar plugin(child))', $this->_output->renderMaster($this->_root));

        // IfHasContent
        self::$_templates = array(
            'root__master' => 'foo {content: Vps_Component_Output_C1_Child_Component root-child}{content}',
            'root__child__hasContent' => 'bar',
        );
        self::$_calls = 0;
        self::$_expectedCalls = 2;
        $this->_output->getCache()->emptyPreload();
        $this->assertEquals('foo bar', $this->_output->renderMaster($this->_root));

        // IfHasContent Child
        self::$_templates = array(
            'root__master' => 'foo {content: Vps_Component_Output_C1_Child_Component root-child}{content}',
            'root__child__hasContent' => 'bar {content: Vps_Component_Output_C1_Child_Component root-child-child}{content}',
            'root__child__child__hasContent' => 'foo',
        );
        self::$_calls = 0;
        self::$_expectedCalls = 3;
        $this->_output->getCache()->emptyPreload();
        $this->assertEquals('foo bar foo', $this->_output->renderMaster($this->_root));

        // IfHasContent Child mit Plugin
        self::$_templates = array(
            'root__master' => 'foo {content: Vps_Component_Output_C1_Child_Component root-child}{content}',
            'root__child__hasContent' => 'bar {content: Vps_Component_Output_C1_Child_Component root-child-child}{content}',
            'root__child__child__hasContent' => 'foo {nocache: Vps_Component_Output_C1_ChildChild_Component root-child-child Vps_Component_Output_Plugin}',
            'root__child__child' => 'child',
        );
        self::$_calls = 0;
        self::$_expectedCalls = 4;
        $this->_output->getCache()->emptyPreload();
        $this->assertEquals('foo bar foo plugin(child)', $this->_output->renderMaster($this->_root));

        // Es darf nur 2x preloaded werden
        self::$_templates = array(
            'root__master' => 'foo {content: Vps_Component_Output_C1_Child_Component root-child}{content} {nocache: Vps_Component_Output_C1_Child_Component root-child} {nocache: Vps_Component_Output_C1_ChildChild_Component root-child-child}',
            'root__child__hasContent' => '{nocache: Vps_Component_Output_C1_Child_Component root-child}',
            'root__child' => 'child',
            'root__child__child' => 'child2',
        );
        self::$_calls = 0;
        self::$_expectedCalls = 2;
        $this->_output->getCache()->emptyPreload();
        $this->assertEquals('foo child child child2', $this->_output->renderMaster($this->_root));
    }

    public function testCacheDisabledForComponent()
    {
        $this->_setup('Vps_Component_Output_C2_Root_Component');

        $this->_output->getCache()->expects($this->never())
                                    ->method('save');

        self::$_templates = array(
            'root__master' => 'foo {nocache: Vps_Component_Output_C2_Child_Component root-child} {nocache: Vps_Component_Output_C2_ChildNoCache_Component root-childNoCache}',
            'root__child' => 'child',
            'root__childNoCache' => 'mustNotBeOutput'
        );
        self::$_calls = 0;
        self::$_expectedCalls = 2;
        $this->_output->getCache()->emptyPreload();
        $this->assertEquals('foo child childNoCache', $this->_output->renderMaster($this->_root));
    }

    public function testComponentNotPreloaded()
    {
        $this->_output->getCache()->expects($this->once())
                                    ->method('save')
                                    ->with(
                                        $this->equalTo('master2 child {nocache: Vps_Component_Output_C1_ChildChild_Component root-child-child }'),
                                        $this->equalTo('root__child'),
                                        $this->equalTo(array(
                                            'componentClass' => 'Vps_Component_Output_C1_Child_Component',
                                            'pageId' => 'root')
                                        ),
                                        $this->equalTo(null)
                                    );
        self::$_templates = array(
            'root__master' => 'foo {nocache: Vps_Component_Output_C1_Child_Component root-child}',
            'root__child__child' => 'child2preloaded',
        );
        self::$_calls = 0;
        self::$_expectedCalls = 5;
        $this->_output->getCache()->emptyPreload();
        $this->assertEquals('foo master2 child child2preloaded', $this->_output->renderMaster($this->_root));
    }

    public function testExpire()
    {
        $this->_setup('Vps_Component_Output_C4_Component');
        $this->_output->getCache()->expects($this->once())
                                    ->method('save')
                                    ->with(
                                        $this->equalTo('foo'),
                                        $this->equalTo('root__master'),
                                        $this->equalTo(array(
                                            'componentClass' => 'Vps_Component_Output_C4_Component',
                                            'pageId' => 'root')
                                        ),
                                        $this->equalTo(10)
                                    );

        self::$_templates = array();
        self::$_calls = 0;
        self::$_expectedCalls = 2;
        $this->_output->getCache()->emptyPreload();
        $this->assertEquals('foo', $this->_output->renderMaster($this->_root));
    }

    public function testPartial()
    {
        $this->_setup('Vps_Component_Output_Partial_Random_Component');

        $this->_output->getCache()->expects($this->never())
                                    ->method('save');

        $params = serialize($this->_root->getComponent()->getPartialParams());
        self::$_templates = array(
            'root__master' => "{partials: root Vps_Component_Output_Partial_Random_Component Vps_Component_Partial_Random $params }",
            'root___0' => 'bar0',
            'root___1' => 'bar1',
            'root___2' => 'bar2',
        );
        self::$_calls = 0;
        self::$_expectedCalls = 2;

        $this->_output->getCache()->emptyPreload();
        $value = $this->_output->renderMaster($this->_root);
        $this->assertTrue(in_array($value, array(
            'bar0bar1', 'bar0bar2', 'bar1bar2', 'bar1bar0', 'bar2bar0', 'bar2bar1'
        )));

        self::$_expectedCalls = 100;
        $x = 0;
        while ($x <= 10 && $value == $this->_output->renderMaster($this->_root)) {
            $x++;
        }
        $this->assertTrue($x < 100);
    }
}
