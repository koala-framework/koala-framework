<div class="<?=$this->cssClass?>">
    <?php
    if ($this->row->vps_upload_id) {
        echo $this->image($this->row, null, 'default', '', $this->imgCssClass);
    } else {
        echo $this->image($this->componentFile($this->data, 'ghost.jpg'));
    }
    ?>
</div>