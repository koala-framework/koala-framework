<?php
/**
 * @group Advanced_SocialBookmarks
 */
class Vpc_Advanced_SocialBookmarks_Test extends Vpc_TestAbstract
{
    public function testIt()
    {
        $this->_init('Vpc_Advanced_SocialBookmarks_Root');
        $page1 = $this->_root->getChildComponent('_page1');
        $page2 = $page1->getChildComponent('_page2');

        $this->assertTrue(strpos($page1->render(true, true), 'Twitter') !== false);
        $this->assertTrue(strpos($page2->render(true, true), 'Twitter') !== false);
        Vps_Model_Abstract::getInstance('Vpc_Advanced_SocialBookmarks_TestModel')
            ->getRow('root-socialBookmarks')
            ->getChildRows('Networks', 2)->current()->delete();
        $this->_process();
        $this->assertTrue(strpos($page1->render(false, true), 'Twitter') === false);
        $this->assertTrue(strpos($page2->render(false, true), 'Twitter') === false);
        $this->assertTrue(strpos($page1->render(true, true), 'Twitter') === false);
        $this->assertTrue(strpos($page2->render(true, true), 'Twitter') === false);
    }
}
