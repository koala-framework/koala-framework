<? foreach ($this->pageLinks as $l) {
    $config = $this->componentAjaxConfig;
    $config['hideFxConfig']['slideDirection'] = $l['pageNumber'] > $l['currentPageNumber'] ? 'l' : 'r';
    $config['showFxConfig']['slideDirection'] = $l['pageNumber'] > $l['currentPageNumber'] ? 'r' : 'l';
    echo ($this->componentLinkAjax($this->data, $config, $l['linktext'], $l['class'], $l['get']));
} ?>