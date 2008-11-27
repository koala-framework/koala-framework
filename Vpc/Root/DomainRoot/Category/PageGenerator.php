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

    protected function _getInitWhere()
    {
        $components = Vps_Component_Data_Root::getInstance()->getChildComponents();
        $domain = null;
        foreach ($components as $component) {
            $generators = Vpc_Abstract::getSetting($component->componentClass, 'generators');
            foreach ($generators as $generator) {
                if ($generator['component'] == $this->_class) $domain = $component->row->id;
            }
        }
        return array('domain' => $domain);
    }

    protected function _getPageIdHome($parentData)
    {
        $domain = $parentData->parent->row->id;
        if (isset($this->_pageHome[$domain])) {
            return $this->_pageHome[$domain];
        }
        return null;
    }

    protected function _getPageIdByFilename($parentData, $filename)
    {
        if ($parentData->componentClass == $this->_class) {
            $domain = $parentData->parent->row->id;
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
