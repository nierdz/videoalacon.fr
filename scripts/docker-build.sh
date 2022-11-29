#!/usr/bin/env bash

set -o errexit
set -o pipefail
set -o nounset

DEBUG=${DEBUG:=0}
[[ $DEBUG -eq 1 ]] && set -o xtrace

version=$(sed -n '/LABEL/s/LABEL version=//p' Dockerfile)
docker build -t "nierdz/mad-rabbit.com:${version}" .
