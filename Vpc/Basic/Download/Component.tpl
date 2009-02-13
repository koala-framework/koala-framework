<div class="<?=$this->cssClass?>">
    <?php
    echo $this->component($this->downloadTag);
    if ($this->icon) { echo "<img src=\"{$this->icon}\" />"; }
    echo ' ';
    echo $this->mailEncodeText($this->infotext);
    echo $this->ifHasContent($this->downloadTag);
    echo '</a>';
    if ($this->filesize) {
        echo ' <span>(' . $this->fileSize($this->filesize) . ')</span>';
    }
    echo $this->ifHasContent();
    ?>
</div>