<div class="<?=$this->cssClass?>">
    <?php
    if ($this->icon) { echo "<img src=\"{$this->icon}\" />"; }
    echo ' ';
    echo $this->component($this->downloadTag);
    echo $this->mailEncodeText($this->infotext);
    if ($this->hasContent($this->downloadTag)) {
        echo '</a>';
        if ($this->filesize) {
            echo ' <span>(' . $this->fileSize($this->filesize) . ')</span>';
        }
    }
    ?>
</div>