# WeFix.lk API Testing Guide

## Quick Start Testing

### Prerequisites
- API server running at `http://localhost:8000`
- cURL installed (or use Postman)

## Test Flow

### 1. Register a New User

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

**Expected Response:**
```json
{
  "success": true,
  "message": "User registered successfully",
  "data": {
    "user": {
      "id": "user_1",
      "name": "Test User",
      "email": "test@example.com",
      "phone": "+94771234567",
      "role": "customer",
      "phoneVerified": false
    },
    "token": "1|abcd1234..."
  }
}
```

**Save the token for next requests!**

---

### 2. Login (Alternative to Register)

```bash
curl -X POST http://localhost:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "customer@example.com",
    "password": "customer123"
  }'
```

---

### 3. Get User Profile

```bash
TOKEN="YOUR_TOKEN_HERE"

curl -X GET http://localhost:8000/api/users/profile \
  -H "Authorization: Bearer $TOKEN"
```

---

### 4. Create a Booking

```bash
curl -X POST http://localhost:8000/api/bookings \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "tvBrand": "Samsung",
    "tvModel": "UA55AU7700",
    "issueType": "display-issue",
    "issueDescription": "Screen has vertical lines and flickering",
    "address": "No. 123, Main Street, Colombo 03, Sri Lanka",
    "phone": "+94771234567",
    "pickupOption": "pickup",
    "customerName": "Test User"
  }'
```

**Expected Response:**
```json
{
  "success": true,
  "message": "Booking created successfully",
  "data": {
    "id": "booking_1234567890abcdef",
    "userId": "user_1",
    "customerName": "Test User",
    ...
  }
}
```

**Save the booking ID!**

---

### 5. Get User Bookings

```bash
curl -X GET "http://localhost:8000/api/bookings/user?page=1&limit=10" \
  -H "Authorization: Bearer $TOKEN"
```

Filter by status:
```bash
curl -X GET "http://localhost:8000/api/bookings/user?status=pending" \
  -H "Authorization: Bearer $TOKEN"
```

---

### 6. Get Specific Booking

```bash
BOOKING_ID="booking_1234567890abcdef"

curl -X GET http://localhost:8000/api/bookings/$BOOKING_ID \
  -H "Authorization: Bearer $TOKEN"
```

---

### 7. Get Products (Public - No Auth Required)

```bash
curl -X GET "http://localhost:8000/api/products?page=1&limit=20"
```

Search products:
```bash
curl -X GET "http://localhost:8000/api/products?search=samsung"
```

Filter by stock:
```bash
curl -X GET "http://localhost:8000/api/products?inStock=true"
```

---

### 8. Add Product to Cart

```bash
curl -X POST http://localhost:8000/api/cart/items \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "productId": 1,
    "quantity": 2
  }'
```

---

### 9. Get Cart

```bash
curl -X GET http://localhost:8000/api/cart \
  -H "Authorization: Bearer $TOKEN"
```

**Response includes cart summary:**
```json
{
  "success": true,
  "data": {
    "items": [...],
    "summary": {
      "itemCount": 2,
      "subtotal": 90000,
      "tax": 0,
      "total": 90000
    }
  }
}
```

---

### 10. Add Address

```bash
curl -X POST http://localhost:8000/api/addresses \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "label": "Home",
    "address": "No. 123, Main Street, Colombo 03, Sri Lanka",
    "isDefault": true
  }'
```

---

### 11. Check Warranty (Public)

```bash
curl -X POST http://localhost:8000/api/warranty/check \
  -H "Content-Type: application/json" \
  -d '{
    "serialNumber": "SN123456789",
    "billNumber": "BILL-2024-001",
    "phoneNumber": "+94771234567"
  }'
```

**Valid Warranty Response:**
```json
{
  "success": true,
  "data": {
    "isValid": true,
    "product": "Samsung 55\" LED TV",
    "purchaseDate": "2024-06-15",
    "expiryDate": "2025-06-15",
    "daysRemaining": 180,
    "coverageType": "Full Warranty",
    "notes": "Covers parts and labor"
  }
}
```

---

### 12. Send OTP

```bash
curl -X POST http://localhost:8000/api/otp/send \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "phone": "+94771234567"
  }'
```

**Response includes OTP in dev mode:**
```json
{
  "success": true,
  "message": "OTP sent successfully",
  "data": {
    "phone": "+94771234567",
    "expiresIn": 300
  },
  "dev_otp": "123456"
}
```

---

### 13. Verify OTP

```bash
curl -X POST http://localhost:8000/api/otp/verify \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "phone": "+94771234567",
    "otp": "123456"
  }'
```

---

### 14. Get Notifications

```bash
curl -X GET http://localhost:8000/api/notifications \
  -H "Authorization: Bearer $TOKEN"
```

Get only unread:
```bash
curl -X GET "http://localhost:8000/api/notifications?unread=true" \
  -H "Authorization: Bearer $TOKEN"
```

---

### 15. Mark Notification as Read

```bash
NOTIFICATION_ID="1"

curl -X PUT http://localhost:8000/api/notifications/$NOTIFICATION_ID/read \
  -H "Authorization: Bearer $TOKEN"
```

---

### 16. Cancel Booking

```bash
curl -X PUT http://localhost:8000/api/bookings/$BOOKING_ID/cancel \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "reason": "Changed my mind, will repair later"
  }'
```

---

## Admin Operations

### 1. Login as Admin

```bash
curl -X POST http://localhost:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "wefixtvrepair@gmail.com",
    "password": "admin123"
  }'
```

**Save the admin token!**

---

### 2. Get All Bookings (Admin)

