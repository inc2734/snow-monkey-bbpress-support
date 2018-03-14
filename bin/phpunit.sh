#!/usr/bin/env bash

if [ -e /tmp/wordpress-tests-lib -a -e /tmp/wordpress-tests-lib/includes/functions.php ]; then
  plugindir=$(pwd)

  cd ${plugindir};
  vendor/bin/phpunit --configuration= ${plugindir}/phpunit.xml.dist
  exit 0
fi

dir=$(cd $(dirname $0) && pwd)
bash "${dir}/wpphpunit.sh"
