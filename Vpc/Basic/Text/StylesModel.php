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

    //public fuer test
    public static function parseMasterStyles($masterContent)
    {
        $styles = array();
        preg_match_all('#^ *.webStandard *((span|p|h[1-6])\\.?([^ ]*)) *{[^}]*} */\\* +(.*?) +\\*/#m', $masterContent, $m);
        foreach (array_keys($m[1]) as $i) {
            $tagName = $m[2][$i];
            $styles[] = array(
                'id' => 'master'.$i,
                'name' => $m[4][$i],
                'tagName' => $tagName,
                'className' => $m[3][$i],
            );
        }
        return $styles;
    }

    public static function getMasterStyles()
    {
        if (file_exists('css/master.css')) {
            return self::parseMasterStyles(file_get_contents('css/master.css'));
        }
        return array();
    }

    public function getStyles($ownStyles = false)
    {
        $styles = array();
        $styles[] = array(
            'id' => 'blockdefault',
            'name' => trlVps('Default'),
            'tagName' => 'p',
            'className' => false,
        );
        $styles[] = array(
            'id' => 'inlinedefault',
            'name' => trlVps('Normal'),
            'tagName' => 'span',
            'className' => false,
        );

        $masterStyles = $this->getMasterStyles();
        $styles = array_merge($styles, $masterStyles);

        $select = $this->select();
        if ($ownStyles) {
            $select->whereEquals('ownStyles', $ownStyles);
        } else {
            $select->whereEquals('ownStyles', '');
        }
        $select->order(new Zend_Db_Expr("ownStyles!=''"));
        $select->order('pos');
        foreach ($this->getRows($select) as $row) {
            $selector = $row->tag.'.style'.$row->id;
            $name = $row->name;
            if ($row->ownStyles) $name = '* '.$name;
            $styles[] = array(
                'id' => 'style'.$row->id,
                'name' => $name,
                'tagName' => $row->tag,
                'className' => 'style'.$row->id,
            );
        }
        foreach ($styles as $k=>$i) {
            if ($i['tagName'] == 'span') {
                $styles[$k]['type'] = 'inline';
            } else {
                $styles[$k]['type'] = 'block';
            }
        }
        return $styles;
    }

    private static function _getCache()
    {
        return new Vps_Assets_Cache();
    }

    public function removeCache()
    {
        return self::_getCache()->remove('RteStyles'.$this->getUniqueIdentifier());
    }

    public function getMTime()
    {
        $mtime = self::_getCache()->test('RteStyles'.$this->getUniqueIdentifier());
        if (!$mtime) $mtime = time();
        return $mtime;
    }

    public static function getStylesContents($modelClass = 'Vpc_Basic_Text_StylesModel')
    {
        $ret = '';
        $_styles = Vps_Model_Abstract::getInstance($modelClass)->_getStylesArray();
        foreach ($_styles as $tag => $classes) {
            foreach ($classes as $class => $style) {
                $styles = '';
                foreach ($style['styles'] as $k => $v) {
                    $styles .= "$k: $v; ";
                }
                $ret .= ".vpcText $tag.$class { {$styles}} /* {$style['name']} */\n";
            }
        }
        return $ret;
    }

    public function getStylesContents2()
    {
        return $this->getStylesContents(get_class($this));
    }

    public static function getStylesArray()
    {
        return Vps_Model_Abstract::getInstance('Vpc_Basic_Text_StylesModel')->_getStylesArray();
    }

    protected function _getStylesArray()
    {
        $cache = self::_getCache();
        $cacheId = 'RteStyles'.$model->getUniqueIdentifier();
        if (!$styles = $cache->load($cacheId)) {
            $styles = array();
            foreach ($this->getRows() as $row) {
                $css = array();
                foreach ($row->getSiblingRow('styles')->toArray() as $name=>$value) {
                    if (!$value) continue;
                    if ($name == 'id') continue;
                    $name = str_replace('_', '-', $name);
                    if ($name == 'additional') {
                        $value = $value;
                        continue;
                    } else if ($name == 'margin-top' || $name == 'margin-bottom'
                            || $name=='font-size') {
                        $value .= 'px';
                    } else if ($name == 'color') {
                        $value = '#'.$value;
                    }
                    $css[$name] = $value;
                }
                $styles[$row->tag]['style' . $row->id] = array(
                    'name' => $row->name,
                    'styles' => $css
                );
            }
            $styles = array('content' => $styles);
            $cache->save($styles, $cacheId);
        }
        return $styles['content'];
    }
}
