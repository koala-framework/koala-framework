<?php
/**
 * @group Kwf_Acl
 */
class Kwf_Acl_MenuDataTest extends Kwf_Test_TestCase
{
    public function testMenuData()
    {
        $acl = new Kwf_Acl();
        $acl->add(new Kwf_Acl_Resource_MenuDropdown('misc',
                    array('text'=>'Einstellungen', 'icon'=>'wrench.png')));
            $acl->add(new Kwf_Acl_Resource_MenuUrl('misc_languages',
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
        $acl = new Kwf_Acl();
        $acl->add(new Kwf_Acl_Resource_MenuDropdown('misc',
                    array('text'=>'Einstellungen', 'icon'=>'wrench.png')));
            $acl->add(new Kwf_Acl_Resource_MenuUrl('misc_languages',
                    array('text'=>'Sprachen', 'icon'=>'comment.png'),
                    '/admin/misc/languages'), 'misc');
        $acl->add(new Kwf_Acl_Resource_MenuDropdown('foo',
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
        $acl = new Kwf_Acl();
        $acl->add(new Zend_Acl_Resource('foo'));
            $acl->add(new Kwf_Acl_Resource_MenuUrl('misc_languages',
                    array('text'=>'Sprachen', 'icon'=>'comment.png'),
                    '/admin/misc/languages'), 'foo');

        $acl->allow(null, 'foo');
        $config = $acl->getMenuConfig(null);
        $this->assertEquals(1, count($config));
        $this->assertEquals('url', $config[0]['type']);
    }
}
