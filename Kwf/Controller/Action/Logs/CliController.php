<?php
/**
 * Example for Acl MenuUrl
 *
 * $this->add(new Kwf_Acl_Resource_MenuUrl('kwf_debug_logs',
 *     array('text'=>trlKwf('Logs'), 'icon'=>'script_error.png')), 'settings');
 * $this->add(new Zend_Acl_Resource('kwf_debug_logs-form'), 'kwf_debug_logs');
 **/
class Kwf_Controller_Action_Logs_CliController extends Kwf_Controller_Action_Cli_Abstract
{
    protected $_ignore = array('.', '..', '.gitignore', 'srpc-call', 'mirrorcache', 'clear-view-cache');

    protected $_start = array(
        '** Exception **',
        '** Thrown **',
        '** Message **',
        '** ExceptionDetail **',
        '** REQUEST_URI **',
        '** HTTP_REFERER **',
        '** User **',
        '** Time **',
        '** _USERAGENT **',
        '** _GET **',
        '** _POST **',
        '** _SERVER **',
        '** _FILES **',
        '** _SESSION **'
    );
    protected $_end = array(
        '-- Exception --',
        '-- Thrown --',
        '-- Message --',
        '-- ExceptionDetail --',
        '-- REQUEST_URI --',
        '-- HTTP_REFERER --',
        '-- User --',
        '-- Time --',
        '-- _USERAGENT --',
        '-- _GET --',
        '-- _POST --',
        '-- _SERVER --',
        '-- _FILES --',
        '-- _SESSION --'
    );

    public static function getHelp()
    {
        return "parse all log files for backend view";
    }

    public function indexAction()
    {
        $config = Kwf_Config::getValueArray('server.updateTags');
        if (in_array('log', $config) === false) throw new Kwf_Exception('add log updateTag in config');

        $model = Kwf_Model_Abstract::getInstance('Kwf_Log_Model');
        if ($logHandle = opendir('log')) {
            while (false !== ($logFolder = readdir($logHandle))) {
                if (in_array($logFolder, $this->_ignore)) continue;

                if ($logSubHandle = opendir('log/' . $logFolder)) {
                    while (false !== ($logSubFolder = readdir($logSubHandle))) {
                        if (in_array($logSubFolder, $this->_ignore)) continue;

                        if ($logFilesHandle = opendir('log/' . $logFolder . '/' . $logSubFolder)) {
                            while (false !== ($logFile = readdir($logFilesHandle))) {
                                if (in_array($logFile, $this->_ignore)) continue;

                                $file = file_get_contents('log/' . $logFolder . '/' . $logSubFolder . '/' . $logFiles);
                                for($i = 0; $i < count($this->_start); $i++) {
                                    preg_match_all('/('.preg_quote($this->_start[$i]).')(.+?)('.preg_quote($this->_end[$i]).')/si', $file, $matches);
                                    if (empty($matches[1])) continue;

                                    $logMessage[trim(str_replace('**', '', $matches[1][0]))] = trim($matches[2][0]);
                                }

                                $select = new Kwf_Model_Select();
                                $select->whereEquals('filename', $logFile);
                                if ($model->countRows($select) > 0) continue;

                                $model->createRow(array(
                                    'type' => $logFolder,
                                    'exception' => $logMessage['Exception'],
                                    'thrown' => $logMessage['Thrown'],
                                    'message' => $logMessage['Message'],
                                    'exception_detail' => $logMessage['ExceptionDetail'],
                                    'request_uri' => $logMessage['REQUEST_URI'],
                                    'http_referer' => $logMessage['HTTP_REFERER'],
                                    'user' => $logMessage['User'],
                                    'useragent' => $logMessage['_USERAGENT'],
                                    'get' => $logMessage['_GET'],
                                    'post' => $logMessage['_POST'],
                                    'server' => $logMessage['_SERVER'],
                                    'files' => $logMessage['_FILES'],
                                    'session' => $logMessage['_SESSION'],
                                    'date' => $logSubFolder . ' ' . $logMessage['Time']
                                ))->save();
                            }
                            closedir($logFilesHandle);
                        }
                    }
                    closedir($logSubHandle);
                }
            }

            closedir($logHandle);
        }


        $this->_helper->viewRenderer->setNoRender(true);
    }

}

