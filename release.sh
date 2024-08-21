#!/bin/bash
#
# release - copies the current release to /target/svnrepo which contains the svn plugin repository
#
set -euo pipefail

cd `dirname $0`

mkdir -p target
cd target/svnrepo/trunk
cp -R ../../../plugin/* ./
svn status

echo 'Hints:'
echo '# svn ci -m "commit message"'
echo '# svn cp trunk tags/1.x'
echo
