#!/bin/bash

# Hospital System - Deployment Script for Linux/Mac
# Creates deployment package for Hostinger shared hosting

set -e

echo "========================================"
echo "Hospital System - Deployment Package"
echo "========================================"
echo ""

# Check if required tools exist
if ! command -v node &> /dev/null; then
    echo "ERROR: Node.js is not installed"
    exit 1
fi

if ! command -v npm &> /dev/null; then
    echo "ERROR: npm is not installed"
    exit 1
fi

# Clean previous builds
echo "[1/6] Cleaning previous builds..."
rm -rf frontend/dist hospital-deployment.zip hospital-deploy-temp

# Install frontend dependencies
echo "[2/6] Installing frontend dependencies..."
cd frontend
npm ci
cd ..

# Build frontend for production
echo "[3/6] Building frontend for production..."
cd frontend
npm run build
cd ..

# Create temporary deployment directory
echo "[4/6] Preparing deployment files..."
mkdir -p hospital-deploy-temp

# Copy backend files (excluding vendor, node_modules, .env, tests)
echo "[5/6] Copying backend files..."
rsync -av --exclude='vendor' \
          --exclude='node_modules' \
          --exclude='.env' \
          --exclude='.env.*' \
          --exclude='tests' \
          --exclude='.git' \
          --exclude='storage/logs/*' \
          --exclude='storage/framework/cache/*' \
          --exclude='storage/framework/sessions/*' \
          --exclude='storage/framework/views/*' \
          backend/ hospital-deploy-temp/backend/

# Copy frontend dist
echo "Copying frontend build..."
mkdir -p hospital-deploy-temp/frontend
cp -r frontend/dist hospital-deploy-temp/frontend/

# Copy deployment instructions
cp DEPLOYMENT.md hospital-deploy-temp/

# Create zip package
echo "[6/6] Creating deployment zip..."
if command -v zip &> /dev/null; then
    cd hospital-deploy-temp
    zip -r ../hospital-deployment.zip .
    cd ..
elif command -v tar &> /dev/null; then
    # Fallback to tar if zip is not available
    tar -czf hospital-deployment.tar.gz -C hospital-deploy-temp .
    echo "Note: Created .tar.gz instead of .zip"
    echo "You may need to convert to .zip for easier upload to Hostinger"
else
    echo "WARNING: Neither zip nor tar found. Creating directory structure instead."
    echo "Please manually compress hospital-deploy-temp folder."
fi

# Clean up temporary directory
rm -rf hospital-deploy-temp

echo ""
echo "========================================"
echo "Deployment package created successfully!"
echo "========================================"
echo ""
echo "File: hospital-deployment.zip (or hospital-deployment.tar.gz)"
echo ""
echo "Next steps:"
echo "1. Check backend/.env.production and update credentials"
echo "2. Upload hospital-deployment.zip to Hostinger"
echo "3. Follow DEPLOYMENT.md instructions"
echo ""
