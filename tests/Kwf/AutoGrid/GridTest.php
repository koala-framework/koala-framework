<?php
/**
 * @group AutoGrid
 */
class Vps_AutoGrid_GridTest extends Vps_Test_TestCase
{
    public function testExprEqualsSelect()
    {
        $request = new Vps_Test_Request_Simple('index', null, null, array());
        $controller = new Vps_AutoGrid_TestWhereEqualsController($request, new Zend_Controller_Response_Http());
        $controller->preDispatch();
        $this->assertEquals(1, $controller->fetchData(array('field' => 'id', 'direction' => 'ASC'), 50, 0)->count());
    }

    public function testExprContainsSelect()
    {
        $request = new Vps_Test_Request_Simple('index', null, null, array());
        $controller = new Vps_AutoGrid_TestContainsController($request, new Zend_Controller_Response_Http());
        $controller->preDispatch();
        $data = $controller->fetchData(array('field' => 'id', 'direction' => 'ASC'), 50, 0);
        $this->assertEquals(3, $data->count());
        $this->assertEquals('Klaus', $data->current()->value);
    }

    public function testFilterContains()
    {
        $request = new Vps_Test_Request_Simple('index', null, null, array('query' => 'a'));
        $controller = new Vps_AutoGrid_TestFilterController($request, new Zend_Controller_Response_Http());
        $controller->preDispatch();
        $this->assertEquals(4, $controller->fetchData(array('field' => 'id', 'direction' => 'ASC'), 50, 0)->count());
    }

    public function testFilterContainsNothing()
    {
        $request = new Vps_Test_Request_Simple('index', null, null, array('query' => 'aaaaaa'));
        $controller = new Vps_AutoGrid_TestFilterController($request, new Zend_Controller_Response_Http());
        $controller->preDispatch();
        $this->assertEquals(0, $controller->fetchData(array('field' => 'id', 'direction' => 'ASC'), 50, 0)->count());
    }

    public function testFilterContainsSeperator()
    {
        $request = new Vps_Test_Request_Simple('index', null, null, array('query' => 'a,us'));
        $controller = new Vps_AutoGrid_TestFilterController($request, new Zend_Controller_Response_Http());
        $controller->preDispatch();
        $data = $controller->fetchData(array('field' => 'id', 'direction' => 'ASC'), 50, 0);
        $this->assertEquals(2, $data->count());
        $this->assertEquals('Klaus', $data->current()->value);
    }

    public function testFilterContainsColon()
    {
        $request = new Vps_Test_Request_Simple('index', null, null, array('query' => 'id:4'));
        $controller = new Vps_AutoGrid_TestFilterController($request, new Zend_Controller_Response_Http());
        $controller->preDispatch();
        $data = $controller->fetchData(array('field' => 'id', 'direction' => 'ASC'), 50, 0);
        $this->assertEquals(1, $data->count());
        $this->assertEquals('Rainer', $data->current()->value);
    }

    public function testFilterContainsColonValue()
    {
        $request = new Vps_Test_Request_Simple('index', null, null, array('query' => 'value:Kurt'));
        $controller = new Vps_AutoGrid_TestFilterController($request, new Zend_Controller_Response_Http());
        $controller->preDispatch();
        $data = $controller->fetchData(array('field' => 'id', 'direction' => 'ASC'), 50, 0);
        $this->assertEquals(1, $data->count());
        $this->assertEquals('Kurt', $data->current()->value);
    }

    public function testFilterTextColumn()
    {
        $request = new Vps_Test_Request_Simple('index', null, null, array(
            'queryTextColumn_text' => 'Kurt', 'queryTextColumn_column' => '0'
        ));
        $controller = new Vps_AutoGrid_TestFilterColumnController($request, new Zend_Controller_Response_Http());
        $controller->preDispatch();
        $data = $controller->fetchData(array('field' => 'id', 'direction' => 'ASC'), 50, 0);
        $this->assertEquals(2, $data->count());
        $this->assertEquals('Kurt', $data->current()->value);
        $this->assertEquals('Herbert', $data->current()->value2);

        $request = new Vps_Test_Request_Simple('index', null, null, array(
            'queryTextColumn_text' => 'Kurt', 'queryTextColumn_column' => 'value2'
        ));
        $controller = new Vps_AutoGrid_TestFilterColumnController($request, new Zend_Controller_Response_Http());
        $controller->preDispatch();
        $data = $controller->fetchData(array('field' => 'id', 'direction' => 'ASC'), 50, 0);
        $this->assertEquals(1, $data->count());
        $this->assertEquals('Klaus', $data->current()->value);
        $this->assertEquals('Kurt', $data->current()->value2);
    }

    public function testFilterQueryId()
    {
        $request = new Vps_Test_Request_Simple('index', null, null, array('queryId' => '5'));
        $controller = new Vps_AutoGrid_TestFilterController($request, new Zend_Controller_Response_Http());
        $controller->preDispatch();
        $data = $controller->fetchData(array('field' => 'id', 'direction' => 'ASC'), 50, 0);
        $this->assertEquals(1, $data->count());
        $this->assertEquals('Franz', $data->current()->value);
    }

    public function testFilterQueryNull()
    {
        $request = new Vps_Test_Request_Simple('index', null, null, array('query' => ''));
        $controller = new Vps_AutoGrid_TestFilterController($request, new Zend_Controller_Response_Http());
        $controller->preDispatch();
        $data = $controller->fetchData(array('field' => 'id', 'direction' => 'ASC'), 50, 0);
        $this->assertEquals(7, $data->count());
    }

    public function testFilterDate()
    {
        $request = new Vps_Test_Request_Simple('index', null, null, array('testtime_from' => '2008-12-10', 'testtime_to' => '2008-12-17'));
        $controller = new Vps_AutoGrid_TestDateRangeController($request, new Zend_Controller_Response_Http());
        $controller->preDispatch();
        $data = $controller->fetchData(array('field' => 'id', 'direction' => 'ASC'), 50, 0);
        $this->assertEquals(3, $data->count());

        $request = new Vps_Test_Request_Simple('index', null, null, array('testtime_from' => '2008-12-09', 'testtime_to' => '2008-12-17'));
        $controller = new Vps_AutoGrid_TestDateRangeController($request, new Zend_Controller_Response_Http());
        $controller->preDispatch();
        $data = $controller->fetchData(array('field' => 'id', 'direction' => 'ASC'), 50, 0);
        $this->assertEquals(4, $data->count());

    }

    public function testFilterSkipWhere()
    {
        $request = new Vps_Test_Request_Simple('index', null, null, array('query' => '3'));
        $controller = new Vps_AutoGrid_TestSkipWhereController($request, new Zend_Controller_Response_Http());
        $controller->preDispatch();
        $data = $controller->fetchData(array('field' => 'id', 'direction' => 'ASC'), 50, 0);
        $this->assertEquals(7, $data->count());
    }

    public function testGetExpressionsSelect()
    {
        $request = new Vps_Test_Request_Simple('index', null, null, array('query' => 't'));
        $controller = new Vps_AutoGrid_TestGetExpressionsController($request, new Zend_Controller_Response_Http());
        $controller->preDispatch();
        $this->assertEquals(5, $controller->fetchData(array('field' => 'id', 'direction' => 'ASC'), 50, 0)->count());
    }

}