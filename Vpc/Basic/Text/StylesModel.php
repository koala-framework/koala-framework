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
            $where["ownStyles = ?"] = $ownStyles;
        } else {
            $where[] = "ownStyles = ''";
        }
        $order = new Zend_Db_Expr("ownStyles!='', pos");
        $blockTags = array('p', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6');
        foreach ($this->fetchAll($where, $order) as $row) {
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

    public static function getStylesContents()
    {
        $cache = self::_getCache();
        if (!$ret = $cache->load('RteStyles')) {
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
            $ret = array(
                'contents' => $css,
                'mtime' => time(),
                'mimeType' => 'text/css',
                'etag' => md5($css)
            );
            $cache->save($ret, 'RteStyles');
        }
        return $ret;
    }
    public static function getStylesUrl()
    {
        $mtime = self::_getCache()->test('RteStyles');
        if (!$mtime) $mtime = time();
        return '/assets/AllRteStyles.css?v='.$mtime;
    }
}
