<?php

namespace DigitalNature\WordPressUtilities\Stores;

use DigitalNature\WordPressUtilities\Models\Model;
use DigitalNature\WordPressUtilities\Patterns\Singleton;
use Exception;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

class ModelStore extends Singleton
{
    private array $models = [];

    /**
     * @param int $modelId
     * @param string $modelClassName
     * @return Model|null
     * @throws Exception
     */
    public static function &load(int $modelId, string $modelClassName): ?Model
    {
        if (!is_a($modelClassName, Model::class, true)) {
            throw new Exception("$modelClassName is not an instance of Model");
        }

        // if we've already loaded this model then return it from cache
        $modelStore = self::getInstance();
        $existingRecord = $modelStore->retrieve($modelId);

        if ($existingRecord) {
            // found the record, return it
            return $existingRecord;
        }

        $thisModel = get_post($modelId);

        if (!isset($thisModel->ID)) {
            throw new Exception("The retrieved post has no ID");
        }

        if ($thisModel->post_type !== $modelClassName::get_post_type()) {
            throw new Exception("The retrieved post does not have the correct class");
        }

        $model = new $modelClassName();
        $model->set_attributes($thisModel);

        // store this so we don't need to load it again later
	    $modelStore->store($modelId, $model);

        return $model;
    }

    /**
     * @param int $modelId
     * @return Model|null
     */
    private function retrieve(int $modelId): ?Model
    {
        if (!array_key_exists($modelId, $this->models)) {
            return null;
        }

        return $this->models[$modelId];
    }

    /**
     * @param int $modelId
     * @param Model $model
     * @return void
     */
    private function store(int $modelId, Model $model): void
    {
        $this->models[$modelId] = $model;
    }
}