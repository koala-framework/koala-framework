<?php
abstract class Vpc_TreeCache_StaticPage extends Vpc_TreeCache_Static
{
    protected $_idSeparator = '_';

    protected function _formatConfig($parentData, $componentKey)
    {
        $c = $this->_classes[$componentKey];

        $data = parent::_formatConfig($parentData, $componentKey);
        $data['filename'] = isset($c['filename']) ? $c['filename'] : $componentKey; // TODO: reicht noch nicht
        $data['rel'] = isset($c['rel']) ? $c['rel'] : '';
        $data['name'] = isset($c['name']) ? $c['name'] : $componentKey;
        $data['isPage'] = true;
        return $data;
    }

    protected function _formatConstraints($parentData, $constraints)
    {
        if (isset($constraints['page'])) {
            if (!$constraints['page']) return null;
            unset($constraints['page']);
        }
        return parent::_formatConstraints($parentData, $constraints);
    }

    protected function _acceptKey($key, $constraints)
    {
        $ret = parent::_acceptKey($key, $constraints);
        if ($ret && isset($constraints['showInMenu'])) {
            $c = $this->_classes[$key];
            if ((isset($c['showInMenu']) && $c['showInMenu']) != $constraints['showInMenu']) {
                $ret = false;
            }
        }
        return $ret;
    }
}
