<?php

namespace DigitalNature\WordPressUtilities\Repositories;

use DigitalNature\WordPressUtilities\Helpers\LogHelper;
use DigitalNature\WordPressUtilities\Models\Model;
use DigitalNature\WordPressUtilities\Models\ModelNote;
use DigitalNature\WordPressUtilities\Query\ModelNote\ModelNoteFromModelQuery;
use WP_User;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

class ModelNotesRepository
{
    /**
     * @param Model $model
     * @param string $content
     * @param WP_User|null $author
     * @param int|null $created_on
     * @return ModelNote
     */
    public static function create(Model $model, string $content, WP_User $author = null, int $created_on = null): ModelNote
    {
        $note = ModelNote::create();
        $note->original_user_id = apply_filters('digital_nature_wordpress_utilities_model_note_get_original_user_id', null);
        $note->author_user_id = $author ? $author->ID : apply_filters('digital_nature_wordpress_utilities_model_note_default_user_id', null);
        $note->created_on = $created_on ?? time();
        $note->model_id = $model->id;
        $note->note = $content;
        $note->save();

        // allow other plugins to hook into this
        do_action('digital_nature_wordpress_utilities_model_note_created', $model::get_post_type(), $note);

        return $note;
    }

    /**
     * Deletes a model and clears relevant caches
     *
     * @param ModelNote $note
     * @return void
     */
    public static function delete(ModelNote $note): void
    {
        // delete this record
        $note->delete();

        // flush caches
        self::flush_caches($note);
    }



    /** MODEL CACHE FLUSH */

    /**
     * @param ModelNote $note
     * @return void
     */
    public static function flush_caches(ModelNote $note): void
    {
        LogHelper::write("DIGITAL_NATURE_MODEL_NOTE_REPOSITORY: Flushing caches");
        $model = $note->get_model();

        if ($model) {
            $query = new ModelNoteFromModelQuery();
            $query->set_model($model)->flush();
        }
    }

    /**
     * @param Model $model
     * @param bool $flushCache
     * @return ModelNote[]
     */
    public static function from_model(Model $model, bool $flushCache = false): array
    {
        $query = new ModelNoteFromModelQuery();
        $query->set_model($model);

        if ($flushCache) {
            $query->flush();
        }

        return $query->run();
    }
}