<?php

namespace DigitalNature\WordPressUtilities\Helpers;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

abstract class DataTableTabHelper
{
    abstract public function is_searchable(): bool;

    abstract public function is_paginated(): bool;

    abstract public function get_label(): string;

    abstract public function get_slug(): string;

    abstract public function get_data(): array;

    abstract public function get_result_count(): int;

    /**
     * @return string
     */
    public function get_intro_text(): string
    {
        return '';
    }
}