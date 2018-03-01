#!/usr/bin/env bash

set -ex

if [ ! -e .plugins ]; then
  BBPRESS=$(curl "https://api.wordpress.org/plugins/info/1.0/bbpress.json" | jq -r .download_link)
  curl -sL $BBPRESS -o bbpress.zip
  unzip -q bbpress.zip -d .plugins
  rm -f bbpress.zip
fi

#
# @todo It can not be used because it is caught by GitHub API rate limit...
#
# if [ ! -e .themes ]; then
#   GH=$(curl "https://api.github.com/repos/inc2734/snow-monkey/releases/latest");
#   SNOW_MONKEY=$(echo "$GH" | jq -r .assets[0].browser_download_url)
#
#   if [ -n "$SNOW_MONKEY" ]; then
#     curl -sL $SNOW_MONKEY -o snow-monkey.zip
#     mkdir -p .themes/snow-monkey
#     unzip -q snow-monkey.zip -d .themes/snow-monkey
#     rm -f snow-monkey.zip
#   else
#     echo $GH;
#     exit 1;
#   fi
# fi
