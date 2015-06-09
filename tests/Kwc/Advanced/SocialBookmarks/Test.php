<?php
/**
 * @group Advanced_SocialBookmarks
 */
class Kwc_Advanced_SocialBookmarks_Test extends Kwc_TestAbstract
{
    public function testIt()
    {
        $this->_init('Kwc_Advanced_SocialBookmarks_Root');
        $page1 = $this->_root->getChildComponent('_page1');
        $page2 = $page1->getChildComponent('_page2');

        $this->assertTrue(strpos($page1->render(true, true), 'twitter') !== false);
        $this->assertTrue(strpos($page2->render(true, true), 'twitter') !== false);
        Kwf_Model_Abstract::getInstance('Kwc_Advanced_SocialBookmarks_TestModel')
            ->getRow('root-socialBookmarks')
            ->getChildRows('Networks', 2)->current()->delete();
        $this->_process();
        $this->assertTrue(strpos($page1->render(false, true), 'twitter') === false);
        $this->assertTrue(strpos($page2->render(false, true), 'twitter') === false);
        $this->assertTrue(strpos($page1->render(true, true), 'twitter') === false);
        $this->assertTrue(strpos($page2->render(true, true), 'twitter') === false);
    }
}
