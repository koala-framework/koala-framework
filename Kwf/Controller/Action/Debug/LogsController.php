<?php
class Kwf_Controller_Action_Debug_LogsController extends Kwf_Controller_Action_Auto_Grid
{
    protected $_buttons = array('delete', 'deleteAll', 'deleteFiles', 'parse');
    protected $_model = 'Kwf_Log_Model';
    protected $_paging = 25;
    protected $_defaultOrder = array(
        'direction' => 'DESC',
        'field' => 'date'
    );

    public function indexAction()
    {
        parent::indexAction();
        $this->view->xtype = 'kwf.logs.panel';
        $this->view->controllerUrl = '/kwf/debug/logs';
        $this->view->formControllerUrl = '/kwf/debug/logs-form';
    }

    protected function _initColumns()
    {
        parent::_initColumns();

        $this->_filters['type'] = array(
            'type'   => 'ComboBox',
            'text'   => trlKwf('Type'),
            'data'   => array(
                array('error', trlKwf('Error')),
                array('accessdenied', trlKwf('Access Denied')),
                array('notfound', trlKwf('Not Found')),
            ),
            'width'  => 100
        );

        $this->_filters['text'] = array(
            'type'=>'TextField',
            'width' => 300
        );

        $columns = $this->_columns;
        $columns->add(new Kwf_Grid_Column_Datetime('date'));
        $columns->add(new Kwf_Grid_Column('type', trlKwf('Type'), 80));
        $columns->add(new Kwf_Grid_Column('message', trlKwf('Message'), 300));
        $columns->add(new Kwf_Grid_Column('request_uri', trlKwf('Uri'), 200))
            ->setRenderer('clickableLink');
        $columns->add(new Kwf_Grid_Column('http_referer', trlKwf('Referer'), 200))
            ->setRenderer('clickableLink');
        $columns->add(new Kwf_Grid_Column('user', trlKwf('User'), 200));
    }

    public function jsonParseFilesAction()
    {
        $pattern = array(
            'start' => array(
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
            ),
            'end' => array(
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
            )
        );

        $files = glob('log/*/*/*');
        if (empty($files)) {
            throw new Kwf_Exception_Client(trlKwf('No log files found.'));
        }

        $logMessage = array();
        foreach ($files as $filename) {
            $fileInfo = pathinfo($filename);
            $folders = explode('/', $fileInfo['dirname']);
            $file = file_get_contents($filename);

            for($i = 0; $i < count($pattern['start']); $i++) {
                preg_match_all('/('.preg_quote($pattern['start'][$i]).')(.+?)('.preg_quote($pattern['end'][$i]).')/si', $file, $matches);
                if (empty($matches[1])) continue;

                $logMessage[trim(str_replace('**', '', $matches[1][0]))] = trim($matches[2][0]);
            }

            $select = new Kwf_Model_Select();
            $select->whereEquals('filename', $fileInfo['filename']);
            if ($this->_getModel()->countRows($select) > 0) continue;

            $this->_getModel()->createRow(array(
                'type' => $folders[1],
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
                'filename' => $fileInfo['filename'],
                'date' => $folders[2] . ' ' . $logMessage['Time']
            ))->save();
        }

        $this->view->message = trlKwf('All files successfully parsed');
    }

    public function jsonDeleteAllAction()
    {
        $count = $this->_getModel()->countRows(array());
        if (!$count) {
            throw new Kwf_Exception_Client(trlKwf('Logs are empty. Nothing found to delete.'));
        }

        $select = $this->_getSelect();
        $count2 = $this->_getModel()->countRows($select);
        $this->_model->deleteRows($select);
        $this->view->message = trlKwf(
            '{0} of {1} logs deleted.',
            array($count2, $count)
        );
    }

    public function jsonDeleteFilesAction()
    {
        $files = glob('log/*/*/*');
        if (empty($files)) {
            throw new Kwf_Exception_Client(trlKwf('No log files found.'));
        }

        $dirNames = array();
        foreach ($files as $filename) {
            $dirName = dirname($filename);
            if (!isset($dirName)) $dirNames[$dirName] = true;
            unlink($filename);
        }

        foreach ($dirNames as $key => $value) {
            rmdir($key);
        }

        $this->view->message = trlKwf('All log files deleted.');
    }
}

