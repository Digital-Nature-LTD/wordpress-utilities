<?php
/** @var $helperClass string */

use DigitalNature\Utilities\Helpers\DataTableHelper;

/** @var DataTableHelper $helper */
$helper = new $helperClass();
$data = $helper::get_active_tab_data();
?>

<?php if (empty($data)) : ?>
    <div>
        <h2>No results</h2>
        <p class="text-center">No matching items found.</p>
    </div>
<?php else : ?>
    <div>
        <?php $tableHeaders = array_keys($data[0]); ?>
        <table class="wp-list-table widefat fixed striped table-view-list posts">
            <thead>
            <tr>
                <?php foreach ($tableHeaders as $tableHeader) : ?>
                    <?php if (is_array($data[0][$tableHeader])) : ?>
                        <?php if (isset($data[0][$tableHeader]['colspan'])) : ?>
                            <th colspan="<?= $data[0][$tableHeader]['colspan']; ?>"><?= $tableHeader; ?></th>
                        <?php else : ?>
                            <th><?= $tableHeader; ?></th>
                        <?php endif; ?>
                    <?php else : ?>
                        <th><?= $tableHeader; ?></th>
                    <?php endif; ?>
                <?php endforeach; ?>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($data as $subscriptionData) : ?>
                <tr>
                    <?php foreach ($subscriptionData as $rowData) : ?>
                        <?php if (is_array($rowData)) : ?>
                            <?php foreach ($rowData['data'] as $subRow) : ?>
                                <td class="<?= $rowData['class']; ?>"><?= $subRow; ?></td>
                            <?php endforeach; ?>
                        <?php else : ?>
                            <td><?= $rowData; ?></td>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>