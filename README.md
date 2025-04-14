# wordpress-utilities
General utilities for use in WordPress plugins


# User Roles and Capabilities
You can programmatically build up a user role and its capabilities by extending the classes in this section.

## BaseCapability
Provides the interface for capabilities, simply requiring a name.

## BaseRole
Provides the interface for a role, and the method to add that role. 

Roles should be built to contain their associated capabilities.

```php
// The capability
class MyNewCapability extends \DigitalNature\WordPressUtilities\Common\Users\Capabilities\BaseCapability {
    /**
     * @return string
     */
    public static function get_capability_name(): string
    {
        return 'my_new_capability';
    }
}

// The role
class MyNewRole extends \DigitalNature\WordPressUtilities\Common\Users\Roles\BaseRole
{
    /**
     * @return string
     */
    public static function get_role_slug(): string
    {
        return 'my-new-role';
    }

    /**
     * @return string
     */
    public static function get_role_name(): string
    {
        return 'My New Role'
    }

    /**
     * @return string[]
     */
    public static function get_capabilities(): array
    {
        return [
            MyNewCapability::get_capability_name()
        ]
    }
}

// The code below shows example usage of the roles/capabilities

// Include the role
MyNewRole::add_role();

// Adding capability for an admin menu item
add_submenu_page(
    'parent_slug',
    'Your Page Title',
    'Your Menu Title',
    MyNewCapability::get_capability_name(),
    'submenu_slug',
    'your callback'
);

// checking capability for the logged in user
$canAccess = current_user_can( MyNewCapability::get_capability_name() );
```


# Config
## PluginConfiguration
The `PluginConfiguration` class gives quick access to a plugins name/dir/file/url for use in templates, adding assets etc.


# Helpers
## Settings
### UserSettingHelper
This abstract class provides the interface for user settings, stored in metadata.

By extending this class you gain the ability to turn settings on and off programatically using the setting helper class.

```php
// Define the setting class
class MySettingHelper extends \DigitalNature\WordPressUtilities\Helpers\Settings\UserSettingHelper
{
    public static function get_meta_key(): string
    {
        return 'my_setting_metadata_key';
    }
}

// switch the setting on/off
MySettingHelper::enable($user);
MySettingHelper::disable($user);

// There are aliases, as some setting names lend themselves to a different terminology
MySettingHelper::turn_on($user);
MySettingHelper::turn_off($user);

// You can check whether the setting is turned on (or not) for the given user
MySettingHelper::is_enabled($user);
MySettingHelper::is_turned_on($user);

// There is a toggle method, should you need to simply switch the setting
MySettingHelper::toggle($user);
```

## ConfigHelper
The `ConfigHelper` simply provides environmental checks, so that you can restrict functionality/integrations 
by environment.

```php
// check if we're on the live site. This is dependent on WP_ENVIRONMENT_TYPE being defined and set to 'live'
ConfigHelper::is_live_site()

// check if we're running a script. This is dependent on DN_IS_SCRIPT being defined and set to true
ConfigHelper::is_script();
```

## CustomPostTypeHelper
Provides an alternative way to register custom post types. 

The benefit here is that we can automatically load the correct model for a custom post type if we have used the `CustomPostTypeHelper` to register it.

```php
// Register your post type
CustomPostTypeHelper::register_post_type(MyModel::class, $args);

// Returns the correct model for the given post (providing we registered the post type using the helper)
$myModel = ModelFactory::from_id(123);
```

## DateHelper
Provides some useful methods for manipulating dates, such as getting the start/end of the month.

## LogHelper
As the name suggests, this logs messages!

For local environments (where `WP_ENVIRONMENT` is 'local', 'dev' or 'development') it will output to a local debug file `wp-uploads/local-debug.log`

When running scripts (see `ConfigHelper`) the `LogHelper` will output to screen rather than file.

For other environments it will output to the php error log.

Should you need them, you can retrieve previously logged messages (from this request only): 

```php
LogHelper::get_logs(); // all logs
LogHelper::get_last_log(); // just the last thing logged
```

## MessageHelper
A very simple error and exit message helper for when you need to bring the script to an abrupt end.

```php
MessageHelper::error_and_exit("My message here");
```

## TemplateHelper
Allows template to be rendered, either immediately or returned in a variable.

```php
TemplateHelper::render(
    'plugin-name/my-template.php',
    [
        'arg' => $argument,
        'arg2' => $anotherArgument
    ],
    trailingslashit('plugin-dir/templates'),
    $returnAsString // boolean
);
```



# Models
## Model
Models are based on WordPress post types, offering the opportunity to create a model per post type.

The biggest advantage of models is encapsulation, having a single class to store and manipulate the data for a post (including its metadata).

A model can be loaded in numerous ways, but the simplest is by ID

```php
// If we know the model class then we can directly pull through that
$myModel = MyModel::from_id(123);

// If we don't know the correct class, just the ID then we have a factory method to retrieve. 
// Note that the factory method will only work for custom post types we have registered using the `CustomPostTypeHelper`
$myModel = ModelFactory::from_id(123);
```

Loading models uses the `ModelStore`, saving resources by ensuring that we only load each model once.

You can define your own methods on your models, or simply define your metadata maps and manipulate the metadata directly.
For example if we have a metadata key of 'my_metadata' we can update like so:

```php
$myModel->my_metadata = 'abc123';
```

If you want to save the updated metadata values then you can do so either by saving the individual field or the entire model

```php
$myModel->save_attribute('my_metadata'); // a single metadata value
$myModel->save(); // all metadata values
```

## ModelNote
Notes can be created for each model, to give a history of changes, audit log etc.

You can retrieve or create notes for any model using the `ModelNoteRepository`



# Patterns
## Singleton
The `Singleton` pattern ensures that there is only one instance of the extending class.

This allows more efficient memory management, ensuring we don't need to load the same resources multiple times.

It also allows a central store of messages - for example log messages in `LogHelper` - that can be retrieved from anywhere in the codebase.


# Query
Queries are used to retrieve posts by their metadata attributes.

Queries are used by repositories, their results are cached for 5 minutes by default.

# Repositories
Repositories are used for database interactions relating to models.

Where we need to create a model, a repository should be used. The parameters for the create method will be specific to the type of model you are creating.

```php
// Create a model note for $myModel, authored by $user
ModelNoteRepository::create($myModel, 'The content of my new note', $user);
```

The benefit to creating models using repositories is that we can more closely manage the caching of model queries. For example if we create a new instance of `MyModel` we know that we can clear the cache for `MyModel::all()` as it has been invalidated.

You may wish to create a repository for your own model classes with create/delete/retrieve/flush caches methods.

```php
class MyModelRepository
{
    public static function create(WP_User $owner): MyModel
    {
        // ...
    }
    
    public static function flush_caches(MyModel $myModel): void
    {
        // ...
    }
}
```

## ModelNotesRepository
The `ModelNotesRepository` provides methods to create, delete and retrieve model notes. It also provides a cache flushing method that is automatically triggered when a note is deleted.


# Stores
Stores are places for us to hold onto data for this request.

## CustomPostTypeStore
Allows us to look up Models for particular custom post types without needing to know the required model.

## InMemoryStore
The base class that looks after the storage and retrieval of records.

## ModelStore
Holds the models we have loaded and returns references to them, ensuring we don't need to look up models and their metadata multiple times.

# Traits
## CacheableModelTrait
This is where the caching logic lives for our models, the `Model` class uses this trait

