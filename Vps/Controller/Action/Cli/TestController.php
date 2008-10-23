<?php
class Vps_Controller_Action_Cli_TestController extends Vps_Controller_Action_Cli_Abstract
{
    public static function getHelp()
    {
        return "run unit tests";
    }
    public static function getHelpOptions()
    {
        return array(
            array(
                'param'=> 'filter',
                'type' => 'string',
                'help' => 'Filter which tests to run'
            ),
            array(
                'param'=> 'group',
                'type' => 'string',
                'help' => 'Only runs tests from the specified group(s)'
            ),
            array(
                'param'=> 'exclude-group',
                'type' => 'string',
                'help' => 'Exclude tests from the specified group(s)'
            ),
            array(
                'param'=> 'verbose',
                'help' => 'Output more verbose information',
            ),
            array(
                'param'=> 'stop-on-failure',
                'help' => 'Stop execution upon first error or failure'
            ),
        );
    }
    public function indexAction()
    {
        Zend_Registry::get('config')->debug->settingsCache = false;
        Zend_Registry::get('config')->debug->benchmark = false;
        Zend_Registry::get('config')->debug->querylog = false;
        Zend_Registry::set('db', null);
        Vps_Benchmark::disable();

        $arguments = array();
        $arguments['colors'] = true;
        if ($this->_getParam('filter')) {
            $arguments['filter'] = $this->_getParam('filter');
        }
        if ($this->_getParam('group')) {
            $arguments['groups'] = explode(',', $this->_getParam('group'));
        }
        if ($this->_getParam('exclude-group')) {
            $arguments['excludeGroups'] = explode(',', $this->_getParam('exclude-group'));
        }
        if ($this->_getParam('verbose')) {
            $arguments['verbose'] = $this->_getParam('verbose');
        }
        if ($this->_getParam('stop-on-failure')) {
            $arguments['stopOnFailure'] = $this->_getParam('stop-on-failure');
        }
        $suite = new Vps_Test_TestSuite();
        $runner = new PHPUnit_TextUI_TestRunner;

        try {
            $result = $runner->doRun(
              $suite,
              $arguments
            );
        }

        catch (Exception $e) {
            throw new Vps_ClientException(
              'Could not create and run test suite: ' . $e->getMessage()
            );
        }

        if ($result->wasSuccessful()) {
            exit(PHPUnit_TextUI_TestRunner::SUCCESS_EXIT);
        }

        else if ($result->errorCount() > 0) {
            exit(PHPUnit_TextUI_TestRunner::EXCEPTION_EXIT);
        }

        else {
            exit(PHPUnit_TextUI_TestRunner::FAILURE_EXIT);
        }

        $this->_helper->viewRenderer->setNoRender(true);
    }
}
