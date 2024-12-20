#!/bin/bash

./vendor/bin/phpcs --standard=PHPCompatibility --runtime-set testVersion 7.4 --ignore=plugin/res/js/htmx-1.9.12.min.js plugin

echo "DONE"
