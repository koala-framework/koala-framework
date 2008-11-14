<?php
class Vps_Controller_Action_Cli_UpdateController extends Vps_Controller_Action_Cli_Abstract
{
    public static function getHelp()
    {
        return 'Update to current version';
    }
    public static function getHelpOptions()
    {
        return array(
            array(
                'param'=> 'current',
                'help' => 'Also execute updates for current revision'
            )
        );
    }
    public function indexAction()
    {
        echo "Update\n";
        $currentRevision = false;
        try {
            $info = new SimpleXMLElement(`svn info --xml`);
            $currentRevision = (int)$info->entry['revision'];
        } catch (Exception $e) {}
        if (!$currentRevision) {
            throw new Vps_ClientException("Can't detect current revision");
        }

        if (!file_exists('application/update')) {
            file_put_contents('application/update', serialize(array('start' => $currentRevision)));
            echo "No application/update revision found, wrote current revision ($currentRevision)\n";
            exit;
        }
        $updateRevision = file_get_contents('application/update');
        if (is_numeric($updateRevision)) {
            $updateRevision = array('start' => $updateRevision);
        } else {
            $updateRevision = unserialize($updateRevision);
        }
        if (!$updateRevision) {
            throw new Vps_ClientException("Invalid application/update revision");
        }
        if (!isset($updateRevision['done'])) $updateRevision['done'] = array();
        $from = $updateRevision['start'];
        $to = $currentRevision;
        if ($this->_getParam('current')) {
            $to++;
        }
        if ($from == $to) {
            echo "Already up-to-date\n\n";
        } else {
            echo "Looking for update-scripts from revistion $from to {$to}...";
            $updates = Vps_Update::getUpdates($from, $to);
            foreach ($updates as $k=>$u) {
                if (in_array($u->getRevision(), $updateRevision['done'])) {
                    if ($this->_getParam('current') && $u->getRevision() == $to-1) continue;
                    unset($updates[$k]);
                }
            }
            echo " found ".count($updates)."\n\n";
            if ($this->_executeUpdate($updates, 'checkSettings')) {
                Vps_Controller_Action_Cli_ClearCacheController::clearCache();
                $this->_executeUpdate($updates, 'preUpdate');
                $this->_executeUpdate($updates, 'update');
                Vps_Controller_Action_Cli_ClearCacheController::clearCache();
                $this->_executeUpdate($updates, 'postUpdate');
                Vps_Controller_Action_Cli_ClearCacheController::clearCache();
                echo "\ncleared cache";
                echo "\n\033[32mupdate finished\033[0m\n";
                foreach ($updates as $k=>$u) {
                    if (!in_array($u->getRevision(), $updateRevision['done'])) {
                        $updateRevision['done'][] = $u->getRevision();
                    }
                }
                file_put_contents('application/update', serialize($updateRevision));
            } else {
                echo "\nupdate stopped\n";
            }
        }

        $this->_helper->viewRenderer->setNoRender(true);
    }

    private function _executeUpdate($updates, $method)
    {
        $ret = true;
        foreach ($updates as $update) {
            if ($method != 'checkSettings') echo "executing $method ".get_class($update)."... ";
            $e = false;
            try {
                $res = $update->$method();
            } catch (Exception $e) {
                if ($method == 'checkSettings') {
                    echo get_class($update);
                }
                echo "\n\033[31mError:\033[0m\n";
                echo $e->getMessage()."\n\n";
                $ret = false;
            }
            if (!$e) {
                if ($method != 'checkSettings') echo "\033[32 OK \033[0m\n";
                if ($res) {
                    print_r($res);
                }
            }
        }
        return $ret;
    }
}
