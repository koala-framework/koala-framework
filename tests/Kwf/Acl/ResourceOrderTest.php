<?php
class Kwf_Acl_ResourceOrderTest extends Kwf_Test_TestCase
{
    public function testOrder()
    {
        $acl = new Kwf_Acl();
        $acl->add(new Kwf_Acl_Resource_MenuUrl('foo',
                array('text'=> 'Foo', 'icon'=>'heart.png'),
                '/admin/foo'));
        $acl->add(new Kwf_Acl_Resource_MenuUrl('bar',
                array('text'=> 'Bar', 'icon'=>'heart.png', 'order'=>1),
                '/admin/bar'));
        $acl->add(new Kwf_Acl_Resource_MenuUrl('foo2',
                array('text'=> 'Foo2', 'icon'=>'heart.png', 'order'=>-1),
                '/admin/foo2'));
        $acl->allow(null, 'foo');
        $acl->allow(null, 'bar');
        $acl->allow(null, 'foo2');
        $config = $acl->getMenuConfig(null);
        $this->assertEquals('Foo2', $config[0]['menuConfig']['text']);
    }

    public function testSameOrderLeaveUnchanged()
    {
        $acl = new Kwf_Acl();
        $acl->add(new Kwf_Acl_Resource_MenuUrl('foo',
                array('text'=> 'Foo', 'icon'=>'heart.png', 'order'=>0),
                '/admin/foo'));
        $acl->add(new Kwf_Acl_Resource_MenuUrl('bar',
                array('text'=> 'Bar', 'icon'=>'heart.png', 'order'=>0),
                '/admin/bar'));
        $acl->add(new Kwf_Acl_Resource_MenuUrl('foo2',
                array('text'=> 'Foo2', 'icon'=>'heart.png', 'order'=>0),
                '/admin/foo2'));
        $acl->allow(null, 'foo');
        $acl->allow(null, 'bar');
        $acl->allow(null, 'foo2');
        $config = $acl->getMenuConfig(null);
        $this->assertEquals('Foo', $config[0]['menuConfig']['text']);
        $this->assertEquals('Bar', $config[1]['menuConfig']['text']);
        $this->assertEquals('Foo2', $config[2]['menuConfig']['text']);
    }

    public function testOrderDropdown()
    {
        $acl = new Kwf_Acl();
        $acl->add(new Kwf_Acl_Resource_MenuDropdown('fooBar',
            array('text'=>trl('FooBar'), 'icon'=>'heart.png')));
        $acl->add(new Kwf_Acl_Resource_MenuUrl('foo',
                array('text'=> 'Foo', 'icon'=>'heart.png', 'order'=>1),
                '/admin/foo'), 'fooBar');
        $acl->add(new Kwf_Acl_Resource_MenuUrl('bar',
                array('text'=> 'Bar', 'icon'=>'heart.png'),
                '/admin/bar'), 'fooBar');
        $acl->allow(null, 'fooBar');
        $config = $acl->getMenuConfig(null);
        $this->assertEquals('Bar', $config[0]['children'][0]['menuConfig']['text']);
    }

    public function testComplexMenu()
    {
        $acl = new Kwf_Acl();
        $acl->add(new Kwf_Acl_Resource_MenuDropdown('fooBar1',
            array('text'=>'FooBar1', 'icon'=>'heart.png')));
        $acl->add(new Kwf_Acl_Resource_MenuUrl('foo1',
                array('text'=> 'Foo1', 'icon'=>'heart.png', 'order' => -1),
                '/admin/foo'), 'fooBar1');
        $acl->add(new Kwf_Acl_Resource_MenuUrl('bar1',
                array('text'=> 'Bar1', 'icon'=>'heart.png', 'order' => -2),
                '/admin/bar'), 'fooBar1');
        $acl->allow(null, 'fooBar1');
        
        $acl->add(new Kwf_Acl_Resource_MenuDropdown('fooBar2',
            array('text'=>'FooBar2', 'icon'=>'heart.png', 'order' => 1)));
        $acl->add(new Kwf_Acl_Resource_MenuUrl('foo2',
                array('text'=> 'Foo2', 'icon'=>'heart.png', 'order'=>1),
                '/admin/foo'), 'fooBar2');
        $acl->add(new Kwf_Acl_Resource_MenuUrl('bar2',
                array('text'=> 'Bar2', 'icon'=>'heart.png'),
                '/admin/bar'), 'fooBar2');
        $acl->allow(null, 'fooBar2');

        $acl->add(new Kwf_Acl_Resource_MenuDropdown('fooBar3',
            array('text'=>'FooBar3', 'icon'=>'heart.png', 'order' => -1)));
        $acl->add(new Kwf_Acl_Resource_MenuUrl('foo3',
                array('text'=> 'Foo3', 'icon'=>'heart.png'),
                '/admin/foo'), 'fooBar3');
        $acl->add(new Kwf_Acl_Resource_MenuUrl('bar3',
                array('text'=> 'Bar3', 'icon'=>'heart.png', 'order'=>-1),
                '/admin/bar'), 'fooBar3');
        $acl->allow(null, 'fooBar3');
        $config = $acl->getMenuConfig(null);

        //order of the dropdowns
        $this->assertEquals('FooBar3', $config[0]['menuConfig']['text']);
        $this->assertEquals('FooBar1', $config[1]['menuConfig']['text']);

        //order of fooBar1 children
        $this->assertEquals('Bar1', $config[1]['children'][0]['menuConfig']['text']);

        //order of fooBar2 children
        $this->assertEquals('Bar2', $config[2]['children'][0]['menuConfig']['text']);

        //order of fooBar3 children
        $this->assertEquals('Bar3', $config[0]['children'][0]['menuConfig']['text']);
    }
}
