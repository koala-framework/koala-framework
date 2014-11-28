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
        if (strpos($masterContent, '.webStandard')===false) return $styles;
        preg_match_all('#^ *.webStandard *((span|p|h[1-6])\\.?([^ ]*)) *{([^}]*)} */\\* +(.*?) +\\*/#m', $masterContent, $m);
        foreach (array_keys($m[1]) as $i) {
            $tagName = $m[2][$i];
            $styles[] = array(
                'id' => 'master'.$i,
                'name' => $m[5][$i],
                'tagName' => $tagName,
                'className' => $m[3][$i],
                'styles' => Kwf_Assets_Dependency_File_Css::expandAssetVariables($m[4][$i], 'web'),
            );
        }
        return $styles;
    }

    public static function getMasterStyles()
    {
        $cacheId = 'textMasterSyles';
        $ret = Kwf_Cache_SimpleStatic::fetch($cacheId);
        if ($ret !== false) return $ret;

        $package = Kwf_Assets_Package_Default::getInstance('Frontend');
        $ret = array();
        foreach ($package->getDependency()->getFilteredUniqueDependencies('text/css') as $dep) {
            if ($dep instanceof Kwf_Assets_Dependency_File) {
                $ret = array_merge($ret, self::parseMasterStyles(file_get_contents($dep->getFileName())));
            }
        }
        Kwf_Cache_SimpleStatic::add($cacheId, $ret);
        return $ret;
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
        //copy from Kwf_Util_ClearCache_Types_Assets
        //TODO implement better in kwf 3.8
        $config = Zend_Registry::get('config');
        $langs = array();
        if ($config->webCodeLanguage) $langs[] = $config->webCodeLanguage;

        if ($config->languages) {
            foreach ($config->languages as $lang=>$name) {
                $langs[] = $lang;
            }
        }

        if (Kwf_Component_Data_Root::getComponentClass()) {
            $lngClasses = array();
            foreach(Kwc_Abstract::getComponentClasses() as $c) {
                if (Kwc_Abstract::hasSetting($c, 'baseProperties') &&
                    in_array('language', Kwc_Abstract::getSetting($c, 'baseProperties'))
                ) {
                    $lngClasses[] = $c;
                }
            }
            $lngs = Kwf_Component_Data_Root::getInstance()
                ->getComponentsBySameClass($lngClasses, array('ignoreVisible'=>true));
            foreach ($lngs as $c) {
                $langs[] = $c->getLanguage();
            }
        }
        $langs = array_unique($langs);
        //end copy

        $dep = new Kwc_Basic_Text_StylesAsset(get_class($this));
        foreach ($langs as $language) {
            $url = get_class($dep).'/'.$dep->toUrlParameter().'/'.$language.'/css';

            $cacheId = str_replace(array(':', '/', '.', ','), '_', $url);
            Kwf_Assets_Cache::getInstance()->remove($cacheId);

            $cacheId = 'as_'.str_replace(array(':', '/', ','), '_', $url).'_'.Kwf_Media_Output::ENCODING_GZIP;
            Kwf_Cache_SimpleStatic::_delete($cacheId);
            $cacheId = 'as_'.str_replace(array(':', '/', ','), '_', $url).'_'.Kwf_Media_Output::ENCODING_DEFLATE;
            Kwf_Cache_SimpleStatic::_delete($cacheId);
        }


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
                $ret .= ".kwcText $tag.$class { {$styles}} /* {$style['name']} */\n";
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
                $css = array();
                foreach ($row->getSiblingRow('styles')->toArray() as $name=>$value) {
                    if (!$value) continue;
                    if ($name == 'id') continue;
                    $name = str_replace('_', '-', $name);
                    if ($name == 'additional') {
                        foreach (explode(';', $value) as $i) {
                            if (preg_match('#^\s*([a-z-]+)\s*:\s*(.*)\s*$#', $i, $m)) {
                                $css[$m[1]] = $m[2];
                            }
                        }
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
