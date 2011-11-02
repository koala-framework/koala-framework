<?php
/**
 * @group Component_Events
 * @group Component_Events_Pages
 */
class Kwf_Component_Events_Pages_Test extends Kwc_TestAbstract
{
    public function setUp()
    {
        parent::setUp('Kwf_Component_Events_Pages_Component');
    }

    public function testEvents()
    {
        $events = Kwf_Component_Events_Pages_Events::getInstance(
            'Kwf_Component_Events_Pages_Events',
            array('componentClass' => 'Kwf_Component_Events_Pages_Component')
        );

        $model = Kwf_Model_Abstract::getInstance('Kwf_Component_Events_Pages_Model');
        $count = 0;

        $row = $model->getRow(4);
        $row->visible = 1;
        $row->save();
        $this->assertEquals(++$count, $events->countCalled);

        $row = $model->getRow(3);
        $row->parent_id = 'root';
        $row->save();
        $this->assertEquals(++$count, $events->countCalled);
    }
}