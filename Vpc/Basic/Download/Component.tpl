<div class="<?=$this->cssClass?>">
    <?php
    echo $this->component($this->downloadTag);
    if ($this->icon) { echo "<img src=\"{$this->icon}\" />"; }
    echo ' ';
    echo $this->mailEncodeText($this->infotext);
    echo '</a>';
    if ($this->filesize) {
        echo ' <span>(' . $this->fileSize($this->filesize) . ')</span>';
    }
    ?>
</div>