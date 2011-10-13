<? foreach ($this->pageLinks as $l) {
    echo ($this->componentLink($this->data, $l['linktext'], array('get'=>$l['get'], 'cssClass'=>$l['class'])));
} ?>