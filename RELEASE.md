# Release Instructions for Soft-Mapper v2.0.0

This document provides instructions for publishing Soft-Mapper to Packagist and creating an official release.

## Prerequisites

- All code changes have been merged to the main branch
- CHANGELOG.md has been updated with release date
- composer.json is valid and properly configured
- All tests pass (if applicable)

## Release Checklist

### 1. Prepare the Release

- [x] Update composer.json with proper metadata
- [x] Update .gitignore to exclude vendor/ and composer.lock
- [x] Update CHANGELOG.md with release date (2025-10-30)
- [x] Update README.md with Composer installation instructions
- [x] Validate composer.json with `composer validate --strict`

### 2. Create Git Tag

After merging this PR to the main branch, create and push the release tag:

```bash
# Switch to main branch
git checkout main
git pull origin main

# Create annotated tag for v2.0.0
git tag -a v2.0.0 -m "Release version 2.0.0 - Major feature update with ORM relationships"

# Push the tag to GitHub
git push origin v2.0.0
```

### 3. Create GitHub Release

1. Go to https://github.com/DedSecTeam17/Soft-Mapper/releases/new
2. Select tag: `v2.0.0`
3. Release title: `v2.0.0 - Major Feature Release`
4. Description: Copy content from CHANGELOG.md for v2.0.0
5. Check "Set as the latest release"
6. Click "Publish release"

### 4. Submit to Packagist

#### First Time Submission

If the package is not yet on Packagist:

1. Go to https://packagist.org/
2. Sign in with your GitHub account
3. Click "Submit" in the top menu
4. Enter repository URL: `https://github.com/DedSecTeam17/Soft-Mapper`
5. Click "Check" then "Submit"

#### Enable Auto-Update Hook

To automatically update Packagist when you push new releases:

1. Go to https://packagist.org/packages/dedsecteam17/soft-mapper
2. Click on your package
3. Click "Settings" or "Edit"
4. Copy the GitHub Service Hook URL
5. Go to GitHub repository Settings > Webhooks
6. Add webhook with the Packagist URL
7. Content type: `application/json`
8. Events: Just the push event
9. Active: checked

Alternatively, use the simpler method:
1. On Packagist, enable GitHub auto-update integration
2. Grant Packagist access to your GitHub repository

### 5. Verify Installation

After publishing, verify the package can be installed:

```bash
# Create a test directory
mkdir /tmp/test-soft-mapper
cd /tmp/test-soft-mapper

# Initialize a new project
composer init --no-interaction

# Require the package
composer require dedsecteam17/soft-mapper

# Verify it works
php -r "require 'vendor/autoload.php'; echo class_exists('SoftMapper') ? 'Success!' : 'Failed!';"
```

### 6. Announce the Release

- Update the GitHub repository description
- Tweet/post about the release (if applicable)
- Update any documentation sites
- Notify users in discussions/issues

## Version Naming Convention

Soft-Mapper follows [Semantic Versioning](https://semver.org/):

- **MAJOR** version (X.0.0): Incompatible API changes
- **MINOR** version (0.X.0): Backwards-compatible new features
- **PATCH** version (0.0.X): Backwards-compatible bug fixes

## Future Releases

For future releases, follow these steps:

1. Update CHANGELOG.md with new version section
2. Make code changes and commit
3. Update version in documentation if needed
4. Merge to main branch
5. Create and push new git tag: `git tag -a vX.Y.Z -m "Release vX.Y.Z"`
6. Create GitHub release
7. Packagist will auto-update (if hook is configured)

## Troubleshooting

### Package Not Showing on Packagist

- Verify the git tag exists: `git tag -l`
- Check the tag is pushed: `git ls-remote --tags origin`
- Ensure composer.json is valid: `composer validate --strict`
- Verify GitHub repository is public

### Composer Cannot Find Package

- Wait a few minutes for Packagist to index
- Check package name matches: `dedsecteam17/soft-mapper`
- Verify minimum stability settings in your project's composer.json
- Clear composer cache: `composer clear-cache`

### Installation Fails

- Check PHP version requirements (>=5.6)
- Verify PDO and PDO_MySQL extensions are installed
- Review composer error messages for specific issues

## Support

For issues or questions:
- GitHub Issues: https://github.com/DedSecTeam17/Soft-Mapper/issues
- Packagist Page: https://packagist.org/packages/dedsecteam17/soft-mapper
