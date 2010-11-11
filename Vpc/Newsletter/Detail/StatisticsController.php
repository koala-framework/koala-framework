<?php
class Vpc_Newsletter_Detail_StatisticsController extends Vps_Controller_Action_Auto_Grid
{
    protected function _initColumns()
    {
        parent::_initColumns();

        $this->_columns->add(new Vps_Grid_Column('link', trlVps('Link'), 600));
        $this->_columns->add(new Vps_Grid_Column('count', trlVps('Count'), 50));
    }

    protected function _fetchData()
    {
        $sql = "
            SELECT r.value, r.type, count(*) c
            FROM vpc_mail_redirect_statistics s, vpc_mail_redirect r
            WHERE s.redirect_id=r.id AND mail_component_id='" . $this->_getParam('componentId') . "-mail'
            GROUP BY redirect_id
            ORDER BY c DESC
        ";
        $ret = array();
        foreach (Vps_Registry::get('db')->fetchAll($sql) as $row) {
            if ($row['type'] == 'showcomponent') {
                $c = Vps_Component_Data_Root::getInstance()->getComponentById($row['value']);
                if ($c) {
                    $link =
                        'http://' . Vps_Registry::get('config')->server->domain .
                        $c->getUrl() .
                        ' (' . substr(1, strrchr($row['value'], '-')) . ')';
                } else {
                    $link = $row['value'];
                }
            } else {
                $link = $row['value'];
            }
            $row['value'] = $link;
            $ret[] = array(
                'link' => $link,
                'count' => $row['c']
            );
        }
        return $ret;
    }
}