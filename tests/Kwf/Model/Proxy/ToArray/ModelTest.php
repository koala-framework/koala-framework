<?php
/**
 * @group Model
 * @group Model_Proxy
 */
class Kwf_Model_Proxy_ToArray_ModelTest extends Kwf_Test_TestCase
{
    public function testToArrayProxy()
    {
        $fnf = new Kwf_Model_FnF(array(
            'uniqueIdentifier' => 'unique',
            'columns' => array('id', 'firstname', 'timefield'),
            'uniqueColumns' => array('id'),
            'data' => array(
                array('id' => 1, 'firstname' => 'Max', 'timefield' => '2008-06-09 00:00:00')
            )
        ));
        $proxy = new Kwf_Model_Proxy(array('proxyModel' => $fnf));
        $result = $proxy->getRow(1)->toArray();
        $this->assertEquals(1, $result['id']);
        $this->assertEquals('Max', $result['firstname']);
        $this->assertEquals('2008-06-09 00:00:00', $result['timefield']);
    }

    public function testToArrayProxyWithSibling()
    {
        $proxy = new Kwf_Model_Proxy_ToArray_ProxyModel();
        $result = $proxy->getRow(1)->toArray();
        $this->assertEquals(1, $result['id']);
        $this->assertEquals('herbertsen', $result['sib_lastname']);
        $this->assertEquals('mch', $result['firstname']);
        $this->assertEquals('1234', $result['timefield']);
    }
}
