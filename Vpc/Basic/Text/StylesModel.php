<?php
class Vpc_Basic_Text_StylesModel extends Vps_Db_Table_Abstract
{
    protected $_name = 'vpc_basic_text_styles';
    protected $_rowClass = 'Vpc_Basic_Text_StylesRow';

    protected function _setupFilters()
    {
        $filter = new Vps_Filter_Row_Numberize();
        $filter->setGroupBy(array('ownStyles', 'tag'=>array('span')));
        $this->_filters = array('pos' => $filter);
    }

    public function getStyles($ownStyles = false)
    {
        $styles = array();
        $styles['block'] = array('p' => trlVps('Default'));
        $styles['inline'] = array('span' => trlVps('Normal'));

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

        $where = array();
        if ($ownStyles) {
            $where["ownStyles = ? OR ownStyles=''"] = $ownStyles;
        } else {
            $where[] = "ownStyles = ''";
        }
        $order = new Zend_Db_Expr("ownStyles!='', pos");
        $blockTags = array('p', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6');
        foreach ($this->fetchAll($where, $order) as $row) {
            $selector = $row->tag;
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
        $frontendOptions = array(
            'lifetime' => null,
            'automatic_serialization' => true
        );
        $backendOptions = array(
            'cache_dir' => 'application/cache/assets/'
        );
        return Zend_Cache::factory('Core', 'File', $frontendOptions, $backendOptions);
    }

    public static function removeCache()
    {
        return self::_getCache()->remove('RteStyles');
    }

    private static function _getCacheData()
    {
        $cache = self::_getCache();
        if (!$cacheData = $cache->load('RteStyles')) {
            $model = new Vps_Model_Db(array('table'=>new Vpc_Basic_Text_StylesModel()));
            $css = '';
            $stylesModel = new Vps_Model_Field(array(
                'parentModel' => $model,
                'fieldName' => 'styles'
            ));

            foreach ($model->fetchAll() as $row) {
                $css .= '.vpcText ' . $row->tag;
                $css .= '.style'.$row->id;
                $css .= ' { ';
                $stylesRow = $stylesModel->getRowByParentRow($row);
                foreach ($stylesRow->toArray() as $name=>$value) {
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

            $cacheData = array(
                'contents' => $css,
                'mtime' => time()
            );
            $cache->save($cacheData, 'RteStyles');
        }
        return $cacheData;
    }
    public static function getStylesContents()
    {
        $cacheData = self::_getCacheData();
        return $cacheData['contents'];
    }
    public static function getStylesUrl()
    {
        $cacheData = self::_getCacheData();
        return '/assets/AllRteStyles.css?v='.$cacheData['mtime'];
    }
}
