#!/usr/bin/env sh

set -euo pipefail

function usage {
    echo "Usage: $0 -b <bucket>"; exit 1;
}

while getopts "b:" opt; do
    case $opt in
        b)
            BUCKET_NAME=$OPTARG
        ;;
    esac
done

if [[ -z "${BUCKET_NAME}" ]]; then
    usage
fi

aws s3 cp --recursive "dist" "s3://${BUCKET_NAME}"
