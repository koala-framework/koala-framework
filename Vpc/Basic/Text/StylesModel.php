<?php
class Vpc_Basic_Text_StylesModel extends Vps_Db_Table_Abstract
{
    protected $_name = 'vpc_basic_text_styles';
    protected $_rowClass = 'Vpc_Basic_Text_StylesRow';

    public function getStyles()
    {
        $styles = array();
        $styles['block'] = array('p' => trlVps('Default'));
        $styles['inline'] = array('span' => trlVps('Normal'));
        foreach ($this->fetchAll(null, 'pos') as $row) {
            $selector = $row->tag;
            $selector .= '.style'.$row->id;
            if ($selector) {
                if ($row->tag == 'span') {
                    $styles['inline'][$selector] = $row->name;
                } else {
                    $styles['block'][$selector] = $row->name;
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
                $css .= '.content ' . $row->tag;
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
