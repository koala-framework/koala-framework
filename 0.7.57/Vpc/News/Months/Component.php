<?php

class Vpc_News_Months_Component extends Vpc_Abstract implements Vpc_News_Interface_Component
{

    public static function getSettings()
    {
        $ret = array_merge(parent::getSettings(), array(
            'tablename'         => 'Vpc_News_Model',
            'hideInNews'        => true,
            'childComponentClasses' => array(
                'details'       => 'Vpc_News_Months_Month_Component'
            ),
            'monthLimit'        => 12,
            'sort'              => 'DESC'
        ));
        return $ret;
    }

    public function getNewsComponent()
    {
        $returnComponent = $this->getParentComponent();
        while (!$returnComponent instanceof Vpc_News_Component) {
            $returnComponent = $returnComponent->getParentComponent();
        }
        return $returnComponent;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['months'] = $this->getPageFactory()->getDateVars();

        foreach ($ret['months'] as $key => $month) {
            $detailpage = $this->getPageFactory()->getChildPageById($month['year'].'-'.$month['month']);
            $ret['months'][$key]['href'] = $this->getPageCollection()->getUrl($detailpage);
        }

        return $ret;
    }

}
