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
                    'count' => $count,
                    'percent' => number_format(($count / $total)*100, 2) . '%'
                );
            }
        }

        $count = $newsletterComponent->getComponent()->getTotalClicks();
        $ret[] = array(
            'pos' => $pos++,
            'link' => trlKwf('click rate') . ' (' . trlKwf('percentage of users which clicked at least one link in newsletter') . ')',
            'count' => $count,
            'percent' => number_format(($count / $total)*100, 2) . '%'
        );
        foreach (Kwf_Component_Data_Root::getInstance()->getPlugins('Kwc_Newsletter_PluginInterface') as $plugin) {
            $bounces = $plugin->getNewsletterStatisticRows($total, $pos);
            foreach ($bounces as $bounceValue) {
                $ret[] = $bounceValue;
            }
        }
        $ret[] = array(
            'pos' => $pos++,
            'link' => ' ',
            'count' => '',
            'percent' => '',
        );
        $sql = "
            SELECT r.value, count(distinct(concat(s.recipient_id,s.recipient_model_shortcut))) c
            FROM kwc_mail_redirect_statistics s, kwc_mail_redirect r
            WHERE s.redirect_id=r.id AND mail_component_id=?
            GROUP BY redirect_id
            ORDER BY c DESC
        ";
        foreach ($db->fetchAll($sql, $newsletterComponent->componentId) as $row) {
            $link = $row['value'];
            $row['value'] = $link;
            $ret[] = array(
                'pos' => $pos++,
                'link' => $link,
                'count' => $row['c'],
                'percent' => number_format(($row['c'] / $total)*100, 2) . '%'
            );
        }
        return $ret;
    }
}
