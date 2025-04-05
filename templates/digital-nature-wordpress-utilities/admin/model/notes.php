<?php
/** @var Base $model */

use DigitalNature\Utilities\Helpers\DataTables\ModelNotes\ModelNotesDataTableHelper;
use DigitalNature\Utilities\Models\Base;

$helper = new ModelNotesDataTableHelper();




?>

<div class="wrap">
    <h1>
        Model notes
        <a class="button" href="<?= $helper::get_cache_flush_url(); ?>">Refresh data</a>
    </h1>

    <h2>
        Notes for '<?= $model->get_post_type_description(); ?>' #<?= $model->id; ?>
    </h2>

    <div id="model-notes-view">
        <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
        <?= $helper::render_tab_intro(); ?>
        <?= $helper::render_tabs(); ?>

        <div class="tab-content">
            <?= $helper::render_search(); ?>
            <?= $helper::render_table(); ?>
            <?= $helper::render_pagination(); ?>
        </div>
    </div>
</div>