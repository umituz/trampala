#!/bin/bash
# stop.sh - Trampala Docker Environment Stop Script

set -e

# Color codes for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Function to print colored output
print_status() {
    echo -e "${BLUE}[INFO]${NC} $1"
}

print_success() {
    echo -e "${GREEN}[SUCCESS]${NC} $1"
}

print_warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

print_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

print_status "Stopping Trampala development environment..."

# Parse command line arguments
CLEAN_CACHE=false
CLEAN_VOLUMES=false
CLEAN_IMAGES=false

while [[ $# -gt 0 ]]; do
    case $1 in
        --clean-cache)
            CLEAN_CACHE=true
            shift
            ;;
        --clean-volumes)
            CLEAN_VOLUMES=true
            shift
            ;;
        --clean-all)
            CLEAN_CACHE=true
            CLEAN_VOLUMES=true
            CLEAN_IMAGES=true
            shift
            ;;
        *)
            print_warning "Unknown option: $1"
            shift
            ;;
    esac
done

# Clean Nuxt.js build cache if requested
if [ "$CLEAN_CACHE" = true ]; then
    print_status "Cleaning Nuxt.js build cache..."
    if [ -d "nuxt/.nuxt" ]; then
        rm -rf nuxt/.nuxt
        print_success "Nuxt.js build cache cleared"
    fi
    
    if [ -d "nuxt/.output" ]; then
        rm -rf nuxt/.output
        print_success "Nuxt.js output cache cleared"
    fi
    
    if [ -d "nuxt/node_modules/.cache" ]; then
        rm -rf nuxt/node_modules/.cache
        print_success "Nuxt.js node_modules cache cleared"
    fi
fi

# Stop and remove containers
print_status "Stopping Docker containers..."
if [ "$CLEAN_VOLUMES" = true ]; then
    docker compose down -v
    print_status "Docker volumes removed"
elif [ "$CLEAN_IMAGES" = true ]; then
    docker compose down --rmi all
    print_status "Docker images removed"
else
    docker compose down
fi

# Check if containers are stopped
if ! docker ps | grep -q "trampala"; then
    print_success "All Trampala containers have been stopped!"
else
    print_warning "Some containers might still be running"
    docker ps | grep "trampala"
fi

print_success "Trampala development environment stopped successfully!"
print_status ""
print_status "Usage options:"
print_status "  ./start.sh                    - Start development environment"
print_status "  ./stop.sh                     - Stop containers only"
print_status "  ./stop.sh --clean-cache       - Stop and clean Nuxt.js cache"
print_status "  ./stop.sh --clean-volumes     - Stop and remove volumes"
print_status "  ./stop.sh --clean-all         - Stop and clean everything"