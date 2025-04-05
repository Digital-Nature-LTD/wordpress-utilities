<?php
/** @var DataTableHelper|null $helper */
/** @var string|null $title */

use DigitalNature\Utilities\Helpers\DataTableHelper;
?>

<div class="wrap digital-nature-utilities-data-table">

    <?php if (!$helper): ?>
        <?php if (isset($title)): ?>
            <h1><?= $title; ?></h1>
        <?php endif; ?>
        <h2>No data table helper has been configured for this page.</h2>
    <?php else: ?>
        <h1>
            <?= $title; ?>
            <a class="button" href="<?= $helper::get_cache_flush_url(); ?>">Refresh data</a>
        </h1>

        <div id="klira-nurse-view">
            <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
            <?= $helper::render_tabs(); ?>
            <?= $helper::render_tab_intro(); ?>

            <?= $helper::render_search(); ?>
            <?= $helper::render_table(); ?>
            <?= $helper::render_pagination(); ?>
        </div>
    <?php endif; ?>
</div>