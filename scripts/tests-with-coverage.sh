#!/usr/bin/env bash

# Exit script if you try to use an uninitialized variable.
set -o nounset

# Use the error status of the first failure, rather than that of the last item in a pipeline.
set -o pipefail

PERCENT=$(./vendor/bin/phpunit ./test --coverage-text --colors=never | tee /dev/tty | grep -Eo --color=never '^\s*Lines:\s*(\d+.)' | grep -Eo --color=never '(\d+)')

if [[ ${PERCENT} -gt 75 ]]; then
    COLOR=green
elif [[ ${PERCENT} -gt 50 ]]; then
    COLOR=yellow
elif [[ ${PERCENT} -gt 25 ]]; then
    COLOR=orange
else
    COLOR=red
fi

BADGE="'{\"schemaVersion\": 1, \"label\": \"coverage\", \"message\": \"${PERCENT} %\", \"color\": \"${COLOR}\"}'"

curl --header "Content-Type: application/json" \
  --header "X-Auth-Token: ${BADGE_TOKEN}" \
  --request POST \
  --data ${BADGE} \
  https://badges.barthy.koeln/badge/image-upload-bundle/coverage

exit 0
