#!/usr/bin/env sh
set -e

rm -rf dist && mkdir -p dist

cp -rf src/* dist/
envsubst < src/index.html > dist/index.html
envsubst < src/404.html > dist/404.html
