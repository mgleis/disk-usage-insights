# PHP SQLite Key Value Store

A PHP library that implements a key/value store with SQLite as the persistence layer.

Use it for small projects / prototypes with at most hundreds or thousands of values.

## Install

    composer require mgleis/php-sqlite-key-value-store

## Usage

    $kv = new KeyValueStore();

### Set a Key

    $kv->set('key', 'value');

### Get a Value by Key

    $value = $kv->get('key');

### Check if a key exists

    $exists = $kv->has('key');

### Delete by Key

    $kv->delete('key');

### Search by Value

    $arr = $kv->searchValueStartsWith('text');
    $arr = $kv->searchValueContains('text');

### Search by Key

    $arr = $kv->searchKeyStartsWith('text');
    $arr = $kv->searchKeyContains('text');


