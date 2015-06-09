<?php
if ($this->imageUrl) {
    echo '<a class="kwcEnlargeTag" href="' . htmlspecialchars($this->imagePage->url) . '"';
    if($this->imagePage->rel) { echo ' rel="' . htmlspecialchars($this->imagePage->rel) . '"'; }
    $attributes = $this->imagePage->getLinkDataAttributes();
    foreach ($attributes as $k=>$i) {
        echo ' data-'.htmlspecialchars($k).'="' . htmlspecialchars($i) . '"';
    }
    echo '>';
}
