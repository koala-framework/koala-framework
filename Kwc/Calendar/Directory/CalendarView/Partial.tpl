<div class="<?=$this->cssClass?>">
    <?if($this->item) {
        echo $this->componentLink($this->item, $this->number);
    } else {
    echo $this->number;
}?>
</div>
<?if ($this->clear) {
    echo '<div class="clear"></div>';
}?>
