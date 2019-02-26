<?php
class Kwc_Basic_Text_StylesModel extends Kwf_Model_Db_Proxy
{
    protected $_table = 'kwc_basic_text_styles';
    protected $_rowClass = 'Kwc_Basic_Text_StylesRow';

    protected function _init()
    {
        parent::_init();
        $this->_siblingModels['styles'] = new Kwf_Model_Field(array('fieldName'=>'styles'));
    }

    protected function _setupFilters()
    {
        $filter = new Kwf_Filter_Row_Numberize();
        $filter->setGroupBy(array('ownStyles', 'tag'=>array('span')));
        $this->_filters = array('pos' => $filter);
    }

    //public fuer test
    public static function parseMasterStyles($masterContent)
    {
        $styles = array();
        $up = Kwf_Config::getValue('application.uniquePrefix');
        if ($up) $up .= '-';

        if (strpos($masterContent, ".{$up}webStandard")===false) return $styles;
        $up = str_replace('-', '\-', $up);
        preg_match_all("#\.{$up}webStandard\s+(span|p|h[1-6])(\.(.+))?(\s+)?{([^}]+)}\s*\/\*\!(.*)\*\/#mU", $masterContent, $m);

        foreach (array_keys($m[1]) as $i) {
            $styles[] = array(
                'id' => 'master'.$i,
                'name' => trim($m[6][$i]),
                'tagName' => $m[1][$i],
                'className' => $m[3][$i],
                'styles' => $m[5][$i],
            );
        }

        return $styles;
    }

    public static function getMasterStyles()
    {
        if (Kwf_Assets_WebpackConfig::getDevServerUrl()) {
            $filename = Kwf_Assets_WebpackConfig::getDevServerUrl() . 'assets/build/Frontend.css';
        } else {
            $filename = 'build/assets/Frontend.css';
        }
        $fileGetContentsContextOptions = array();
        if (Kwf_Config::getValue('server.https') && Kwf_Config::getValue('debug.webpackDevServer')) {
            $fileGetContentsContextOptions["ssl"] = array(
                "verify_peer" => false,
                "verify_peer_name" => false
            );
        }

        return self::parseMasterStyles(file_get_contents($filename, false, stream_context_create($fileGetContentsContextOptions)));
    }

    public function getStyles($ownStyles = false)
    {
        $styles = array();
        $styles[] = array(
            'id' => 'blockdefault',
            'name' => trlKwf('Default'),
            'tagName' => 'p',
            'className' => false,
        );
        $styles[] = array(
            'id' => 'inlinedefault',
            'name' => trlKwf('Normal'),
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
        return new Kwf_Assets_Cache();
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

    public static function getStylesContents($modelClass = 'Kwc_Basic_Text_StylesModel')
    {
        $ret = '';
        $_styles = Kwf_Model_Abstract::getInstance($modelClass)->_getStylesArray();
        foreach ($_styles as $tag => $classes) {
            foreach ($classes as $class => $style) {
                $styles = '';
                foreach ($style['styles'] as $k => $v) {
                    $styles .= "$k: $v; ";
                }
                $ret .= ".kwcText $tag.$class { {$styles}} /"."* {$style['name']} *"."/\n";
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
        return Kwf_Model_Abstract::getInstance('Kwc_Basic_Text_StylesModel')->_getStylesArray();
    }

    protected function _getStylesArray()
    {
        $cache = self::_getCache();
        $cacheId = 'RteStyles'.$this->getUniqueIdentifier();
        if (!$styles = $cache->load($cacheId)) {
            $styles = array();
            foreach ($this->getRows() as $row) {
                $styles[$row->tag]['style' . $row->id] = array(
                    'name' => $row->name,
                    'styles' => $row->getStyles()
                );
            }
            $styles = array('content' => $styles);
            $cache->save($styles, $cacheId);
        }
        return $styles['content'];
    }
}
