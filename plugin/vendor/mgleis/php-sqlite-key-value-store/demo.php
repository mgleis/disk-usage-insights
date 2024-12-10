<?php

require_once 'vendor/autoload.php';

use Mgleis\PhpSqliteKeyValueStore\KeyValueStore;

$kv = new KeyValueStore();
$kv->set("key", "value");
$kv->set("arr", [0, 1, 2, 3]);
$kv->set("assarr", ['name' => 'John']);

var_dump($kv->get("key"));
var_dump($kv->get("arr"));
var_dump($kv->get("assarr"));

var_dump($kv->has('key'));
$kv->delete('key');
var_dump($kv->has('key'));
var_dump($kv->get('key'));

$kv->set('doc-1', 'doc1');
$kv->set('doc-2', 'doc2');
var_dump($kv->searchKeyStartsWith('doc-'));
