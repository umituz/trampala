# Trampala - Full-Stack Listing Management Platform

## 📋 Project Overview

Trampala is a comprehensive full-stack listing management platform featuring a Laravel 12.x API backend and a modern Nuxt.js 3 frontend. The project includes complete listing creation, admin approval system, category management, location-based filtering, and a responsive web interface. The platform follows modern development patterns with Docker containerization and comprehensive testing coverage.

## 🚀 Quick Start

### Prerequisites

- Docker & Docker Compose
- Git

### Installation

1. **Clone the repository**
```bash
git clone <repository-url>
cd trampala
```

2. **Start Docker environment**
```bash
./start.sh
```

3. **Set up the application**
```bash
# Enter Laravel container
docker exec -it trampala_laravel bash

# Run migrations and seed data
php artisan migrate:fresh --seed
```

4. **Generate API tokens for testing**
```bash
# For admin user
docker exec -it trampala_laravel php artisan api:token admin@test.com --name=api-testing

# For regular user  
docker exec -it trampala_laravel php artisan api:token user@test.com --name=api-testing
```

### Default Test Users
After seeding, the following users are available:
- **Admin**: `admin@test.com` / Password: `admin123`
- **User**: `user@test.com` / Password: `user123`

## 🏗️ Architecture

The project follows a modular, service-oriented architecture:

### Core Technologies

#### Backend
- **Laravel 12.x** - PHP Framework
- **PostgreSQL 15** - Primary Database
- **Redis 6.2** - Caching & Sessions
- **Laravel Sanctum** - API Authentication
- **PHPUnit** - Testing Framework

#### Frontend
- **Nuxt.js 3** - Vue.js Framework
- **TypeScript** - Type Safety
- **Tailwind CSS** - Utility-first CSS
- **Pinia** - State Management
- **Nuxt UI** - Component Library

### Architectural Patterns
- **Repository Pattern** - Data access abstraction
- **Service Layer** - Business logic encapsulation
- **Observer Pattern** - Event-driven audit logging
- **Request/Response Classes** - Standardized API format
- **Policy Pattern** - Authorization logic

## 📚 API Endpoints

Base URL: `http://localhost:8080`

### Authentication
- `POST /api/register` - User registration
- `POST /api/login` - User login
- `POST /api/logout` - User logout

### Listings
- `GET /api/listings` - List approved listings with filtering
- `POST /api/listings` - Create listing (pending approval)
- `GET /api/listings/{listing}` - Get listing details
- `PUT /api/listings/{listing}` - Update listing
- `DELETE /api/listings/{listing}` - Delete listing (soft delete)

### Admin - Listing Management
- `GET /api/admin/listings/pending` - List pending approvals
- `POST /api/admin/listings/{listing}/approve` - Approve listing
- `POST /api/admin/listings/{listing}/reject` - Reject listing
- `GET /api/admin/listings` - List all listings

### Categories
- `GET /api/categories` - List categories
- `GET /api/categories/{category}/subcategories` - Get subcategories
- `POST /api/categories` - Create category (admin)
- `PUT /api/categories/{category}` - Update category (admin)
- `DELETE /api/categories/{category}` - Delete category (admin)

### Locations
- `GET /api/cities` - List cities
- `GET /api/cities/{city}/districts` - Get districts by city

### User Profile
- `GET /api/profile` - Get user profile
- `PUT /api/profile` - Update profile

### Health Check
- `GET /api/health` - Application health status

## 🐳 Docker Environment

### Container Services
- **trampala_laravel** - Laravel API backend (Port: 8080)
- **trampala_nuxt** - Nuxt.js frontend (Port: 3000)
- **trampala_postgres** - PostgreSQL database (Port: 5432)
- **trampala_redis** - Redis cache
- **trampala_mailhog** - Email testing (Port: 8025)

### Development Commands
```bash
# Start all services
./start.sh

# Stop all services
./stop.sh

# Stop and clean caches
./stop.sh --clean-cache

# Stop and clean all data
./stop.sh --clean-all

# Access containers
docker exec -it trampala_laravel bash    # Laravel backend
docker exec -it trampala_nuxt sh         # Nuxt.js frontend

# Database access
docker exec -it trampala_postgres psql -U postgres -d trampala

# View logs
docker logs trampala_laravel             # Backend logs
docker logs trampala_nuxt               # Frontend logs
```

## 🧪 Testing

The project maintains comprehensive test coverage with unit and feature tests.

### Run Tests
```bash
# All tests with coverage
docker exec -it trampala_laravel php artisan test --coverage

# Unit tests only
docker exec -it trampala_laravel php artisan test tests/Unit

# Feature tests only
docker exec -it trampala_laravel php artisan test tests/Feature

# Specific test file
docker exec -it trampala_laravel php artisan test tests/Unit/Models/Listing/ListingTest.php
```

