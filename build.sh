#!/bin/bash
#
# build - creates the plugin zip file /target/disk-usage-insights.zip
#
set -euo pipefail

cd `dirname $0`

mkdir -p target
cd plugin
rm ../target/disk-usage-insights.zip
zip -r ../target/disk-usage-insights.zip * -x output/*db*

cd `dirname $0`
