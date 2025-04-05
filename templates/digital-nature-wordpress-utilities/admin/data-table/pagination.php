<?php
/** @var $helperClass string */

use DigitalNature\Utilities\Helpers\DataTableHelper;
use DigitalNature\Utilities\Helpers\DataTableTabHelper;

/** @var DataTableHelper $helper */
/** @var DataTableTabHelper $tab */
$helper = new $helperClass();
$tab = $helper::get_active_tab_object();

$urlParams = $_GET;
if (isset($urlParams[$helper::get_active_page_no_key()])) {
  unset($urlParams[$helper::get_active_page_no_key()]);
}

$urlParamStrings = [];

foreach($urlParams as $param => $value) {
  $urlParamStrings[] = "$param=$value&";
}

$paginationUrlPrefix = implode('&',$urlParamStrings);

?>

<?php if (!$helper::is_searching_by_id()): ?>
    <div class="digital-nature-page-controls">
        <div class="digital-nature-page-size">
            <select name="digital-nature-results-per-page" data-page-size-param="<?= $helper::get_active_page_size_key(); ?>">
                <option <?php if ($helper::get_active_page_size() == 10) echo 'selected'; ?>>10</option>
                <option <?php if ($helper::get_active_page_size() == 20) echo 'selected'; ?>>20</option>
                <option <?php if ($helper::get_active_page_size() == 50) echo 'selected'; ?>>50</option>
                <option <?php if ($helper::get_active_page_size() == 100) echo 'selected'; ?>>100</option>
            </select>
        </div>

        <div class="digital-nature-results-total"><?= $tab->get_result_count() . ' results'; ?></div>

        <div class="digital-nature-pagination" data-page-no-param="<?= $helper::get_active_page_no_key(); ?>">
            <?php foreach($helper::get_pagination_links($tab) as $page): ?>
                <?php if (isset($page['current'])): ?>
                    <span class="current"><?= $page['content'] ?? $page['no']; ?></span>
                <?php else: ?>
                    <a href="?<?= $paginationUrlPrefix; ?><?= $helper::get_active_page_no_key(); ?>=<?= $page['no']; ?>" data-page-no="<?= $page['no']; ?>" class="<?= $page['class'] ?? ''; ?>">
                        <?= $page['content'] ?? $page['no']; ?>
                    </a>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    </div>
<?php endif; ?>