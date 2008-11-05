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
            file_put_contents('application/update', $currentRevision);
            echo "No application/update revision found, wrote current revision ($currentRevision)\n";
            exit;
        }
        $updateRevision = (int)file_get_contents('application/update');
        if (!$updateRevision) {
            throw new Vps_ClientException("Invalid application/update revision");
        }
        $from = $updateRevision;
        $to = $currentRevision;
        if ($this->_getParam('current')) {
            $to++;
        }
        if ($from == $to) {
            echo "Already up-to-date\n\n";
        } else {
            echo "Looking for update-scripts from revistion $from to {$to}...";
            try {
                $updates = Vps_Update::getUpdates($from, $to);
                echo " found ".count($updates)."\n";
                foreach ($updates as $update) {
                    echo "executing ".get_class($update)."...\n";
                    $res = $update->update();
                    if ($res) {
                        print_r($res);
                    }
                }
                echo "\033[32msucessfully updated\033[37m\n";

                file_put_contents('application/update', $currentRevision);
            } catch (Vps_ClientException $e) {
                echo "\033[31mError:\033[37m\n";
                echo $e->getMessage()."\n";
            }
        }

        $this->_forward('index', 'clear-cache');
        $this->_helper->viewRenderer->setNoRender(true);
    }
}
