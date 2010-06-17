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
        $this->markTestIncomplete();
        $this->_setup();
    }

    private function _setup($rootClass = 'Vps_Component_Output_C1_Root_Component')
    {
        Vps_Component_Data_Root::setComponentClass($rootClass);
        $this->_root = Vps_Component_Data_Root::getInstance();

        $this->_output = new Vps_Component_Output_Cache();
        $this->_output->getCache()->setModel(new Vps_Component_Cache_CacheModel());
        $this->_output->getCache()->setMetaModel(new Vps_Component_Cache_CacheMetaModel());
        $this->_output->getCache()->setFieldsModel(new Vps_Component_Cache_CacheFieldsModel());
        $this->_output->getCache()->emptyPreload();
    }

    public function testCachePreloaded()
    {
        // Nur root
        $this->_output->getCache()->save('foo', 'root-master');
        $this->_output->getCache()->emptyPreload();
        $this->assertEquals('foo', $this->_output->renderMaster($this->_root));
        $this->assertEquals(1, $this->_output->getCache()->countPreloadCalls());

        // Root nicht als MasterTemplate
        $this->_output->getCache()->save('bar', 'root');
        $this->_output->getCache()->emptyPreload();
        $this->assertEquals('bar', $this->_output->render($this->_root));
        $this->assertEquals(1, $this->_output->getCache()->countPreloadCalls());

        // Root und Child
        $this->_output->getCache()->save('foo {nocache: Vps_Component_Output_C1_Child_Component root-child} bar', 'root-master');
        $this->_output->getCache()->save('child', 'root-child');
        $this->_output->getCache()->emptyPreload();
        $this->assertEquals('foo child bar', $this->_output->renderMaster($this->_root));
        $this->assertEquals(1, $this->_output->getCache()->countPreloadCalls());
        $this->assertEquals('child', $this->_output->render($this->_root->getChildComponent('-child')));

        // Root mit ChildChild
        $this->_output->getCache()->save('foo {nocache: Vps_Component_Output_C1_Child_Component root-child} root', 'root-master');
        $this->_output->getCache()->save('bar {nocache: Vps_Component_Output_C1_ChildChild_Component root-child-child}', 'root-child');
        $this->_output->getCache()->save('child', 'root-child-child');
        $this->_output->getCache()->emptyPreload();
        $this->assertEquals('foo bar child root', $this->_output->renderMaster($this->_root));
        $this->assertEquals(1, $this->_output->getCache()->countPreloadCalls());

        // Plugin im Root
        $this->_output->getCache()->save('foo', 'root-master');
        $this->_output->getCache()->emptyPreload();
        $plugins = array('Vps_Component_Output_Plugin');
        $this->assertEquals('plugin(foo)', $this->_output->renderMaster($this->_root, $plugins));
        $this->assertEquals(1, $this->_output->getCache()->countPreloadCalls());

        // After Plugin im Root
        $this->_output->getCache()->save('root plugin(plugin({nocache: Vps_Component_Output_C1_Child_Component root-child}))', 'root-master');
        $this->_output->getCache()->save('master2 child child2', 'root-child');
        $this->_output->getCache()->emptyPreload();
        $plugins = array('Vps_Component_Output_PluginAfter');
        $this->assertEquals('afterPlugin(root plugin(plugin(master2 child child2)))', $this->_output->renderMaster($this->_root, $plugins));
        $this->assertEquals(1, $this->_output->getCache()->countPreloadCalls());

        // 2 Plugins im Child
        $this->_output->getCache()->save('foo {nocache: Vps_Component_Output_C1_Child_Component root-child Vps_Component_Output_Plugin Vps_Component_Output_Plugin}', 'root-master');
        $this->_output->getCache()->save('bar', 'root-child');
        $this->_output->getCache()->emptyPreload();
        $this->assertEquals('foo plugin(plugin(bar))', $this->_output->renderMaster($this->_root));
        $this->assertEquals(1, $this->_output->getCache()->countPreloadCalls());

        // Plugin im Child und im Child-Child
        $this->_output->getCache()->save('foo {nocache: Vps_Component_Output_C1_Child_Component root-child Vps_Component_Output_Plugin}', 'root-master');
        $this->_output->getCache()->save('bar {nocache: Vps_Component_Output_C1_ChildChild_Component root-child-child Vps_Component_Output_Plugin}', 'root-child');
        $this->_output->getCache()->save('child', 'root-child-child');
        $this->_output->getCache()->emptyPreload();
        $this->assertEquals('foo plugin(bar plugin(child))', $this->_output->renderMaster($this->_root));
        $this->assertEquals(1, $this->_output->getCache()->countPreloadCalls());

        // IfHasContent
        $this->_output->getCache()->save('foo {content: Vps_Component_Output_C1_Child_Component root-child 1}{content}', 'root-master');
        $this->_output->getCache()->save('bar', 'root-child-hasContent1');
        $this->_output->getCache()->emptyPreload();
        $this->assertEquals('foo bar', $this->_output->renderMaster($this->_root));
        $this->assertEquals(1, $this->_output->getCache()->countPreloadCalls());

        // IfHasContent Child
        $this->_output->getCache()->save('foo {content: Vps_Component_Output_C1_Child_Component root-child 1}{content}', 'root-master');
        $this->_output->getCache()->save('bar {content: Vps_Component_Output_C1_Child_Component root-child-child 1}{content}', 'root-child-hasContent1');
        $this->_output->getCache()->save('foo', 'root-child-child-hasContent1');
        $this->_output->getCache()->emptyPreload();
        $this->assertEquals('foo bar foo', $this->_output->renderMaster($this->_root));
        $this->assertEquals(1, $this->_output->getCache()->countPreloadCalls());

        // IfHasContent Child mit Plugin
        $this->_output->getCache()->save('foo {content: Vps_Component_Output_C1_Child_Component root-child 1}{content}', 'root-master');
        $this->_output->getCache()->save('bar {content: Vps_Component_Output_C1_Child_Component root-child-child 1}{content}', 'root-child-hasContent1');
        $this->_output->getCache()->save('foo {nocache: Vps_Component_Output_C1_ChildChild_Component root-child-child Vps_Component_Output_Plugin}', 'root-child-child-hasContent1');
        $this->_output->getCache()->save('child', 'root-child-child');
        $this->_output->getCache()->emptyPreload();
        $this->assertEquals('foo bar foo plugin(child)', $this->_output->renderMaster($this->_root));
        $this->assertEquals(1, $this->_output->getCache()->countPreloadCalls());

        // 2x hasContent mit gleicher ComponentClass
        $this->_output->getCache()->save('{content: Vps_Component_Output_C1_Child_Component root-child 1}{content}{content: Vps_Component_Output_C1_Child_Component root-child 2}{content}', 'root-master');
        $this->_output->getCache()->save('foo', 'root-child-hasContent1');
        $this->_output->getCache()->save('bar', 'root-child-hasContent2');
        $this->_output->getCache()->emptyPreload();
        $this->assertEquals('foobar', $this->_output->renderMaster($this->_root));
        $this->assertEquals(1, $this->_output->getCache()->countPreloadCalls());

        // dynamic
        $content = serialize(array('content'));
        $this->_output->getCache()->save("{dynamic: Content }$content{/dynamic}", 'root-master');
        $this->_output->getCache()->emptyPreload();
        $this->assertEquals('content', $this->_output->renderMaster($this->_root));
        $this->assertEquals(1, $this->_output->getCache()->countPreloadCalls());
    }

    public function testCacheDisabledForComponent()
    {
        $this->_setup('Vps_Component_Output_C2_Root_Component');

        $this->_output->getCache()->save('foo {nocache: Vps_Component_Output_C2_Child_Component root-child} {nocache: Vps_Component_Output_C2_ChildNoCache_Component root-childNoCache}', 'root-master');
        $this->_output->getCache()->save('child', 'root-child');
        $this->_output->getCache()->emptyPreload();
        $this->assertEquals('foo child childNoCache', $this->_output->renderMaster($this->_root));
    }

    public function testComponentNotPreloaded()
    {
        $this->_output->getCache()->save('foo {nocache: Vps_Component_Output_C1_Child_Component root-child}', 'root-master');
        $this->_output->getCache()->save('child2preloaded', 'root-child-child');
        $this->assertEquals('foo master2 child child2preloaded', $this->_output->renderMaster($this->_root));

        $row = $this->_output->getCache()->getModel()->getRow('root-child');
        $this->assertEquals('Vps_Component_Output_C1_Child_Component', $row->component_class);
        $this->assertEquals('master2 child {nocache: Vps_Component_Output_C1_ChildChild_Component root-child-child }', $row->content);
    }

    public function testExpire()
    {
        $this->_setup('Vps_Component_Output_C4_Component');
        $this->assertEquals('foo', $this->_output->renderMaster($this->_root));
        $row = $this->_output->getCache()->getModel()->getRow('root-master');
        $this->assertEquals('Vps_Component_Output_C4_Component', $row->component_class);
        $this->assertTrue($row->expire > time());
    }

    public function testPartial()
    {
        $this->_setup('Vps_Component_Output_Partial_Random_Component');

        $params = serialize($this->_root->getComponent()->getPartialParams());
        $this->_output->getCache()->save("{partials: root Vps_Component_Output_Partial_Random_Component Vps_Component_Partial_Random $params }", 'root-master');
        $this->_output->getCache()->save('bar0', 'root~0');
        $this->_output->getCache()->save('bar1', 'root~1');
        $this->_output->getCache()->save('bar2', 'root~2');

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
