<!--suppress ALL -->
<meta charset="UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<meta name="viewport" content="width=device-width, initial-scale=1" />

<!-- Title -->
<title><?= $this->title; ?></title>

<!-- Favicon -->
<link rel="shortcut icon" type="image/png" href="<?= IMG_URL; ?>/favicon.png"/>

<!-- Fonts and icons -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.6.0/css/font-awesome.min.css">

<!-- My own styles -->
<link rel="stylesheet" href="<?= CSS_URL; ?>/style.css" />

<?php

if (!empty($this->css)) {
    foreach ($this->css as $css) { ?>
        <style>
            <?= $css; ?>
        </style>
    <?php }
}