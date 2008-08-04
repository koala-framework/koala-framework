<?php
class Vps_Controller_Action_Cli_VpsTagController extends Vps_Controller_Action_Cli_Abstract
{
    public static function getHelp()
    {
        return "create new vps-tag";
    }
    public static function getHelpOptions()
    {
        return array(
            array(
                'param' => 'branch',
                'value' => 'trunk/vps',
                'valueOptional'=>true,
                'help' => 'enable debug output'
            ),array(
                'param'=> 'version',
                'value' => '0.8.0',
                'help' => 'new version-number, 0.8.x to autogenerate next higher one'
            )
        );
    }

    public function indexAction()
    {
        $svnBase = 'file:///var/lib/svn';

        $branch = $this->_getParam('branch');
        $version = $this->_getParam('version');

        $versions = array();
        foreach (explode("\n", `svn ls $svnBase/tags/vps`) as $i) {
            if ($i) {
                $versions[] = trim($i, '/');
            }
        }

        if (substr($version, -1) == 'x') {
            $version = substr($version, 0, -1);
            $max = -1;
            foreach ($versions as $v) {
                if (substr($v, 0, strlen($version)) == $version) {
                    $i = (int)substr($v, strlen($version));
                    if ($i > $max) $max = $i;
                }
            }
            if ($max < 0) {
                throw new Vps_ClientException("No Tag starting with '$version' found");
            }
            $version .= $max+1;
        }

        if (!preg_match('#^[0-9]+\.[0-9]+\.[0-9]+-[0-9]*$#', $version)) {
            throw new Vps_ClientException("Invalid version number: '$version'");
        }
        if (in_array($version, $versions)) {
            throw new Vps_ClientException("Tag '$version' exists allready");
        }

        passthru("svn cp $svnBase/$branch $svnBase/tags/vps/$version -m \"created new version through vps-cli\"", $ret);
        if (!$ret) {
            echo "Tag $version successfully created.\n";
        }
        exit($ret);
    }
}
