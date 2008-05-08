<ul class="newsCatagory">
    <?php foreach ($this->categories as $cat) { ?>
        <li>
            <a href="<?= $cat['href'] ?>"><?= $cat['value'] ?></a>
        </li>
    <?php } ?>
</ul>