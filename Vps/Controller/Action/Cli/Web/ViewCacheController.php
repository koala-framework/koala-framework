<?php
class Vps_Controller_Action_Cli_Web_ViewCacheController extends Vps_Controller_Action_Cli_Abstract
{
    public static function getHelp()
    {
        return "various view cache commands";
    }
    protected function _callProcessInput($c)
    {
        $process = $c->getRecursiveChildComponents(array(
                    'page' => false,
                    'flags' => array('processInput' => true)
                ));
        if (Vps_Component_Abstract::getFlag($c->componentClass, 'processInput')) {
            $process[] = $c;
        }
        $postData = array();
        foreach ($process as $i) {
            Vps_Benchmark::count('processInput', $i->componentId);
            if (method_exists($i->getComponent(), 'preProcessInput')) {
                $i->getComponent()->preProcessInput($postData);
            }
        }
        foreach ($process as $i) {
            if (method_exists($i->getComponent(), 'processInput')) {
                $i->getComponent()->processInput($postData);
            }
        }
    }
    
    public function generateAction()
    {
        $queueFile = 'application/temp/viewCacheGenerateQueue';
        $processedFile = 'application/temp/viewCacheGenerateProcessed';

        $componentId = 'root';
        file_put_contents($processedFile, $componentId);
        file_put_contents($queueFile, $componentId);
        while(true) {
            $pid = pcntl_fork();
            if ($pid == -1) {
                throw new Vps_Exception("fork failed");
            } else if ($pid) {
                //parent process
                pcntl_wait($status); //Schützt uns vor Zombie Kindern
                if ($status != 0) {
                    throw new Vps_Exception("child process failed");
                }

                //echo "memory_usage (parent): ".(memory_get_usage()/(1024*1024))."MB\n";
                if (!file_get_contents($queueFile)) {
                    echo "fertig.\n";
                    break;
                }
            } else {

                Zend_Session::start(true);
                while (true) {
                    //child process

                    //echo "memory_usage (child): ".(memory_get_usage()/(1024*1024))."MB\n";
                    if (memory_get_usage() > 50*1024*1024) {
                        echo "new process...\n";
                        break;
                    }

                    $queue = file_get_contents($queueFile);
                    if (!$queue) break;

                    $queue = explode("\n", $queue);
                    //echo "queued: ".count($queue)."\n";
                    $componentId = array_shift($queue);
                    file_put_contents($queueFile, implode("\n", $queue));

                    //echo "==> ".$componentId.' ';
                    $page = Vps_Component_Data_Root::getInstance()->getComponentById($componentId);
                    //echo "$page->url\n";
                    foreach ($page->getChildPseudoPages(array(), array('pseudoPage'=>false)) as $c) {
                        //echo "queued $c->componentId\n";
                        if (!in_array($c->componentId, file($processedFile))) {
                            file_put_contents($processedFile, "\n".$c->componentId, FILE_APPEND);
                            $queue[] = $c->componentId;
                            file_put_contents($queueFile, implode("\n", $queue));
                        }
                    }

                    if (!$page->isPage) continue;
                    if (is_instance_of($page->componentClass, 'Vpc_Abstract_Feed_Component')) continue;

                    echo "$page->componentId $page->url...";
                    try {
                        $this->_callProcessInput($page);
                    } catch (Vps_Exception_AccessDenied $e) {
                        echo " Access Denied [skipping]\n";
                        continue;
                    }
                    echo " processedInput";
                    try {
                        $page->render();
                    } catch (Vps_Exception_AccessDenied $e) {
                        echo " Access Denied [skipping]\n";
                        continue;
                    }
                    echo " rendered\n";
                }
                //echo "child finished\n";
                exit(0);
            }
        }
        exit;
    }
}
