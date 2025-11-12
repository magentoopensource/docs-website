#!/bin/bash

###############################################################################
# GitHub Contributors Widget - Manual Cron Job Runner
#
# This script allows you to manually run the cron job for testing purposes
# It provides better output formatting and error handling than running directly
#
# Usage:
#   ./cron/run-manual.sh
#   bash cron/run-manual.sh
#
###############################################################################

set -e  # Exit on error

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Get script directory
SCRIPT_DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
PROJECT_ROOT="$(dirname "$SCRIPT_DIR")"

echo ""
echo "════════════════════════════════════════════════════════════════"
echo "  GitHub Contributors Widget - Manual Cron Job"
echo "════════════════════════════════════════════════════════════════"
echo ""

# Check if PHP is available
if ! command -v php &> /dev/null; then
    echo -e "${RED}❌ ERROR: PHP not found${NC}"
    echo "Please install PHP or add it to your PATH"
    exit 1
fi

PHP_VERSION=$(php -r "echo PHP_VERSION;")
echo -e "${BLUE}🐘 PHP Version:${NC} $PHP_VERSION"

# Check if project exists
if [ ! -f "$PROJECT_ROOT/cron/update-contributors.php" ]; then
    echo -e "${RED}❌ ERROR: Cron script not found${NC}"
    echo "Expected: $PROJECT_ROOT/cron/update-contributors.php"
    exit 1
fi

echo -e "${BLUE}📁 Project Root:${NC} $PROJECT_ROOT"

# Check if .env exists
if [ ! -f "$PROJECT_ROOT/.env" ]; then
    echo -e "${YELLOW}⚠️  WARNING: .env file not found${NC}"
    echo "Create one from .env.example"
    read -p "Continue anyway? (y/n) " -n 1 -r
    echo
    if [[ ! $REPLY =~ ^[Yy]$ ]]; then
        exit 1
    fi
fi

# Check if vendor directory exists
if [ ! -d "$PROJECT_ROOT/vendor" ]; then
    echo -e "${RED}❌ ERROR: Dependencies not installed${NC}"
    echo "Run: composer install"
    exit 1
fi

echo -e "${GREEN}✅ Pre-flight checks passed${NC}"
echo ""

# Ask for confirmation
read -p "Run the cron job now? (y/n) " -n 1 -r
echo
if [[ ! $REPLY =~ ^[Yy]$ ]]; then
    echo "Cancelled"
    exit 0
fi

echo ""
echo "════════════════════════════════════════════════════════════════"
echo "  Running Cron Job..."
echo "════════════════════════════════════════════════════════════════"
echo ""

# Record start time
START_TIME=$(date +%s)

# Run the cron job
cd "$PROJECT_ROOT"

if php cron/update-contributors.php; then
    EXIT_CODE=0
else
    EXIT_CODE=$?
fi

# Record end time
END_TIME=$(date +%s)
DURATION=$((END_TIME - START_TIME))

echo ""
echo "════════════════════════════════════════════════════════════════"

if [ $EXIT_CODE -eq 0 ]; then
    echo -e "${GREEN}✅ Cron Job Completed Successfully${NC}"
else
    echo -e "${RED}❌ Cron Job Failed (Exit Code: $EXIT_CODE)${NC}"
fi

echo "════════════════════════════════════════════════════════════════"
echo ""
echo -e "${BLUE}Duration:${NC} ${DURATION}s"
echo ""

# Show log location
if [ -f "$PROJECT_ROOT/storage/logs/github-widget.log" ]; then
    echo "📋 Detailed logs available at:"
    echo "   $PROJECT_ROOT/storage/logs/github-widget.log"
    echo ""
    echo "View logs:"
    echo "   tail -f $PROJECT_ROOT/storage/logs/github-widget.log"
    echo ""

    # Ask if user wants to see recent logs
    read -p "Show last 20 log lines? (y/n) " -n 1 -r
    echo
    if [[ $REPLY =~ ^[Yy]$ ]]; then
        echo ""
        echo "════════════════════════════════════════════════════════════════"
        echo "  Recent Log Entries"
        echo "════════════════════════════════════════════════════════════════"
        echo ""
        tail -n 20 "$PROJECT_ROOT/storage/logs/github-widget.log"
        echo ""
    fi
fi

# Show cache status
echo "💾 Cache Status:"
if command -v mysql &> /dev/null; then
    # Try to query cache (will fail if MySQL credentials are wrong, but that's ok)
    mysql -u widget_user -p github_contributors -e "SELECT COUNT(*) as cached_items FROM widget_cache;" 2>/dev/null || echo "   (Run test-services.php to check cache)"
else
    echo "   (MySQL not available in PATH)"
fi
echo ""

exit $EXIT_CODE
