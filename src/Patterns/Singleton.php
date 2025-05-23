<?php

namespace DigitalNature\WordPressUtilities\Patterns;

// Exit if accessed directly.
if (!defined('ABSPATH')) exit;

abstract class Singleton
{
    /**
     * The Singleton's instance is stored in a static field. This field is an
     * array, because we'll allow our Singleton to have subclasses. Each item in
     * this array will be an instance of a specific Singleton's subclass. You'll
     * see how this works in a moment.
     */
    private static $singletonInstances = [];

    /**
     * The Singleton's constructor should always be private to prevent direct
     * construction calls with the `new` operator.
     */
    protected function __construct() { }

    /**
     * Singletons should not be cloneable.
     */
    protected function __clone() { }

    /**
     * Singletons should not be restorable from strings.
     */
    public function __wakeup()
    {
        throw new \Exception("Cannot unserialize a singleton.");
    }

    /**
     * This is the static method that controls the access to the singleton
     * instance. On the first run, it creates a singleton object and places it
     * into the static field. On subsequent runs, it returns the client existing
     * object stored in the static field.
     *
     * This implementation lets you subclass the Singleton class while keeping
     * just one instance of each subclass around.
     *
     * @return static
     */
    public static function getInstance(): Singleton
    {
        $cls = static::class;
        if (!isset(self::$singletonInstances[$cls])) {
            self::$singletonInstances[$cls] = new static();
        }

        return self::$singletonInstances[$cls];
    }
}