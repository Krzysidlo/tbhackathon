<!--suppress ALL -->

<!-- jQuery 3.3.1 -->
<script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>

<!-- My own js -->
<script src="<?= JS_URL; ?>/main.js"></script>

<?php

if (!empty($this->js)) {
    foreach ($this->js as $js) { ?>
        <script>
            <?= $js; ?>
        </script>
    <?php }
}