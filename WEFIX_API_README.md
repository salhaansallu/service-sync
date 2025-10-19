# WeFix.lk API - Laravel Implementation

## Overview
Complete REST API implementation for WeFix.lk TV repair and e-commerce platform built with Laravel 10, Sanctum JWT authentication, and MySQL database.

## Technology Stack
- **Framework**: Laravel 10
- **Authentication**: Laravel Sanctum (JWT tokens)
- **Database**: MySQL
- **PHP**: 8.1+

## Installation & Setup

### 1. Install Dependencies
```bash
composer install
```

### 2. Environment Configuration
```bash
cp .env.example .env
php artisan key:generate
```

### 3. Configure Database
Edit `.env` file:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=wefix_db
DB_USERNAME=root
DB_PASSWORD=your_password
```

### 4. Run Migrations
```bash
php artisan migrate
```

### 5. Seed Database (Optional)
```bash
php artisan db:seed --class=WefixSeeder
```

This creates:
- Admin user: `wefixtvrepair@gmail.com` / `admin123`
- Test customer: `customer@example.com` / `customer123`
- Sample warranty records

### 6. Start Server
```bash
php artisan serve
```

API will be available at: `http://localhost:8000/api`

## API Documentation

### Base URL
```
Local: http://localhost:8000/api
Production: https://your-domain.com/api
```

### Authentication

All protected endpoints require Bearer token in header:
```
Authorization: Bearer {token}
```

#### Register
```http
POST /api/auth/register
Content-Type: application/json

{
  "name": "John Doe",
  "email": "john@example.com",
  "phone": "+94771234567",
  "password": "password123"
}
```

#### Login
```http
POST /api/auth/login
Content-Type: application/json

{
  "email": "john@example.com",
  "password": "password123"
}
```

**Response:**
```json
{
  "success": true,
  "message": "Login successful",
  "data": {
    "user": {
      "id": "user_1",
      "name": "John Doe",
      "email": "john@example.com",
      "phone": "+94771234567",
      "role": "customer",
      "phoneVerified": false
    },
    "token": "1|abc123..."
  }
}
```

## API Endpoints Summary

### Authentication
- `POST /api/auth/register` - Register new user
- `POST /api/auth/login` - Login user
- `POST /api/auth/logout` - Logout (Protected)
- `POST /api/auth/forgot-password` - Request password reset
- `POST /api/auth/reset-password` - Reset password

### User Profile
- `GET /api/users/profile` - Get user profile (Protected)
- `PUT /api/users/profile` - Update profile (Protected)
- `PUT /api/users/change-password` - Change password (Protected)

### Bookings
- `POST /api/bookings` - Create booking (Protected)
- `GET /api/bookings/user` - Get user bookings (Protected)
- `GET /api/bookings/{bookingId}` - Get booking details (Protected)
- `PUT /api/bookings/{bookingId}/cancel` - Cancel booking (Protected)
- `DELETE /api/bookings/{bookingId}` - Delete booking (Protected)

### Products
- `GET /api/products` - List all products (Public)
- `GET /api/products/{productId}` - Get product details (Public)

### Cart
- `GET /api/cart` - Get user cart (Protected)
- `POST /api/cart/items` - Add item to cart (Protected)
- `PUT /api/cart/items/{itemId}` - Update cart item (Protected)
- `DELETE /api/cart/items/{itemId}` - Remove cart item (Protected)
- `DELETE /api/cart` - Clear cart (Protected)

### Addresses
- `GET /api/addresses` - Get user addresses (Protected)
- `POST /api/addresses` - Add address (Protected)
- `PUT /api/addresses/{addressId}` - Update address (Protected)
- `DELETE /api/addresses/{addressId}` - Delete address (Protected)
- `PUT /api/addresses/{addressId}/default` - Set default address (Protected)

### Notifications
- `GET /api/notifications` - Get user notifications (Protected)
- `PUT /api/notifications/{id}/read` - Mark as read (Protected)
- `PUT /api/notifications/read-all` - Mark all as read (Protected)

