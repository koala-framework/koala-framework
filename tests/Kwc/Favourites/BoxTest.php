<?php
class Kwc_Favourites_BoxTest extends Kwc_TestAbstract
{
    public function setUp()
    {
        parent::setUp('Kwc_Favourites_Root');

        //use custom user model
        Kwf_Registry::get('config')->user->model = 'Kwc_Favourites_UserModel';

        //unset existing userModel instance to get new one
        $reg = Kwf_Registry::getInstance()->set('userModel',
            Kwf_Model_Abstract::getInstance('Kwc_Favourites_UserModel')
        );
    }

    public function tearDown()
    {
        parent::tearDown();
        Kwf_Registry::getInstance()->offsetUnset('config'); //re-reads config, undoes changes to config done above
        Kwf_Registry::getInstance()->offsetUnset('userModel');
    }

    //tests if the in this test overwritten userModel works correctly
    public function testUserModel()
    {
        $this->markTestIncomplete();
        $u = Kwf_Registry::get('userModel')->getAuthedUser();
        $this->assertEquals($u->id, 1);
    }

    public function testGetFavouriteComponentIds()
    {
        $this->markTestIncomplete();
        $componentIds = Kwc_Favourites_Favourite_Component::getFavouriteComponentIds('Kwc_Favourites_Favourite_Model');
        $this->assertEquals(count($componentIds), 2);
    }

    public function testComponentLink()
    {
        $this->markTestIncomplete();
        $c2 = $this->_root->getComponentById(2005)->getComponent();
        $html = $c2->getData()->render();
        $this->assertRegExp("#<div class=\"kwcFavouritesPageComponentFavouritesCount\">2</div>#", $html, 'Does match with the complete div because this div is needed by JavaScript');
    }

    private function getFavouriteCountFromBox($component)
    {
        $this->markTestIncomplete();
        $html = $component->getData()->render();
        $repl = preg_replace("#\n#", '', $html);
        $repl = preg_replace("#.*<div class=\"kwcFavouritesPageComponentFavouritesCount\">#", '', $repl);
        $repl = preg_replace("#</div>.*#", '', $repl);
        $count = $repl;
        return $count;
    }

    public function testComponentRemoveAdd()
    {
        $this->markTestIncomplete();
        $c = $this->_root->getComponentById(2005)->getComponent();
        $count = $this->getFavouriteCountFromBox($c);

        $row = $this->_root->getGenerator('page')->getModel()->getRow(2002);
        $row->visible = 0;
        $row->save();
        $this->_process();

        $c = $this->_root->getComponentById(2005)->getComponent();
        $countAfter = $this->getFavouriteCountFromBox($c);

        $this->assertEquals($count-1, $countAfter);

        $row = $this->_root->getGenerator('page')->getModel()->getRow(2002);
        $row->visible = 1;
        $row->save();
        $this->_process();

        $c = $this->_root->getComponentById(2005)->getComponent();
        $countAfterAfter = $this->getFavouriteCountFromBox($c);

        $this->assertEquals($count, $countAfterAfter);
    }

    public function testComponentRecursiveRemoveAdd()
    {
        $this->markTestIncomplete();
        $c = $this->_root->getComponentById(2005)->getComponent();
        $count = $this->getFavouriteCountFromBox($c);

        $row = $this->_root->getGenerator('page')->getModel()->getRow(2001);
        $row->visible = 0;
        $row->save();
        $this->_process();
//         $this->markTestIncomplete();
        // RecursiveRemove isn't handled correctly and RecusiveAdd is never fired this way
        $c = $this->_root->getComponentById(2005)->getComponent();
        $countAfter = $this->getFavouriteCountFromBox($c);

        $this->assertEquals($count-1, $countAfter);

        $row = $this->_root->getGenerator('page')->getModel()->getRow(2001);
        $row->visible = 1;
        $row->save();
        $this->_process();

        $c = $this->_root->getComponentById(2005)->getComponent();
        $countAfterAfter = $this->getFavouriteCountFromBox($c);

        $this->assertEquals($count, $countAfterAfter);
    }

    public function testUserAddFavourite()
    {
        $this->markTestIncomplete();
        $c = $this->_root->getComponentById(2005)->getComponent();
        $count = $this->getFavouriteCountFromBox($c);

        $model = Kwf_Model_Abstract::getInstance('Kwc_Favourites_Favourite_Model');
        $row = $model->createRow();
        $row->user_id = 1;
        $row->component_id = '2003';
        $row->save();

        $c = $this->_root->getComponentById(2005)->getComponent();
        $countAfter = $this->getFavouriteCountFromBox($c);

        $this->assertEquals($count+1, $countAfter);
    }

    public function testUserRemoveFavourite()
    {
        $this->markTestIncomplete();
        $c = $this->_root->getComponentById(2005)->getComponent();
        $count = $this->getFavouriteCountFromBox($c);

        $model = Kwf_Model_Abstract::getInstance('Kwc_Favourites_Favourite_Model');
        $row = $model->getRow(1);
        $row->delete();

        $c = $this->_root->getComponentById(2005)->getComponent();
        $countAfter = $this->getFavouriteCountFromBox($c);

        $this->assertEquals($count-1, $countAfter);
    }
}
