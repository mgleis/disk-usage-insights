#!/bin/bash
set -euo pipefail

cd `dirname $0`

mkdir -p target
cd plugin
zip -r ../target/disk-usage-insights.zip *

cd `dirname $0`
