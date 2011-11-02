<?php
/**
 * @group Component_Events
 * @group Component_Events_PseudoPage
 */
class Kwf_Component_Events_PseudoPage_Test extends Kwc_TestAbstract
{
    public function setUp()
    {
        parent::setUp('Kwf_Component_Events_PseudoPage_Component');
    }

    public function testEvents()
    {
        $events = Kwf_Component_Events_PseudoPage_Events::getInstance(
            'Kwf_Component_Events_PseudoPage_Events',
            array('componentClass' => 'Kwf_Component_Events_PseudoPage_Component')
        );

        $model = Kwf_Model_Abstract::getInstance('Kwf_Component_Events_PseudoPage_Model');
        $row = $model->getRow(1);

        $row->visible = 1;
        $row->save();
        $this->assertEquals(0, $events->countCalled);

        $row->name = 'bar';
        $row->save();
        $this->assertEquals(2, $events->countCalled);
    }
}