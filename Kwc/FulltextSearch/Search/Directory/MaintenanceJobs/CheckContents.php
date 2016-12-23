<?php
class Kwc_FulltextSearch_Search_Directory_MaintenanceJobs_CheckContents extends Kwf_Util_Maintenance_Job_Abstract
{
    private $_options;

    public function __construct(array $options = array())
    {
        $this->_options = $options;
    }

    public function getFrequency()
    {
        return self::FREQUENCY_DAILY;
    }

    public function getPriority()
    {
        return 10; //after page meta
    }

    public function execute($debug)
    {
        $startTime = microtime(true);

        $secondsAsDurationHelper = new Kwf_View_Helper_SecondsAsDuration();

        foreach (Kwf_Util_Fulltext_Backend_Abstract::getInstance()->getSubroots() as $subroot) {

            $t = time();
            if ($debug) echo "\n[$subroot] check-for-invalid...\n";
            $cmd = Kwf_Config::getValue('server.phpCli')." bootstrap.php fulltext check-for-invalid-subroot --subroot=$subroot";
            if ($debug) $cmd .= " --debug";
            //if ($this->_getParam('silent')) $cmd .= " --silent";
            passthru($cmd, $ret);
            if ($ret) exit($ret);
            if ($debug) echo "[$subroot] check-for-invalid finished: ".$secondsAsDurationHelper->secondsAsDuration(time()-$t)."\n\n";

            $t = time();
            if ($debug) echo "\n[$subroot] check-contents...\n";
            $cmd = Kwf_Config::getValue('server.phpCli')." bootstrap.php fulltext check-contents-subroot --subroot=$subroot";
            if (isset($this->_options['skipDiff'])) $cmd .= " --skip-diff";
            if ($debug) $cmd .= " --debug";
            //if ($this->_getParam('silent')) $cmd .= " --silent";
            passthru($cmd, $ret);
            if ($ret) exit($ret);
            if ($debug) echo "[$subroot] check-contents finished: ".$secondsAsDurationHelper->secondsAsDuration(time()-$t)."\n\n";

            $t = time();
            if ($debug) echo "\n[$subroot] optimize...\n";
            Kwf_Util_Fulltext_Backend_Abstract::getInstance()->optimize($debug);
            if ($debug) echo "[$subroot] optimize finished: ".$secondsAsDurationHelper->secondsAsDuration(time()-$t)."\n\n";
        }

        if ($debug) echo "\ncomplete fulltext check-contents finished: ".$secondsAsDurationHelper->secondsAsDuration(microtime(true)-$startTime)."s\n";
    }
}
