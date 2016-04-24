[![Build Status](https://travis-ci.org/hollodotme/IncompleteClassAccessor.svg?branch=master)](https://travis-ci.org/hollodotme/IncompleteClassAccessor)
[![Coverage Status](https://coveralls.io/repos/github/hollodotme/IncompleteClassAccessor/badge.svg?branch=master)](https://coveralls.io/github/hollodotme/IncompleteClassAccessor?branch=master)

# IncompleteClassAccessor

Class to access ALL properties of a __PHP_Incomplete_Class

**PLEASE NOTE:** This is a proof of concept and not ment to be used in production software.

## Use case

* You have an object serialized and stored to e.g. the database.
* Time is passing by and the underlying class has been modified, moved or deleted.
* You read the serealized object from your storage and receive a `__PHP_Incomplete_Class` object.
* You want to read the object's properties, even those which are private.

## PHP build-in

* PHP does not provide a convenient way to read private properties from such an object. `ReflectionClass` does not publish any of the properties.
* The available [`unserialize_callback_func`](http://php.net/manual/de/var.configuration.php#unserialize-callback-func)
only lets you load an apropriate class by name (which may not exist anymore).

## What IncompleteClassAccessor does

* Reads the name of the original class from the `__PHP_Incomplete_Class` object. 
* It serializes the `__PHP_Incomplete_Class` object.
* It modifies the resulting serialized string to convert the `__PHP_Incomplete_Class` object to a `stdClass` object.
* It unserializes the modified string and reads all (now accessable) object properties into a key-value array.
* If a property value itself is a `__PHP_Incomplete_Class` object, it creates an instance of `IncompleteClassAccessor` for this value. (So it is recursive.)

## Example usage

```php
<?php

namespace MyVendor\MyProject;

use hollodotme\IncompleteClassAccessor\IncompleteClassAccessor;

# 1. Read some serialized object
$serialized = file_get_contents( '/tmp/serialized.txt' );

# 2. Unserialize
$unserialzed = unserialize( $serialized );

# 3. Check for __PHP_Inclomplete_Class
if ( $unserialzed instanceof \__PHP_Inclomplete_Class )
{
    $accessor = new IncompleteClassAccessor( $unserialized );
 
    # Print the original class name
    echo $accessor->getOriginalClassName();
    
    # Print all properties
    print_r( $accessor->getProperties() );
    
    # Print a single property
    echo $accessor->getProperty( 'someProperty' );
}
```

You are now able to map the old serialized object to an apropriate new one.