### OTP Verification
- `POST /api/otp/send` - Send OTP (Protected)
- `POST /api/otp/verify` - Verify OTP (Protected)

### Warranty
- `POST /api/warranty/check` - Check warranty status (Public)

### Admin Endpoints (Require Admin Role)
- `GET /api/admin/bookings` - List all bookings
- `PUT /api/admin/bookings/{id}/status` - Update booking status
- `GET /api/admin/service-requests` - List service requests
- `PUT /api/admin/service-requests/{id}` - Update service request
- `POST /api/admin/notifications` - Send notification

## Database Schema

### New Tables Created
1. **bookings** - TV repair bookings with timeline tracking
2. **addresses** - User saved addresses
3. **carts** - Shopping cart
4. **cart_items** - Cart items
5. **notifications** - User notifications
6. **service_requests** - Service requests (web design, SEO, etc.)
7. **warranty_records** - Warranty tracking
8. **otp_verifications** - OTP verification records

### Updated Tables
- **users** - Added: phone, role, phone_verified, notification_preferences

### Existing Tables (Integrated)
- **products** - TV parts and accessories
- **customers** - Customer records
- **repairs** - Repair jobs

## Testing

### Using cURL

**Register:**
```bash
curl -X POST http://localhost:8000/api/auth/register \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Test User",
    "email": "test@example.com",
    "phone": "+94771234567",
    "password": "password123"
  }'
```

**Login:**
```bash
curl -X POST http://localhost:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "test@example.com",
    "password": "password123"
  }'
```

**Get Profile (Replace TOKEN):**
```bash
curl -X GET http://localhost:8000/api/users/profile \
  -H "Authorization: Bearer YOUR_TOKEN_HERE"
```

**Create Booking:**
```bash
curl -X POST http://localhost:8000/api/bookings \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "Content-Type: application/json" \
  -d '{
    "tvBrand": "Samsung",
    "tvModel": "UA55AU7700",
    "issueType": "display-issue",
    "issueDescription": "Screen has vertical lines",
    "address": "123 Main St, Colombo",
    "phone": "+94771234567",
    "pickupOption": "pickup",
    "customerName": "Test User"
  }'
```

## Error Handling

All endpoints return consistent error responses:

```json
{
  "success": false,
  "message": "Error description",
  "error": "ERROR_CODE",
  "details": {}
}
```

### Common Error Codes
- `VALIDATION_ERROR` - Invalid input data
- `UNAUTHORIZED` - Authentication required
- `FORBIDDEN` - Insufficient permissions
- `NOT_FOUND` - Resource not found
- `DUPLICATE_EMAIL` - Email already exists
- `INVALID_CREDENTIALS` - Wrong email/password

## Security Features

1. **JWT Authentication** - Secure token-based authentication
2. **Role-Based Access Control** - Admin/Customer roles
3. **Password Hashing** - Bcrypt encryption
4. **OTP Verification** - Phone number verification
5. **Input Validation** - All inputs validated
6. **SQL Injection Prevention** - Eloquent ORM
7. **CORS Configuration** - Cross-origin resource sharing

## Integration with Existing System

The API seamlessly integrates with the existing POS system:

- Uses existing `products` table for e-commerce
- Can link `bookings` with existing `repairs` table
- Shares `customers` database
- Maintains existing POS routes

## Rate Limiting

Laravel's built-in rate limiting is configured:
- API routes: 60 requests per minute per IP
- Login attempts: 5 per minute

## Deployment

### Production Checklist

1. Set `APP_ENV=production` in `.env`
2. Run `php artisan config:cache`
3. Run `php artisan route:cache`
4. Enable HTTPS
5. Configure proper CORS settings
6. Set up database backups
7. Configure email service for OTP
8. Set up SMS service for OTP
9. Enable Laravel logging

## Support

For API support:
- Email: support@wefix.lk
- Phone: +94 77 330 0905
- WhatsApp: +94 77 330 0905

## License

Proprietary - WeFix.lk © 2025
