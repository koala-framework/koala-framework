<?php
class Vpc_Basic_Text_StylesModel extends Vps_Model_Db_Proxy
{
    protected $_table = 'vpc_basic_text_styles';
    protected $_rowClass = 'Vpc_Basic_Text_StylesRow';

    protected function _init()
    {
        parent::_init();
        $this->_siblingModels['styles'] = new Vps_Model_Field(array('fieldName'=>'styles'));
    }

    protected function _setupFilters()
    {
        $filter = new Vps_Filter_Row_Numberize();
        $filter->setGroupBy(array('ownStyles', 'tag'=>array('span')));
        $this->_filters = array('pos' => $filter);
    }

    public static function getMasterStyles()
    {
        $styles = array('inline' => array(), 'block' => array());
        if (file_exists('css/master.css')) {
            $masterContent = file_get_contents('css/master.css');
            preg_match_all('#^ *.webStandard *((span|p|h[1-6])\\.?[^ ]*) *{[^}]*} */\\* +(.*?) +\\*/#m', $masterContent, $m);
            foreach (array_keys($m[1]) as $i) {
                $selector = $m[1][$i];
                $name = $m[3][$i];
                if (substr($selector, 0, 4)=='span') {
                    $styles['inline'][$selector] = $name;
                } else {
                    $styles['block'][$selector] = $name;
                }
            }
        }
        return $styles;
    }

    //um es im test einfacher überschreiben zu können
    protected function _getMasterStyles()
    {
        return self::getMasterStyles();
    }

    public function getStyles($ownStyles = false)
    {
        $styles = array();
        $styles['block'] = array('p' => trlVps('Default'));
        $styles['inline'] = array('span' => trlVps('Normal'));

        $masterStyles = $this->_getMasterStyles();
        $styles['block'] = array_merge($styles['block'], $masterStyles['block']);
        $styles['inline'] = array_merge($styles['inline'], $masterStyles['inline']);

        $select = $this->select();
        if ($ownStyles) {
            $select->whereEquals('ownStyles', $ownStyles);
        } else {
            $select->whereEquals('ownStyles', '');
        }
        $select->order(new Zend_Db_Expr("ownStyles!=''"));
        $select->order('pos');
        $blockTags = array('p', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6');
        foreach ($this->getRows($select) as $row) {
            $selector = $row->tag.'.style'.$row->id;
            if ($selector) {
                $name = $row->name;
                if ($row->ownStyles) $name = '* '.$name;
                if ($row->tag == 'span') {
                    $styles['inline'][$selector] = $name;
                } else if (in_array($row->tag, $blockTags)) {
                    $styles['block'][$selector] = $name;
                }
            }
        }
        return $styles;
    }

    private static function _getCache()
    {
        return new Vps_Assets_Cache();
    }

    public static function removeCache()
    {
        return self::_getCache()->remove('RteStyles');
    }

    public static function getMTime()
    {
        $mtime = self::_getCache()->test('RteStyles');
        if (!$mtime) $mtime = time();
        return $mtime;
    }

    public function getStylesContents()
    {
        $cacheId = 'RteStyles'.$this->getUniqueIdentifier();
        $cache = self::_getCache();
        if (!$css = $cache->load($cacheId)) {
            $css = '';
            foreach ($this->getRows() as $row) {
                $css .= '.vpcText ' . $row->tag;
                $css .= '.style'.$row->id;
                $css .= ' { ';
                foreach ($row->getSiblingRow('styles')->toArray() as $name=>$value) {
                    if (!$value) continue;
                    if ($name == 'id') continue;
                    $name = str_replace('_', '-', $name);
                    if ($name == 'additional') {
                        $css .= $value;
                        continue;
                    } else if ($name == 'margin-top' || $name == 'margin-bottom'
                            || $name=='font-size') {
                        $value .= 'px';
                    } else if ($name == 'color') {
                        $value = '#'.$value;
                    }
                    $css .= $name.': '.$value.'; ';
                }
                $css .= "} /* $row->name */\n";
            }
            $css = array('contents' => $css);
            $cache->save($css, $cacheId);
        }
        return $css['contents'];
    }
}
