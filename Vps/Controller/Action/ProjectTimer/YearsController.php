<?php
class Vps_Controller_Action_ProjectTimer_YearsController extends Vps_Controller_Action_Auto_Grid
{
    protected $_buttons = array();
    protected $_permissions = array();
    protected $_paging = 25;

    protected function _initColumns()
    {
        parent::_initColumns();

        $this->_columns->add(new Vps_Grid_Column('year', trlVps('Year'), 200));
        $this->_columns->add(new Vps_Grid_Column('spent_time', trlVps('Spent time'), 100))
            ->setRenderer('secondsToTimeProjectTimer');
        $this->_columns->add(new Vps_Grid_Column('included_time', trlVps('Included time'), 100))
            ->setRenderer('secondsToTime');
    }

    protected function _fetchData($order, $limit, $start)
    {
        $ret = array();

        $projectTimer = Vps_Model_Abstract::getInstance('Vps_Util_Model_ProjectTimer');
        $projects = Vps_Model_Abstract::getInstance('Vps_Util_Model_Projects');
        $projectIds = $projects->getApplicationProjectIds();
        if (!$projectIds) return $ret;

        $projectRow = $projects->getRow($projects->select()
            ->whereEquals('id', $projectIds)
            ->where(new Vps_Model_Select_Expr_Not(
                new Vps_Model_Select_Expr_Equals('invoice_first', 0)
            ))
            ->where(new Vps_Model_Select_Expr_Not(
                new Vps_Model_Select_Expr_Equals('invoice_month', 0)
            ))
        );
        if (!$projectRow) return $ret;

        $includedTime = $projectRow->included_time;

        $years = array();
        $j = 1;
        // alle jahre, ohne dem aktuellen
        for ($i = $projectRow->invoice_first; $i < date('Y'); $i++) {
            $year = array(
                'year' => $j++,
                'from' => $i.'-'.str_pad($projectRow->invoice_month, 2, '0', STR_PAD_LEFT).'-01 00:00:00'
            );
            $year['to'] = ($i+1).date('-m-d H:i:s', strtotime($year['from']) - 1);
            $years[] = $year;
        }
        // wenn invoice month überschritten wurde, eintrag für nächstes jahr auch hinzufügen
        if ($projectRow->invoice_month <= date('m')) {
            $year = array(
                'year' => $j++,
                'from' => date('Y').'-'.str_pad($projectRow->invoice_month, 2, '0', STR_PAD_LEFT).'-01 00:00:00'
            );
            $year['to'] = (date('Y')+1).date('-m-d H:i:s', strtotime($year['from']) - 1);
            $years[] = $year;
        }

        $id = 1;
        foreach ($years as $year) {
            $timer = $projectTimer->getRows($projectTimer->select()
                ->whereEquals('project_id', $projectIds)
                ->where(new Vps_Model_Select_Expr_Or(array(
                    new Vps_Model_Select_Expr_Equals('start', $year['from']),
                    new Vps_Model_Select_Expr_HigherDate('start', $year['from'])
                )))
                ->where(new Vps_Model_Select_Expr_Or(array(
                    new Vps_Model_Select_Expr_Equals('start', $year['to']),
                    new Vps_Model_Select_Expr_SmallerDate('start', $year['to'])
                )))
            );
            // timer zusammenzählen - ist schwierig mit nem child von projects
            // zu machen, weils ja mehrere projectIds geben könnt
            $spentTime = 0;
            foreach ($timer as $t) {
                $spentTime += $t->time;
            }

            $ret[] = array(
                'id' => $id++,
                'year' => '<span class="year"><strong>'.trlVps('Year {0}', array($year['year'])).'</strong> ('
                    .date(trlVps('Y-m-d'), strtotime($year['from'])).' - '
                    .date(trlVps('Y-m-d'), strtotime($year['to'])).')</span>',
                'spent_time' => $spentTime,
                'included_time' => $includedTime
            );
        }
        $ret = array_reverse($ret);

        return $ret;
    }
}
