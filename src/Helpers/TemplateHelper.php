<?php

namespace DigitalNature\WordPressUtilities\Helpers;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

class TemplateHelper
{
    /**
     * @param string|null $name
     * @param array $args
     * @param string $templateDirPath
     * @param bool $returnAsString
     * @return string|null
     */
    public static function render(string $name, array $args = [], string $templateDirPath = '', bool $returnAsString = false): ?string
    {
        if (!file_exists($templateDirPath . $name)) {
            LogHelper::write("TEMPLATE_HELPER: File could not be found: {$templateDirPath}{$name}");
            return null;
        }

        if ($returnAsString) {
            ob_start();
        }

        if (!empty($args)) {
            extract($args);
        }

        // include the file
        include $templateDirPath . $name;

        if ($returnAsString) {
            $message = ob_get_contents();

            ob_end_clean();

            return $message;
        }

        return null;
    }
}