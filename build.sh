#!/bin/bash

# Installation package builder for com_ra_tools
# Usage: ./build.sh [version]
# Example: ./build.sh 3.5.1

# Default version if not provided
VERSION="${1:-9.9.9}"

# Navigate to com_ra_tools directory
cd "$(dirname "$0")/com_ra_tools" || exit 1

echo "Building com_ra_tools-${VERSION}.zip..."

# Step 1: Copy manifest to correct location (dereference symlink)
echo "  - Copying manifest file..."
cp administrator/ra_tools.xml ra_tools.xml

# Step 2: Create zip with required folders, manifest, and script
echo "  - Compressing files..."
zip -r "com_ra_tools-${VERSION}.zip" \
  administrator \
  site \
  installer \
  modules \
  plugins \
  media \
  script.php \
  ra_tools.xml \
  -x "*.DS_Store" \
  ".git/*" \
  "*.zip"

# Step 3: Verify zip was created
if [ -f "com_ra_tools-${VERSION}.zip" ]; then
  echo "✓ Package created successfully: com_ra_tools-${VERSION}.zip"
  
  # Step 4: Delete the temporary manifest copy
  echo "  - Cleaning up temporary files..."
  rm ra_tools.xml
  
  echo "✓ Build complete"
else
  echo "✗ Error: Failed to create zip file"
  rm -f ra_tools.xml
  exit 1
fi
