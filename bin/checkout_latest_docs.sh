#!/bin/bash

DOCS_VERSIONS=(
  main
)

# Determine the correct base path
# If DEPLOYER_ROOT is set, we're in a Deployer context
if [ -n "$DEPLOYER_ROOT" ]; then
    BASE_PATH="$DEPLOYER_ROOT/shared"
else
    # Running locally or in old deployment setup
    BASE_PATH="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
fi

echo "Using base path: $BASE_PATH"

for v in "${DOCS_VERSIONS[@]}"; do
    if [ -d "$BASE_PATH/resources/docs/$v" ]; then
        echo "Pulling latest documentation updates for $v..."
        (cd "$BASE_PATH/resources/docs/$v" && git pull)
    else
        echo "Cloning $v..."
        mkdir -p "$BASE_PATH/resources/docs"
        git clone --single-branch --branch "$v" https://github.com/magentoopensource/docs.git "$BASE_PATH/resources/docs/$v"
    fi;
done

# Only clear cache if not in Deployer context (Deployer handles this separately)
if [ -z "$DEPLOYER_ROOT" ]; then
    echo "Clearing application cache..."
    cd "$BASE_PATH"
    php artisan cache:clear
fi
