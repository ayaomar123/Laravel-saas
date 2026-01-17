# Laravel 11 Multi-Tenant Tasks API

A production-ready REST API for a multi-tenant SaaS "Tasks" application using Laravel 11 and Sanctum authentication.

## Features

- **Multi-Tenancy:** Single database with tenant_id isolation
- **Domain-based Tenant Resolution:** Automatic tenant detection from custom domains
- **SPA Authentication:** Laravel Sanctum with HttpOnly cookies
- **Complete Tasks API:** Full CRUD operations with tenant isolation
- **Production Code Quality:** Clean architecture with proper abstractions

## Architecture

### Multi-Tenancy Model
- Single database architecture
- All tenant-owned tables include `tenant_id` column
- Global scope automatically filters by current tenant
- Tenant resolved from request hostname

### Data Isolation
- `BaseTenantModel` extends Laravel's Eloquent Model
- Global scope automatically applies `where tenant_id = currentTenantId`
- Automatic tenant_id assignment on create
- API endpoints return 404 for cross-tenant access attempts

## Installation

### Prerequisites
- PHP 8.2+
- Composer
- MySQL/PostgreSQL/SQLite
- Web server (Apache/Nginx)

### Steps

1. Clone the repository:
```bash
git clone <repository-url>
cd laravel-11-multitenant-tasks-api
```

2. Install dependencies:
```bash
composer install
```

3. Configure environment:
```bash
cp .env.example .env
php artisan key:generate
```

4. Configure your database in `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=multitenant_tasks
DB_USERNAME=root
DB_PASSWORD=
```

5. Run migrations and seeders:
```bash
php artisan migrate
php artisan db:seed
```

6. Configure your web server or use Laravel's built-in server:
```bash
php artisan serve --host=0.0.0.0 --port=8000
```

## Local Development Setup

### Hosts File Configuration

Add these entries to your `/etc/hosts` file (Linux/macOS) or `C:\Windows\System32\drivers\etc\hosts` (Windows):

```
127.0.0.1 acme.test
127.0.0.1 beta.test
```

### Sanctum Configuration

For SPA authentication with HttpOnly cookies, configure your frontend:

1. Ensure your frontend domain is listed in `config/sanctum.php` stateful domains
2. Include credentials in API calls:

```javascript
// Example Axios configuration
axios.defaults.withCredentials = true;
axios.defaults.baseURL = 'http://acme.test:8000/api';

// First, get CSRF cookie
await axios.get('/sanctum/csrf-cookie');

// Then make authenticated requests
const response = await axios.post('/auth/login', {
    email: 'user@example.com',
    password: 'password'
});
```

## API Endpoints

### Authentication Endpoints (Tenant-aware)

All auth endpoints are scoped to the current tenant resolved from domain.

#### Register
```http
POST /api/auth/register
Content-Type: application/json

{
    "name": "John Doe",
    "email": "john@example.com",
    "password": "password",
    "password_confirmation": "password"
}
```

#### Login
```http
POST /api/auth/login
Content-Type: application/json

{
    "email": "john@example.com",
    "password": "password"
}
```

#### Logout
```http
POST /api/auth/logout
Authorization: Bearer {token}
```

#### Get Current User
```http
GET /api/auth/me
Authorization: Bearer {token}
```

### Tasks Endpoints (Tenant-scoped)

All task operations are automatically scoped to the current tenant.

#### List Tasks (Paginated)
```http
GET /api/tasks
Authorization: Bearer {token}
```

#### Create Task
```http
POST /api/tasks
Authorization: Bearer {token}
Content-Type: application/json

{
    "title": "Complete project documentation"
}
```

#### Get Task
```http
GET /api/tasks/{id}
Authorization: Bearer {token}
```

#### Update Task
```http
PUT /api/tasks/{id}
Authorization: Bearer {token}
Content-Type: application/json

{
    "title": "Updated task title",
    "completed": true
}
```

#### Delete Task
```http
DELETE /api/tasks/{id}
Authorization: Bearer {token}
```

## Code Structure

### Models
- `App\Models\Tenant` - Tenant entity
- `App\Models\TenantDomain` - Domain mapping for tenants
- `App\Models\User` - User model with tenant awareness
- `App\Models\Task` - Task model extending BaseTenantModel
- `App\Models\BaseTenantModel` - Base model with tenant scoping

### Middleware
- `App\Http\Middleware\TenantResolver` - Resolves tenant from domain

### Global Scope
- `App\Scopes\TenantScope` - Automatically filters all queries by tenant_id

### Requests
- `App\Http\Requests\LoginRequest` - Login validation
- `App\Http\Requests\RegisterRequest` - Registration validation
- `App\Http\Requests\TaskRequest` - Task CRUD validation

### Controllers
- `App\Http\Controllers\AuthController` - Authentication operations
- `App\Http\Controllers\TaskController` - Task CRUD operations

## Security Features

- **Domain Validation:** Returns 404 for unregistered domains
- **Tenant Isolation:** Global scope prevents cross-tenant data access
- **Token Authentication:** Sanctum with HttpOnly cookies
- **Request Validation:** FormRequest validation for all endpoints
- **Automatic Tenant Scoping:** No manual tenant_id filtering required

## Testing

### Manual Testing with cURL

1. **Test domain resolution:**
```bash
# Should return "Domain not configured"
curl -H "Host: unknown.test" http://localhost:8000/api/tasks

# Should work with seeded domains
curl http://acme.test:8000/api/auth/register \
  -H "Content-Type: application/json" \
  -d '{"name":"Test User","email":"test@acme.com","password":"password","password_confirmation":"password"}'
```

2. **Test tenant isolation:**
```bash
# Register on acme.test
curl http://acme.test:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email":"test@acme.com","password":"password"}'

# Same credentials should fail on beta.test
curl http://beta.test:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email":"test@acme.com","password":"password"}'
```

### Running Tests
```bash
php artisan test
```

## Production Deployment

### Environment Variables
Ensure these are properly configured in production:

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com

# Sanctum Configuration
SANCTUM_STATEFUL_DOMAINS=your-frontend-domain.com
SESSION_DOMAIN=.your-domain.com

# Database
DB_CONNECTION=mysql
DB_HOST=your-db-host
DB_DATABASE=your-database
DB_USERNAME=your-username
DB_PASSWORD=your-password
```

### Web Server Configuration

Example Nginx configuration:

```nginx
server {
    listen 80;
    server_name acme.test beta.test;
    root /path/to/public;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
```

## Troubleshooting

### Common Issues

1. **"Domain not configured" error**
   - Check hosts file entries
   - Verify domains are seeded in database
   - Ensure domain is verified (verified_at is not null)

2. **CORS issues**
   - Configure `config/cors.php` with your frontend domain
   - Ensure `supports_credentials` is true for Sanctum

3. **Tenant isolation not working**
   - Verify models extend `BaseTenantModel`
   - Check that global scope is applied
   - Ensure tenant is resolved in middleware

## License

MIT License