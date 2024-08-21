#!/bin/bash
#
# build - creates the plugin zip file /target/disk-usage-insights.zip
#
set -euo pipefail

cd `dirname $0`

mkdir -p target
cd plugin
zip -r ../target/disk-usage-insights.zip *

cd `dirname $0`
