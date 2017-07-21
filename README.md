# PHPObjectExplorer

An easier way to examine the properties of objects in PHP

## Installation

Either download the ZIP file and include the ```autoloader.php``` file, or you can use composer:

```
composer require taeon/php-object-explorer
```

## Usage

Pass the data that you want to explore into a new Explorer object, and then render the result:

```php
$explorer = new \PHPObjectExplorer\Explorer($data);
echo( $explorer->Render() );
```

...that's it.

The output is 'collapsed' by default. Click on any property to expand it.
