<?php
class Kwc_Newsletter_Detail_StatisticsController extends Kwf_Controller_Action_Auto_Grid
{
    protected function _initColumns()
    {
        parent::_initColumns();

        $this->_columns->add(new Kwf_Grid_Column('link', trlKwf('Link'), 600));
        $this->_columns->add(new Kwf_Grid_Column('count', trlKwf('Count'), 50))
            ->setCssClass('kwf-renderer-decimal');
        $this->_columns->add(new Kwf_Grid_Column('percent', trlKwf('[%]'), 50));
    }

    protected function _fetchData()
    {
        $db = Kwf_Registry::get('db');
        $pos = 1;

        $ret = array();
        $newsletterId = substr(strrchr($this->_getParam('componentId'), '_'), 1);
        $total = $db->fetchOne("SELECT count_sent FROM kwc_newsletter WHERE id=$newsletterId");
        if ($total) {
            $sql = "
                SELECT count(distinct (concat (recipient_id,recipient_model_shortcut)))
                FROM kwc_mail_redirect_statistics s, kwc_mail_redirect r
                WHERE s.redirect_id=r.id AND mail_component_id='" . $this->_getParam('componentId') . "-mail'";
            $count = $db->fetchOne($sql);
            $ret[] = array(
                'pos' => $pos++,
                'link' => '<b>' . trlKwf('click rate') . '</b> (' . trlKwf('percentage of users which clicked at least one link in newsletter') . ')',
                'count' => $count,
                'percent' => number_format(($count / $total)*100, 2) . '%'
            );
            $ret[] = array(
                'pos' => $pos++,
                'link' => ' ',
                'count' => '',
                'percent' => '',
            );
        }
        $sql = "
            SELECT r.value, r.type, count(*) c
            FROM kwc_mail_redirect_statistics s, kwc_mail_redirect r
            WHERE s.redirect_id=r.id AND mail_component_id='" . $this->_getParam('componentId') . "-mail'
            GROUP BY redirect_id
            ORDER BY c DESC
        ";
        $ret = array();
        foreach (Kwf_Registry::get('db')->fetchAll($sql) as $row) {
            if ($row['type'] == 'showcomponent') {
                $c = Kwf_Component_Data_Root::getInstance()->getComponentById($row['value']);
                if ($c) {
                    $link =
                        'http://' . Kwf_Registry::get('config')->server->domain .
                        $c->getUrl() .
                        ' (' . substr(strrchr($row['value'], '-'), 1) . ')';
                } else {
                    $link = $row['value'];
                }
            } else {
                $link = $row['value'];
            }
            $row['value'] = $link;
            $ret[] = array(
                'link' => $link,
                'count' => $row['c'],
                'percent' => number_format(($row['c'] / $total)*100, 2) . '%'
            );
        }
        return $ret;
    }
}