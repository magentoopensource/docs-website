#!/bin/bash

# GitHub Contributors Widget - Installation Check
# Quick diagnostic script to verify setup readiness

echo ""
echo "════════════════════════════════════════════════════════════════"
echo "  GitHub Contributors Widget - Installation Check"
echo "════════════════════════════════════════════════════════════════"
echo ""

# Colors for output
GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

ERRORS=0
WARNINGS=0

# Check 1: PHP Version
echo "🔍 Checking PHP..."
if command -v php &> /dev/null; then
    PHP_VERSION=$(php -r "echo PHP_VERSION;")
    PHP_MAJOR=$(php -r "echo PHP_MAJOR_VERSION;")

    if [ "$PHP_MAJOR" -ge 8 ]; then
        echo -e "${GREEN}✅ PHP $PHP_VERSION found${NC}"
    else
        echo -e "${RED}❌ PHP 8.0+ required, found $PHP_VERSION${NC}"
        ERRORS=$((ERRORS+1))
    fi
else
    echo -e "${RED}❌ PHP not found${NC}"
    ERRORS=$((ERRORS+1))
fi
echo ""

# Check 2: Composer
echo "🔍 Checking Composer..."
if command -v composer &> /dev/null; then
    COMPOSER_VERSION=$(composer --version 2>&1 | grep -oP '\d+\.\d+\.\d+' | head -1)
    echo -e "${GREEN}✅ Composer $COMPOSER_VERSION found${NC}"
else
    echo -e "${RED}❌ Composer not found${NC}"
    echo "   Install from: https://getcomposer.org"
    ERRORS=$((ERRORS+1))
fi
echo ""

# Check 3: MySQL
echo "🔍 Checking MySQL..."
if command -v mysql &> /dev/null; then
    MYSQL_VERSION=$(mysql --version | grep -oP '\d+\.\d+\.\d+' | head -1)
    echo -e "${GREEN}✅ MySQL $MYSQL_VERSION found${NC}"
else
    echo -e "${YELLOW}⚠️  MySQL command not found${NC}"
    echo "   Make sure MySQL/MariaDB is installed"
    WARNINGS=$((WARNINGS+1))
fi
echo ""

# Check 4: Project files
echo "🔍 Checking project files..."

if [ -f "composer.json" ]; then
    echo -e "${GREEN}✅ composer.json exists${NC}"
else
    echo -e "${RED}❌ composer.json not found${NC}"
    ERRORS=$((ERRORS+1))
fi

if [ -f ".env" ]; then
    echo -e "${GREEN}✅ .env file exists${NC}"
else
    echo -e "${YELLOW}⚠️  .env file not found${NC}"
    echo "   Run: cp .env.example .env"
    WARNINGS=$((WARNINGS+1))
fi

if [ -f "database/schema.sql" ]; then
    echo -e "${GREEN}✅ database/schema.sql exists${NC}"
else
    echo -e "${RED}❌ database/schema.sql not found${NC}"
    ERRORS=$((ERRORS+1))
fi

if [ -d "vendor" ]; then
    echo -e "${GREEN}✅ vendor directory exists (dependencies installed)${NC}"
else
    echo -e "${YELLOW}⚠️  vendor directory not found${NC}"
    echo "   Run: composer install"
    WARNINGS=$((WARNINGS+1))
fi
echo ""

# Check 5: PHP Extensions
echo "🔍 Checking PHP extensions..."
REQUIRED_EXTS=("pdo" "pdo_mysql" "json" "mbstring" "curl")

for ext in "${REQUIRED_EXTS[@]}"; do
    if php -m | grep -q "^$ext$"; then
        echo -e "   ${GREEN}✓ $ext${NC}"
    else
        echo -e "   ${RED}✗ $ext (MISSING)${NC}"
        ERRORS=$((ERRORS+1))
    fi
done
echo ""

# Check 6: Directories
echo "🔍 Checking directories..."

if [ -d "storage/logs" ]; then
    if [ -w "storage/logs" ]; then
        echo -e "${GREEN}✅ storage/logs exists and is writable${NC}"
    else
        echo -e "${YELLOW}⚠️  storage/logs exists but not writable${NC}"
        echo "   Run: chmod 755 storage/logs"
        WARNINGS=$((WARNINGS+1))
    fi
else
    echo -e "${YELLOW}⚠️  storage/logs does not exist${NC}"
    echo "   Run: mkdir -p storage/logs && chmod 755 storage/logs"
    WARNINGS=$((WARNINGS+1))
fi
echo ""

# Summary
echo "════════════════════════════════════════════════════════════════"
echo "  Summary"
echo "════════════════════════════════════════════════════════════════"
echo ""

if [ $ERRORS -eq 0 ] && [ $WARNINGS -eq 0 ]; then
    echo -e "${GREEN}🎉 Perfect! All checks passed.${NC}"
    echo ""
    echo "Next steps:"
    echo "  1. Run: composer install"
    echo "  2. Run: php demo/test-setup.php"
    echo "  3. Run: php demo/test-github-api.php"
    echo ""
elif [ $ERRORS -eq 0 ]; then
    echo -e "${YELLOW}⚠️  Setup complete with $WARNINGS warning(s).${NC}"
    echo ""
    echo "Review warnings above, then run:"
    echo "  php demo/test-setup.php"
    echo ""
else
    echo -e "${RED}❌ Setup incomplete. $ERRORS error(s), $WARNINGS warning(s).${NC}"
    echo ""
    echo "Please fix errors above before proceeding."
    echo ""
    exit 1
fi

exit 0
