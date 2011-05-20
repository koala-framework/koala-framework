<? foreach ($this->pageLinks as $l) {
    if ($this->useComponentSwitch) {
        $config = $this->componentSwitchConfig;
        $config['hideFxConfig']['slideDirection'] = $l['pageNumber'] > $l['currentPageNumber'] ? 'l' : 'r';
        $config['showFxConfig']['slideDirection'] = $l['pageNumber'] > $l['currentPageNumber'] ? 'r' : 'l';
        echo ($this->componentLinkAjax($this->data, $config, $l['linktext'], $l['class'], $l['get']));
    } else {
        echo ($this->componentLink($this->data, $l['linktext'], $l['class'], $l['get']));
    }
} ?>