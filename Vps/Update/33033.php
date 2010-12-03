<?php
class Vps_Update_33033 extends Vps_Update
{
    public function getTags()
    {
        return array('vpc');
    }

    public function update()
    {
        $info = $this->_updateDir('Vpc');
        $ret = "\n";
        if ($info['templates']) {
            $count = count($info['templates']);
            $ret .= "   Folgende $count Templates wurden an neues ifHasContent angepasst, bitte checken ob es passt und commiten:\n";
            foreach ($info['templates'] as $file) {
                $ret .= "        $file\n";
            }
        }
        if ($info['getCacheVars']) {
            $count = count($info['getCacheVars']);
            $ret .= "   Folgende $count Komponenten haben getCacheVars ueberschrieben und muessen an getCacheMeta angepasst werden:\n";
            foreach ($info['getCacheVars'] as $file) {
                $ret .= "        $file\n";
            }
        }
        if ($info['getStaticCacheVars']) {
            $count = count($info['getStaticCacheVars']);
            $ret .= "   Folgende $count Komponenten haben getStaticCacheVars ueberschrieben und muessen an getStaticCacheMeta angepasst werden:\n";
            foreach ($info['getStaticCacheVars'] as $file) {
                $ret .= "        $file\n";
            }
        }
        return $ret;
        /* zum Testen
        $string = '
<?=$this->ifHasContent($this->item);?>
<?=$this->ifHasContent();?>

<?php echo $this->ifHasContent ( $this->item ) ; ?>
<?php echo $this->ifHasContent ( ) ; ?>

<?=$this->ifHasNoContent($this->item) ;?>
<?=$this->ifHasNoContent() ;?>
        ';
        d($this->_replaceHasContent($string));
        */
    }

    private function _updateDir($dir) {
        $ret = array(
            'templates' => array(),
            'getCacheVars' => array(),
            'getStaticCacheVars' => array()
        );
        $count = 0;
        foreach (new DirectoryIterator($dir) as $i) {
            if (substr($i, 0, 1) == '.') continue;
            $path = $dir . '/' . $i;
            if (substr($path, -4) == '.tpl') {
                $original = file_get_contents($path);
                $new = $this->_replaceHasContent($original);
                file_put_contents($path, $new);
                if ($original != $new) $ret['templates'][] = $path;
            }

            if ($i == 'Component.php') {
                $string = file_get_contents($path);
                if (strpos($string, 'getCacheVars') !== false) {
                    $ret['getCacheVars'][] = $path;
                }
                if (strpos($string, 'getStaticCacheVars') !== false) {
                    $ret['getStaticCacheVars'][] = $path;
                }
            }

            if (!is_dir($path)) continue;
            $ret2 = $this->_updateDir($path);
            foreach ($ret as $key => $r) {
                $ret[$key] = array_merge($r, $ret2[$key]);
            }
        }
        return $ret;
    }

    private function _replaceHasContent($string)
    {
        $pattern = '/(=|echo)\s*\$this->ifHasContent\s*\(\s*(\S+)\s*\)\s*;?/i';
        $replacement = 'if (\$this->hasContent($2)) {';
        $string = preg_replace($pattern, $replacement, $string);
        $pattern = '/(=|echo)\s*\$this->ifHasNoContent\s*\(\s*(\S+)\s*\)\s*;?/i';
        $replacement = 'if (!\$this->hasContent($2)) {';
        $string = preg_replace($pattern, $replacement, $string);
        $pattern = '/(=|echo)\s*\$this->ifHas(No)?Content\s*\(\s*\)\s*;?/i';
        $replacement = '}';
        $string = preg_replace($pattern, $replacement, $string);

        return $string;
    }
}