### Test Structure
- **Unit Tests** - Models, Services, Repositories, Observers
- **Feature Tests** - API endpoints, authentication flows
- **Base Test Classes** - Shared testing utilities and traits

## 🛡️ Security Features

- **Laravel Sanctum** - Secure API token authentication
- **CORS Middleware** - Cross-origin request handling
- **Trusted Proxies** - Secure proxy configuration
- **Policy Authorization** - Resource-based access control
- **Input Validation** - Comprehensive request validation
- **File Upload Security** - Image validation and processing
- **Soft Delete** - Data integrity protection

## 📊 Database Design

### Core Entities
- **Users** - Authentication and profiles
- **Listings** - Main listing entities with approval status
- **Categories** - Hierarchical category system
- **Cities & Districts** - Location management
- **Listing Images** - Image attachments for listings

### Key Features
- UUID primary keys for enhanced security
- Soft deletes for data integrity
- Comprehensive indexing for performance
- Foreign key constraints for referential integrity
- Status-based workflow (pending, approved, rejected)

## 🔧 Application Access

### Web Application
- **Frontend**: http://localhost:3000 - Nuxt.js listing interface
- **Backend API**: http://localhost:8080 - Laravel API endpoints
- **Email Testing**: http://localhost:8025 - Mailhog interface

### API Response Format

#### Success Response
```json
{
  "success": true,
  "data": {...},
  "message": "Operation successful",
  "meta": {
    "total": 100,
    "per_page": 15,
    "current_page": 1
  }
}
```

#### Error Response
```json
{
  "success": false,
  "error": {
    "code": "VALIDATION_ERROR",
    "message": "Invalid input data",
    "details": {...}
  }
}
```

### Frontend Services Architecture
```bash
# Services (Axios-based)
services/
├── auth.service.ts      # Authentication operations
├── listing.service.ts   # Listing CRUD operations
├── user.service.ts      # User profile management
└── api.service.ts       # Base API configuration

# Stores (Pinia)
stores/
├── auth.store.ts        # Authentication state
├── listing.store.ts     # Listing management state
└── category.store.ts    # Category data state
```

## 📈 Performance

### Optimization Features

#### Backend (Laravel)
- **Database indexing** on frequently queried columns
- **Eager loading** to prevent N+1 queries
- **Redis caching** for session and cache management
- **Query optimization** with proper relationship loading
- **API resource transformation** for efficient data serialization
- **Image optimization** and storage management

#### Frontend (Nuxt.js)
- **Server-side rendering (SSR)** for improved SEO and performance
- **Static site generation (SSG)** for faster page loads
- **Image optimization** with Nuxt Image module
- **Code splitting** for reduced bundle sizes
- **TypeScript** for type safety and better development experience
- **Pinia** for efficient state management

## 🎯 Listing Requirements

### Mandatory Fields
- **Listing Title** *(string, required, max:255)*
- **Unique Listing Number** *(auto-generated, UUID)*
- **Category** *(required, foreign_key)*
- **Subcategory** *(required, foreign_key)*
- **Description** *(text, required)*
- **City** *(required, foreign_key)*
- **District** *(required, foreign_key)*
- **Image** *(required, image, max:5MB)*

### System Fields
- **Status** *(enum: pending, approved, rejected)*
- **Created At** *(timestamp)*
- **Updated At** *(timestamp)*
- **Deleted At** *(soft delete timestamp)*

## 🔄 Approval Workflow

1. **User Creates Listing** - All required fields must be filled
2. **Pending Review** - Listing enters admin approval queue
3. **Admin Review** - Admin can approve or reject with comments
4. **Approved Listings** - Display on main page and search results
5. **Rejected Listings** - User can edit and resubmit

## 🎨 Frontend Features

### User Interface
- **Responsive Design** - Mobile-first approach
- **Dark/Light Theme** - Theme toggle support
- **Loading States** - Skeleton screens and progress indicators
- **Form Validation** - Real-time validation feedback
- **Image Upload** - Drag & drop file upload with preview

### State Management (Pinia)
```typescript
// Listing Store Usage
const listingStore = useListingStore()
await listingStore.fetchListings()
await listingStore.createListing(formData)

// Auth Store Usage  
const authStore = useAuthStore()
await authStore.login(credentials)
```

## 📝 Development Guidelines

### Backend Standards
- Request/Response classes for all endpoints
- Service layer for business logic
- Repository pattern for data access
- Soft delete implementation
- Comprehensive exception handling

### Frontend Standards
- Axios service layer architecture
- Pinia for state management
- Composables for reusable logic
- TypeScript for type safety
- Component-based architecture

## 📝 License

This project is developed as a full-stack listing management platform.

---