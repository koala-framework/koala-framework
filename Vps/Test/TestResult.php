<?php
class Vps_Test_TestResult extends PHPUnit_Framework_TestResult
{
    private $_retryOnError;
    public function setRetryOnError($f)
    {
        $this->_retryOnError = (bool)$f;
    }
    private function _askForRetry(PHPUnit_Framework_Test $test, Exception $e, $time)
    {
        if ($e instanceof PHPUnit_Framework_IncompleteTest) return false;
        if ($e instanceof PHPUnit_Framework_SkippedTest) return false;

        if (file_exists("/www/testtimes")) {
            $app = Vps_Registry::get('config')->application->id;
            file_put_contents("/www/testtimes/failure_$app/".get_class($test), time());
        }

        if (!$this->_retryOnError) return false;
        
        $error = new PHPUnit_Framework_TestFailure($test, $e);

        if ($test instanceof PHPUnit_Framework_SelfDescribing) {
            echo $test->toString();
        } else {
            echo get_class($test);
        }
        echo "\n";
        echo $error->getExceptionAsString() .
        PHPUnit_Util_Filter::getFilteredStacktrace(
            $error->thrownException(),
            FALSE
        );

        echo "\nTest failed. Try again? [Y/n]";
        if (isset($_SERVER['USER']) && $_SERVER['USER']=='niko') {
            $msg = Vps_Registry::get('config')->application->name.' Test failed. Try again?';
            $msg = str_replace(" ", "\ ", utf8_decode($msg));
            system("ssh niko \"export DISPLAY=:0 && /usr/bin/kdialog --passivepopup $msg 2\"");
        }
        $stdin = fopen('php://stdin', 'r');
        $input = strtolower(trim(fgets($stdin, 2)));
        fclose($stdin);
        if ($input == 'j' || $input == 'y' || $input == '') {
            $this->run($test);
            return true;
        }
        return false;
    }
    public function addFailure(PHPUnit_Framework_Test $test, PHPUnit_Framework_AssertionFailedError $e, $time)
    {
        if (!$this->_askForRetry($test, $e, $time)) {
            parent::addFailure($test, $e, $time);
        }
    }

    public function addError(PHPUnit_Framework_Test $test, Exception $e, $time)
    {
        if (!$this->_askForRetry($test, $e, $time)) {
            parent::addError($test, $e, $time);
        }
    }
}
