<?php
class Kwf_Controller_Action_Cli_TestController extends Kwf_Controller_Action_Cli_Abstract
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
            array(
                'param'=> 'retry-on-error',
            ),
            array('param'=> 'log-xml'),
            array('param'=> 'log-pmd'),
            array('param'=> 'log-metrics'),
            array('param'=> 'coverage-xml'),
            array('param'=> 'coverage-html'),
            array('param'=> 'report'),
            array('param'=> 'no-progress'),
            array('param'=> 'disable-debug'),
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
        Kwf_Session::start();
        ini_set('memory_limit', '512M');

        Kwf_Component_Data_Root::setComponentClass(false);
        Zend_Registry::set('db', Kwf_Test::getTestDb());

        set_time_limit(0);
        Kwf_Benchmark::disable();
    }

    public function indexAction()
    {
        self::initForTests();

        if (!Kwf_Registry::get('config')->server->domain) {
            throw new Kwf_Exception_Client("Can't run tests; server.domain is not set. Please set in tests/config.local.ini");
        }
        $arguments = array();
        $arguments['colors'] = true;
        $arguments['filter'] = false;
        if ($this->_getParam('filter')) {
            $arguments['filter'] = $this->_getParam('filter');
        }
        $arguments['groups'] = array();
        if ($this->_getParam('group')) {
            $arguments['groups'] = explode(',', $this->_getParam('group'));
        }
        $arguments['excludeGroups'] = array();
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
        if ($this->_getParam('retry-on-error')) {
            $arguments['retryOnError'] = $this->_getParam('retry-on-error');
        }

        if ($this->_getParam('coverage')) {
            if (!extension_loaded('tokenizer') || !extension_loaded('xdebug')) {
                throw new Kwf_ClientException('tokenizer and xdebug extensions must be loaded');
            }
            if (!is_string($this->_getParam('coverage'))) {
                $arguments['reportDirectory'] = './report';
            } else {
                $arguments['reportDirectory'] = $this->_getParam('coverage');
            }
        }

        Kwf_Registry::set('testDomain', Kwf_Registry::get('config')->server->domain);
        Kwf_Registry::set('testServerConfig', Kwf_Registry::get('config'));

        if ($this->_getParam('report')) {
            $resultLogger = new Kwf_Test_ResultLogger(true/*verbose*/);
            $arguments['listeners'][] = $resultLogger;
        }
        if ($this->_getParam('testdox')) {
            $arguments['printer'] = new PHPUnit_Util_TestDox_ResultPrinter_Text;
            $arguments['noProgress'] = true;
        } else if ($this->_getParam('no-progress')) {
            $arguments['noProgress'] = true;
        }
        if ($this->_getParam('disable-debug')) {
            Kwf_Debug::disable();
        }

        //nur temporär deaktiviert, damit ich selenium-verbindungs-probleme besser debuggen kann
        PHPUnit_Util_Filter::setFilter(false);

        $runner = new Kwf_Test_TestRunner();
        $suite = new Kwf_Test_TestSuite();

        Kwf_Model_Abstract::clearInstances();
        Kwf_Trl::getInstance()->setModel(null, 'web');
        Kwf_Trl::getInstance()->setModel(null, 'kwf');

        $result = $runner->doRun(
            $suite,
            $arguments
        );

        if ($this->_getParam('report')) {
            $resultLogger->printResult($result);

            $reportData = array(
                'tests' => $result->count(),
                'failures' => $result->failureCount()+$result->errorCount(),
                'skipped' => $result->skippedCount(),
                'not_implemented' => $result->notImplementedCount(),
                //'log' => $resultLogger->getContent(),
                'kwf_version' => Kwf_Util_Git::kwf()->getActiveBranch().' ('.Kwf_Util_Git::kwf()->revParse('HEAD').')'
            );
            if (Kwf_Registry::get('config')->application->id != 'kwf') {
                $reportData['web_version'] = Kwf_Util_Git::web()->getActiveBranch().' ('.Kwf_Util_Git::web()->revParse('HEAD').')';
            }
            echo "===REPORT===";
            echo serialize($reportData);
            echo "===/REPORT===";
        }

        Kwf_Benchmark::shutDown();

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

    public function forwardAction()
    {
        $this->_forward($this->_getParam('action'), $this->_getParam('controller'), 'kwf_test');
    }
}
