<?php
class Kwc_Newsletter_Detail_StatisticsController extends Kwf_Controller_Action_Auto_Grid
{
    protected $_buttons = array();
    protected $_position = 'pos';

    protected function _initColumns()
    {
        parent::_initColumns();

        $this->_columns->add(new Kwf_Grid_Column('pos'));
        $this->_columns->add(new Kwf_Grid_Column('link', trlKwf('Link'), 600));
        $this->_columns->add(new Kwf_Grid_Column('title', trlKwf('Title'), 200));
        $this->_columns->add(new Kwf_Grid_Column('count', trlKwf('Count'), 50))
            ->setCssClass('kwf-renderer-decimal');
        $this->_columns->add(new Kwf_Grid_Column('percent', trlKwf('[%]'), 50));
    }

    protected function _getNewsletterId()
    {
        return substr(strrchr($this->_getParam('componentId'), '_'), 1);
    }

    protected function _getNewsletterMailComponentId()
    {
        return $this->_getParam('componentId') . '_mail';
    }

    protected function _fetchData($order, $limit, $start)
    {
        $db = Kwf_Registry::get('db');
        $pos = 1;

        $ret = array();
        $newsletterId = $this->_getNewsletterId();
        $total = $db->fetchOne("SELECT count_sent FROM kwc_newsletter WHERE id=$newsletterId");

        if (!$total) { return array(); }

        $newsletterComponent = Kwf_Component_Data_Root::getInstance()->getComponentByDbId(
            $this->_getNewsletterMailComponentId(),
            array('ignoreVisible' => true)
        );
        $trackViews = Kwc_Abstract::getSetting($newsletterComponent->componentClass, 'trackViews');
        if ($trackViews) {
            $count = $newsletterComponent->getComponent()->getTotalViews();
            if ($count) {
                $ret[] = array(
                    'pos' => $pos++,
                    'link' => trlKwf('view rate') . ' (' . trlKwf('percentage of users which opened the html newsletter') . ')',
                    'title' => '',
                    'count' => $count,
                    'percent' => number_format(($count / $total)*100, 2) . '%'
                );
            }
        }

        $count = $newsletterComponent->getComponent()->getTotalClicks();
        $ret[] = array(
            'pos' => $pos++,
            'link' => trlKwf('click rate') . ' (' . trlKwf('percentage of users which clicked at least one link in newsletter') . ')',
            'title' => '',
            'count' => $count,
            'percent' => number_format(($count / $total)*100, 2) . '%'
        );
        $ret[] = array(
            'pos' => $pos++,
            'link' => ' ',
            'title' => '',
            'count' => '',
            'percent' => '',
        );
        $sql = "
            SELECT r.value, r.type, r.title, count(distinct(concat(s.recipient_id,s.recipient_model_shortcut))) c
            FROM kwc_mail_redirect_statistics s, kwc_mail_redirect r
            WHERE s.redirect_id=r.id AND mail_component_id=?
            GROUP BY redirect_id
            ORDER BY c DESC
        ";
        foreach ($db->fetchAll($sql, $newsletterComponent->componentId) as $row) {
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
                'pos' => $pos++,
                'link' => $link,
                'title' => $row['title'],
                'count' => $row['c'],
                'percent' => number_format(($row['c'] / $total)*100, 2) . '%'
            );
        }
        return $ret;
    }
}
