#!/usr/bin/env bash

set -e

if [[ "false" != "$TRAVIS_PULL_REQUEST" ]]; then
  echo "Not deploying pull requests."
  exit
fi

if [[ "master" != "$TRAVIS_BRANCH" ]]; then
  echo "Not on the 'master' branch."
  exit
fi

if [[ "7.1" != "$TRAVIS_PHP_VERSION" ]]; then
	echo "deploy only PHP 7"
	exit
fi

git clone -b release --quiet git@github.com:inc2734/snow-monkey-bbpress-support.git release
cd release
ls | xargs rm -rf
ls -la
cd ../
rm -rf node_modules
yarn install
rm -rf vendor
composer install --no-dev
yarn run gulp build
rsync -a --exclude="release" --exclude=".*" ./ release/
cd release
rm -rf .gitignore .editorconfig .travis.yml .travis node_modules tests package.json gulpfile.js yarn.lock composer.json composer.lock phpcs.ruleset.xml phpunit.xml.dist bin
ls -la

git add -A
git commit -m "[ci skip] release branch update from travis $TRAVIS_COMMIT"
git push origin release
