<?php
class Vps_Controller_Action_Debug_SqlController extends Vps_Controller_Action
{
    public function jsonDataAction()
    {
        $this->view->requestNum = false;

        $n = $this->_getParam('requestNum');
        $file = '/tmp/querylog.'.$n;
        if (!file_exists($file)) {
            throw new Vps_ClientException("No Querylog found for this requestNum");
        }
        $c = explode("\nquerylog\n", file_get_contents($file));
        $j = 0;
        $db = Zend_Registry::get('db');
        $this->view->data = array();
        foreach ($c as $i) {
            if ($i == '') continue;
            $i = unserialize($i);
            $row = array();
            $row[] = ++$j;
            $row[] = $i['query'];
            $row[] = $i['time'];
            $row[] = $i['type'];
            $row[] = $i['params'];
            if ($i['type'] == Zend_Db_Profiler::SELECT) {
                $rows = $db->query($i['query'], $i['params'])->fetchAll();
                $row[] = count($rows);
                $rows = $db->query('EXPLAIN '.$i['query'], $i['params'])->fetchAll();
                $explainRows = 1;
                foreach ($rows as $r) {
                    $explainRows *= $r['rows'];
                }
                $row[] = $explainRows;
            } else {
                $row[] = null;
                $row[] = null;
            }
            $row[] = implode("", $i['backtrace']);
            $this->view->data[] = $row;
        }
    }
    public function jsonExplainAction()
    {
        $this->view->requestNum = false;

        $query = $this->_getParam('query');
        $db = Zend_Registry::get('db');
        $this->view->data = array();
        foreach ($db->query("EXPLAIN $query")->fetchAll() as $row) {
            $this->view->data[] = array_values($row);
        }
    }

    public function jsonQuerycountAction()
    {
        $this->view->requestNum = false;

        $db = Zend_Registry::get('db');

        $this->view->data = array();
        foreach (explode(';', $this->_getParam('requestNums')) as $nr) {
//         p($this->_getParam('requestNums'));
//         d(explode(';', $this->_getParam('requestNums')));
            $file = '/tmp/querylog.'.$nr;
            if (!file_exists($file)) continue;
            $c = explode("\nquerylog\n", file_get_contents($file));
            $queries = 0;
            $rows = 0;
            $explainRows = 0;
            foreach ($c as $i) {
                if ($i == '') continue;
                ++$queries;
                $i = unserialize($i);
                if ($i['type'] == Zend_Db_Profiler::SELECT) {
                    $rows += count($db->query($i['query'], $i['params'])->fetchAll());
                    $expl = $db->query('EXPLAIN '.$i['query'], $i['params'])->fetchAll();
                    $r = 1;
                    foreach ($expl as $i) {
                        $r *= $i['rows'];
                    }
                    $explainRows += $r;
                }
            }
            $this->view->data[] = array(
                'requestNum' => $nr,
                'queries' => $queries,
                'rows' => $rows,
                'explainRows' => $explainRows
            );
        }
    }
}
