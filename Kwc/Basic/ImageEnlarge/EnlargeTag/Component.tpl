<?php
if ($this->imageUrl) {
    echo '<a class="' . $this->bemClass('kwcEnlargeTag') . ' ' . $this->imagePage->getLinkClass() . '" href="' . htmlspecialchars($this->imageUrl) . '"';
    if ($this->imagePage->rel) { echo ' rel="' . htmlspecialchars($this->imagePage->rel) . '"'; }
    if ($this->linkTitle) { echo ' title="' . htmlspecialchars($this->linkTitle) . '"'; }
    $attributes = $this->imagePage->getLinkDataAttributes();
    foreach ($attributes as $k=>$i) {
        echo ' data-'.htmlspecialchars($k).'="' . htmlspecialchars($i) . '"';
    }
    echo '>';
}
