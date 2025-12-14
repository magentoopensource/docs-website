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

# Marker file to track if we've done the post-rewrite clone
MARKER_FILE="$BASE_PATH/resources/docs/.history-rewrite-fixed"

for v in "${DOCS_VERSIONS[@]}"; do
    DOCS_DIR="$BASE_PATH/resources/docs/$v"

    # If marker doesn't exist and docs dir exists, force fresh clone
    if [ ! -f "$MARKER_FILE" ] && [ -d "$DOCS_DIR" ]; then
        echo "First run after history rewrite - forcing fresh clone for $v..."
        rm -rf "$DOCS_DIR"
    fi

    if [ -d "$DOCS_DIR" ]; then
        echo "Updating documentation for $v..."
        cd "$DOCS_DIR"
        git fetch --force origin "$v"
        git reset --hard "origin/$v"
        echo "Successfully updated $v"
    else
        echo "Cloning $v..."
        mkdir -p "$BASE_PATH/resources/docs"
        git clone --single-branch --branch "$v" https://github.com/magentoopensource/docs.git "$DOCS_DIR"
    fi
done

# Create marker file after successful sync
touch "$MARKER_FILE"

# Only clear cache if not in Deployer context (Deployer handles this separately)
if [ -z "$DEPLOYER_ROOT" ]; then
    echo "Clearing application cache..."
    cd "$BASE_PATH"
    php artisan cache:clear
fi
