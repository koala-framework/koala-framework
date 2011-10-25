<?php
class Kwc_Newsletter_Detail_StatisticsController extends Kwf_Controller_Action_Auto_Grid
{
    protected function _initColumns()
    {
        parent::_initColumns();

        $this->_columns->add(new Kwf_Grid_Column('link', trlKwf('Link'), 600));
        $this->_columns->add(new Kwf_Grid_Column('count', trlKwf('Count'), 50));
    }

    protected function _fetchData()
    {
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
                'count' => $row['c']
            );
        }
        return $ret;
    }
}