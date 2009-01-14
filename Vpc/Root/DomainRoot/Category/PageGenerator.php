<?php
class Vpc_Root_DomainRoot_Category_PageGenerator extends Vpc_Root_Category_PageGenerator
{
    protected function _getPageIds($parentData, $select)
    {
        $pageIds = parent::_getPageIds($parentData, $select);
        if ($parentData && $parentData->parent &&
            is_instance_of($parentData->parent->componentClass, 'Vpc_Root_DomainRoot_Domain_Component')
        ) {
            if (isset($this->_pageDomain[$parentData->parent->row->id])) {
                $pageIds = array_intersect($this->_pageDomain[$parentData->parent->row->id], $pageIds);
            } else {
                $pageIds = array();
            }
        }

        return $pageIds;
    }

    public function getDomains($parentData = null, $select = null)
    {
        /*
        $c = null;
        if ($select instanceof Vps_Component_Select) {
            if ($select->hasPart(Vps_Component_Select::WHERE_ON_SAME_PAGE)) {
                $c = $select->getPart(Vps_Component_Select::WHERE_ON_SAME_PAGE);
            }
            if (!$c && $select->hasPart(Vps_Component_Select::WHERE_SUBROOT)) {
                $c = $select->getPart(Vps_Component_Select::WHERE_SUBROOT);
            }
        }
        if (!$c && $parentData) {
            $c = $parentData;
        }

        if ($c) {
            while($c && !$c instanceof Vpc_Root_DomainRoot_Domain_Data) {
                $c = $c->parent;
            }
            if ($c) return array($c->row->id);
        }
        */

        $components = Vps_Component_Data_Root::getInstance()->getChildComponents();
        $domains = array();
        foreach ($components as $component) {
            $generators = Vpc_Abstract::getSetting($component->componentClass, 'generators');
            foreach ($generators as $generator) {
                if ($generator['component'] == $this->_class) $domains[] = $component->row->id;
            }
        }
        return $domains;
    }

    protected function _getPageIdHome($parentData)
    {
        $d = $parentData;
        while (!is_instance_of($d->componentClass, 'Vpc_Root_DomainRoot_Domain_Component')) {
            $d = $d->parent;
            if (!$d) {
                throw new Vps_Exception("Domain component not found");
            }
        }
        $domain = $d->row->id;
        if (isset($this->_pageHome[$domain])) {
            return $this->_pageHome[$domain];
        }
        return null;
    }

    protected function _getPageIdByFilename($parentData, $filename)
    {
        if (!$parentData->getPage()) {
            $d = $parentData;
            while (!is_instance_of($d->componentClass, 'Vpc_Root_DomainRoot_Domain_Component')) {
                $d = $d->parent;
                if (!$d) {
                    throw new Vps_Exception("Domain component not found");
                }
            }
            $domain = $d->row->id;
            if (isset($this->_pageDomainFilename[$domain][$filename])) {
                return $this->_pageDomainFilename[$domain][$filename];
            }
            return null;
        } else {
            return parent::_getPageIdByFilename($parentData, $filename);
        }
    }

    protected function _getParentDataByRow($row)
    {
        $parentData = Vps_Component_Data_Root::getInstance()
            ->getChildComponent('-' . $row['domain'])
            ->getChildComponent('-' . $row['category']);
        if ($parentData->componentClass != $this->_class) return null;
        return $parentData;
    }
}
