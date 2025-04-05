<?php
/** @var $helperClass string */

use DigitalNature\Utilities\Helpers\DataTableHelper;
use DigitalNature\Utilities\Helpers\DataTableTabHelper;

/** @var DataTableHelper $helper */
$helper = new $helperClass();
?>

<nav class="nav-tab-wrapper">
    <?php foreach($helper::get_tabs() as $slug => $tab): ?>
        <?php /** @var DataTableTabHelper $tab */ ?>
        <a href="?<?= $helper::get_base_url_with_params(); ?>&<?= $helper::get_active_tab_key(); ?>=<?= $tab->get_slug(); ?>" class="nav-tab <?php if ($tab->get_slug() === $helper::get_active_tab()) : ?>nav-tab-active<?php endif; ?>"><?= $tab->get_label(); ?></a>
    <?php endforeach; ?>
</nav>