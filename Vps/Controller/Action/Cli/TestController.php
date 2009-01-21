<?php
class Vps_Controller_Action_Cli_TestController extends Vps_Controller_Action_Cli_Abstract
{
    public static function getHelp()
    {
        return "run unit tests";
    }
    public static function getHelpOptions()
    {
        $ret = array(
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
            array(
                'param'=> 'coverage',
                'help' => 'Create a coverage report'
            ),
            array('param'=> 'log-xml'),
            array('param'=> 'log-pmd'),
            array('param'=> 'log-metrics'),
            array('param'=> 'coverage-xml'),
            array('param'=> 'coverage-html'),
            array('param'=> 'report'),
        );
        $value = self::_getConfigSectionsWithTestDomain();
        if (in_array('production', $value)) {
            unset($value[array_search('production', $value)]);
            $value = array_values($value);
        }
        if ($value) {
            $ret[] = array(
                'param'=> 'server',
                'value' => $value,
                'valueOptional' => true,
                'help' => 'Server for Selenium-Tests'
            );
        }
        return $ret;
    }

    public static function initForTests()
    {
        Zend_Session::start();
        ini_set('memory_limit', '256M');
        Zend_Registry::get('config')->debug->settingsCache = false;
        Zend_Registry::get('config')->debug->benchmark = false;
        Zend_Registry::get('config')->debug->querylog = false;
        Zend_Registry::get('config')->hasIndex = false; //zwischenlösung bis index auf models umgestellt wurde und auch getestet werden muss
        Zend_Registry::get('config')->debug->errormail = false;
        Zend_Registry::set('db', null);
        set_time_limit(0);
        Vps_Benchmark::disable();
    }

    public function indexAction()
    {
        //set_include_path('/www/public/niko/phpunit:'.get_include_path());
        self::initForTests();

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
        $arguments['verbose'] = false;
        if ($this->_getParam('verbose')) {
            $arguments['verbose'] = $this->_getParam('verbose');
        }
        if ($this->_getParam('stop-on-failure')) {
            $arguments['stopOnFailure'] = $this->_getParam('stop-on-failure');
        }
        if ($this->_getParam('log-xml')) {
            $arguments['xmlLogfile'] = $this->_getParam('log-xml');
        }
        if ($this->_getParam('log-pmd')) {
            $arguments['pmdXML'] = $this->_getParam('log-pmd');
        }
        if ($this->_getParam('log-metrics')) {
            $arguments['metricsXML'] = $this->_getParam('log-metrics');
        }
        if ($this->_getParam('coverage-xml')) {
            $arguments['coverageClover'] = $this->_getParam('coverage-xml');
        }
        if ($this->_getParam('coverage')) {
            if (!extension_loaded('tokenizer') || !extension_loaded('xdebug')) {
                throw new Vps_ClientException('tokenizer and xdebug extensions must be loaded');
            }
            if (!is_string($this->_getParam('coverage'))) {
                $arguments['reportDirectory'] = './report';
            } else {
                $arguments['reportDirectory'] = $this->_getParam('coverage');
            }
        }

        if ($this->_getParam('server')) {
            $cfg = new Zend_Config_Ini('application/config.ini', $this->_getParam('server'));
            Vps_Registry::set('testDomain', $cfg->server->domain);
            Vps_Registry::set('testServerConfig', $cfg);
        }
        if ($this->_getParam('report')) {
            $resultLogger = new Vps_Test_ResultLogger(true/*verbose*/);
            $arguments['listeners'][] = $resultLogger;
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
        if ($this->_getParam('report')) {
            $resultLogger->printResult($result);
            $info = new SimpleXMLElement(`svn info --xml`);

            $client = new Zend_Http_Client('http://zeiterfassung.niko.vivid/test_report.php');
            $client->setMethod(Zend_Http_Client::POST);
            $response = $client->request('POST');

            $client->setParameterPost(array(
                'svnPath' => (string)$info->entry->url,
                'tests' => $result->count(),
                'failures' => $result->failureCount()+$result->errorCount(),
                'skipped' => $result->skippedCount(),
                'not_implemented' => $result->notImplementedCount(),
                'log' => $resultLogger->getContent()
            ));
            $response = $client->request();
            if ($response->getBody() != 'OK') {
                throw new Vps_Exception("Can't report to zeiterfassung: ".$response->getBody());
            }
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
