<?php
/** @var $helperClass string */

use DigitalNature\Utilities\Helpers\DataTableHelper;

/** @var DataTableHelper $helper */
$helper = new $helperClass();
?>

<div class="search-bar">
    <div>
        <p class="digital-nature-search search-box">
            <label class="screen-reader-text" for="dn-search-<?= $helper::get_active_search_key(); ?>"><?= $helper::get_search_label(); ?></label>
            <input type="search" id="dn-search-<?= $helper::get_active_search_key(); ?>" name="<?= $helper::get_active_search_key(); ?>" value="<?= $_GET[$helper::get_active_search_key()] ?? ''; ?>"  data-page-no-param="<?= $helper::get_active_page_no_key(); ?>" />
            <a class="button"><?= $helper::get_search_submit_label(); ?></a>
        </p>
    </div>
</div>