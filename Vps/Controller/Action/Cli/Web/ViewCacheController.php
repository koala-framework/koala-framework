<?php
class Vps_Controller_Action_Cli_Web_ViewCacheController extends Vps_Controller_Action_Cli_Abstract
{
    public static function getHelpOptions()
    {
        $ret = array();
        $ret[] = array('param' => 'componentId');
        $ret[] = array('param' => 'domain');
        return $ret;
    }

    public static function getHelp()
    {
        return "Various view cache commands";
    }

    public function generateOneAction()
    {
        Zend_Session::start(true);
        $ids = $this->_getParam('componentId');

        $ids = explode(',', $ids);
        foreach ($ids as $id) {
            $this->_generate($id);
        }
        exit;
    }

    private function _generate($componentId)
    {
        $domain = $this->_getParam('domain');
        if (!$domain) $domain = Vps_Registry::get('config')->server->domain;
        $login = Vps_Registry::get('config')->preLogin ? 'vivid:planet' : '';
        $url = 'http://' . $login . $domain . '/vps/util/render/render?componentId=' . $componentId;
        echo "$url: ";
        $b = Vps_Benchmark::start('render');
        $content = file_get_contents($url);
        $b->stop();
        echo round(strlen($content) / 1000, 2) . 'KB, ' . round($b->duration, 3) . 's';
        echo " rendered\n";
    }

    public function generateAction()
    {
        if ($this->_getParam('componentId')) $this->generateOneAction();

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

                    $this->_generate($page->componentId);
                }
                //echo "child finished\n";
                exit(0);
            }
        }
        exit;
    }
}
