<? foreach ($this->pageLinks as $l) {
    echo ($this->componentLink($this->data, $l['linktext'], $l['class'], $l['get']));
} ?>