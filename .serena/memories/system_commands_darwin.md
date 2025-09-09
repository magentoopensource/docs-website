# System Commands for Darwin (macOS)

## Standard Unix Commands Available
```bash
# File operations
ls -la                      # List files with details
find . -name "*.php"        # Find files by pattern
grep -r "search_term" .     # Search in files recursively
cd /path/to/directory      # Change directory
pwd                        # Print working directory
mkdir -p path/to/dir       # Create directories recursively

# File viewing/editing
cat filename.txt           # Display file contents
head -n 20 filename.txt    # Show first 20 lines
tail -f storage/logs/laravel.log  # Follow log file
less filename.txt          # Page through file

# Process management
ps aux | grep php          # Show running PHP processes
kill -9 <pid>             # Kill process by ID
lsof -i :8000             # Show what's using port 8000

# Git operations
git status                # Check git status
git log --oneline -10     # Show recent commits
git branch -a             # Show all branches
git diff                  # Show changes
```

## macOS Specific Considerations
- Use `brew` for package management if needed
- File paths use forward slashes
- Case-sensitive filesystem (usually)
- Uses `sed -i ''` for inline file editing (note the empty string after -i)

## Laravel/PHP Specific
```bash
# Check PHP version and extensions
php -v
php -m | grep extension_name

# Composer operations
composer install --no-dev  # Production dependencies only
composer dump-autoload     # Regenerate autoloader

# Node/NPM operations  
node -v                    # Check Node version
npm list                   # Show installed packages
npm outdated              # Check for updates
```