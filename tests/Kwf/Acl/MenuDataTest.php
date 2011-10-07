<?php
/**
 * @group Vps_Acl
 */
class Vps_Acl_MenuDataTest extends Vps_Test_TestCase
{
    public function testMenuData()
    {
        $acl = new Vps_Acl();
        $acl->add(new Vps_Acl_Resource_MenuDropdown('misc',
                    array('text'=>'Einstellungen', 'icon'=>'wrench.png')));
            $acl->add(new Vps_Acl_Resource_MenuUrl('misc_languages',
                    array('text'=>'Sprachen', 'icon'=>'comment.png'),
                    '/admin/misc/languages'), 'misc');

        $acl->allow(null, 'misc');
        $config = $acl->getMenuConfig(null);
        $this->assertEquals(1, count($config));
        $this->assertEquals('dropdown', $config[0]['type']);
        $this->assertEquals(1, count($config[0]['children']));
    }

    public function testEmptyDropdown()
    {
        $acl = new Vps_Acl();
        $acl->add(new Vps_Acl_Resource_MenuDropdown('misc',
                    array('text'=>'Einstellungen', 'icon'=>'wrench.png')));
            $acl->add(new Vps_Acl_Resource_MenuUrl('misc_languages',
                    array('text'=>'Sprachen', 'icon'=>'comment.png'),
                    '/admin/misc/languages'), 'misc');
        $acl->add(new Vps_Acl_Resource_MenuDropdown('foo',
                    array('text'=>'Einstellungen', 'icon'=>'wrench.png')));

        $acl->allow(null, 'misc');
        $acl->allow(null, 'foo');
        $config = $acl->getMenuConfig(null);
        $this->assertEquals(1, count($config));
        $this->assertEquals('dropdown', $config[0]['type']);
        $this->assertEquals(1, count($config[0]['children']));
    }

    public function testBelowZendResourceGoesToParent()
    {
        $acl = new Vps_Acl();
        $acl->add(new Zend_Acl_Resource('foo'));
            $acl->add(new Vps_Acl_Resource_MenuUrl('misc_languages',
                    array('text'=>'Sprachen', 'icon'=>'comment.png'),
                    '/admin/misc/languages'), 'foo');

        $acl->allow(null, 'foo');
        $config = $acl->getMenuConfig(null);
        $this->assertEquals(1, count($config));
        $this->assertEquals('url', $config[0]['type']);
    }
}
