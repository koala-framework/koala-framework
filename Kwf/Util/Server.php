<?php
class Vps_Util_Server
{
    public function export($config, $options)
    {
        $sshHost = $config->server->user.'@'.$config->server->host.':'.$config->server->port;
        $sshDir = $config->server->dir;

        $params = '';

        if (isset($config->server->useVpsForUpdate) && !$config->server->useVpsForUpdate) {
            echo "updating $sshHost:$sshDir\n";
            $cmd = "svn up{$params}";
            $cmd = "sshvps $sshHost $sshDir $cmd";
            $cmd = "sudo -u vps $cmd";
            if (isset($options['debug']) && $options['debug']) {
                echo $cmd."\n";
            }
            self::_systemCheckRet($cmd);
        } else {
            if (isset($options['with-library']) && $options['with-library']) {
                $params .= ' --with-library';
            }
            if (isset($options['skip-update']) && $options['skip-update']) {
                $params .= ' --skip-update';
            }
            $cmd = "svn-up{$params}";
            $cmd = "sshvps $sshHost $sshDir $cmd";
            $cmd = "sudo -u vps ".Vps_Util_Git::getAuthorEnvVars()." $cmd";
            if (isset($options['debug']) && $options['debug']) {
                echo $cmd."\n";
            }
            self::_systemCheckRet($cmd);
        }
    }

    private static function _systemCheckRet($cmd)
    {
        $ret = null;
        passthru($cmd, $ret);
        if ($ret != 0) throw new Vps_ClientException("Command failed");
    }
}