```bash
ADMIN_TOKEN="YOUR_ADMIN_TOKEN"

curl -X GET "http://localhost:8000/api/admin/bookings?page=1&limit=20" \
  -H "Authorization: Bearer $ADMIN_TOKEN"
```

Filter by status:
```bash
curl -X GET "http://localhost:8000/api/admin/bookings?status=pending" \
  -H "Authorization: Bearer $ADMIN_TOKEN"
```

Filter by date range:
```bash
curl -X GET "http://localhost:8000/api/admin/bookings?startDate=2025-01-01&endDate=2025-12-31" \
  -H "Authorization: Bearer $ADMIN_TOKEN"
```

---

### 3. Update Booking Status (Admin)

```bash
curl -X PUT http://localhost:8000/api/admin/bookings/$BOOKING_ID/status \
  -H "Authorization: Bearer $ADMIN_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "status": "in-progress",
    "note": "Repair work started, panel replacement in progress"
  }'
```

**Available Statuses:**
- `pending`
- `confirmed`
- `parts-ordered`
- `in-progress`
- `testing`
- `ready`
- `completed`
- `cancelled`

---

### 4. Get Service Requests (Admin)

```bash
curl -X GET http://localhost:8000/api/admin/service-requests \
  -H "Authorization: Bearer $ADMIN_TOKEN"
```

---

### 5. Update Service Request (Admin)

```bash
curl -X PUT http://localhost:8000/api/admin/service-requests/1 \
  -H "Authorization: Bearer $ADMIN_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "status": "in-progress",
    "notes": "Started working on the design mockups"
  }'
```

---

### 6. Send Notification to User (Admin)

```bash
curl -X POST http://localhost:8000/api/admin/notifications \
  -H "Authorization: Bearer $ADMIN_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "userId": 1,
    "title": "Repair Status Update",
    "message": "Your TV repair is now in testing phase",
    "type": "booking_update",
    "data": {
      "bookingId": "booking_123",
      "status": "testing"
    }
  }'
```

---

## Testing Error Cases

### 1. Invalid Credentials

```bash
curl -X POST http://localhost:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "wrong@example.com",
    "password": "wrongpassword"
  }'
```

**Expected:**
```json
{
  "success": false,
  "message": "Invalid email or password",
  "error": "INVALID_CREDENTIALS"
}
```

---

### 2. Unauthorized Access

```bash
curl -X GET http://localhost:8000/api/users/profile
# Missing Authorization header
```

**Expected:**
```json
{
  "success": false,
  "message": "Authentication required",
  "error": "UNAUTHORIZED"
}
```

---

### 3. Forbidden Access (Customer trying admin endpoint)

```bash
curl -X GET http://localhost:8000/api/admin/bookings \
  -H "Authorization: Bearer $CUSTOMER_TOKEN"
```

**Expected:**
```json
{
  "success": false,
  "message": "Access denied. Admin privileges required",
  "error": "FORBIDDEN"
}
```

---

### 4. Validation Error

```bash
curl -X POST http://localhost:8000/api/auth/register \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Test",
    "email": "invalid-email"
  }'
```

**Expected:**
```json
{
  "success": false,
  "message": "Validation error",
  "error": "VALIDATION_ERROR",
  "details": {
    "email": ["The email must be a valid email address."],
    "password": ["The password field is required."]
  }
}
```

---

### 5. Resource Not Found

```bash
curl -X GET http://localhost:8000/api/bookings/invalid_booking_id \
  -H "Authorization: Bearer $TOKEN"
```

**Expected:**
```json
{
  "success": false,
  "message": "Booking not found",
  "error": "BOOKING_NOT_FOUND"
}
```

---

## Performance Testing

### Load Test with Apache Bench

```bash
# Test login endpoint (10 concurrent, 100 requests)
ab -n 100 -c 10 -p login.json -T application/json \
  http://localhost:8000/api/auth/login
```

### Stress Test Products Endpoint

```bash
# Test product listing (50 concurrent, 1000 requests)
ab -n 1000 -c 50 http://localhost:8000/api/products
```

---

## Database Verification

### Check Created Data

```sql
-- Check users
SELECT id, name, email, role, phone_verified FROM users;

-- Check bookings
SELECT booking_id, customer_name, tv_brand, status, created_at 
FROM bookings 
ORDER BY created_at DESC;

-- Check cart items
SELECT ci.id, ci.quantity, ci.price, p.pro_name 
FROM cart_items ci 
JOIN products p ON ci.product_id = p.id;

-- Check notifications
SELECT id, title, is_read, created_at 
FROM notifications 
WHERE user_id = 1 
ORDER BY created_at DESC;
```

---

## Common Issues & Solutions

### 1. "Unauthenticated" Error
**Solution:** Ensure token is valid and not expired. Login again to get a new token.

### 2. "CSRF token mismatch"
**Solution:** This shouldn't happen with API routes. Ensure you're using `/api/` prefix.

### 3. Database connection error
**Solution:** Check `.env` database credentials and ensure MySQL is running.

### 4. "Class not found" errors
**Solution:** Run `composer dump-autoload`

---

## Next Steps

1. **Test all endpoints** using the examples above
2. **Import Postman collection** for easier testing
3. **Check logs** at `storage/logs/laravel.log` for errors
4. **Set up SMS service** for production OTP
5. **Configure email** for password reset

---

## Support

If you encounter issues:
1. Check Laravel logs: `tail -f storage/logs/laravel.log`
2. Enable debug mode: Set `APP_DEBUG=true` in `.env`
3. Clear cache: `php artisan cache:clear`
4. Contact support: support@wefix.lk
