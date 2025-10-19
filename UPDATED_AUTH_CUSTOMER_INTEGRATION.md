# WeFix.lk API - Updated Authentication & Customer Integration

## Important Changes

### 🔑 Phone-Based Authentication
- **Registration and Login now use PHONE NUMBER instead of email**
- Email is optional during registration
- Phone numbers are unique identifiers

### 🔗 Customer Integration
- When a user registers with a phone number, the system checks if a customer exists with that phone in the `customers` table
- If found, the user is automatically linked to that customer
- All orders and repairs under that customer can be fetched via API

---

## Updated API Endpoints

### 1. Register (Phone-Based)

```bash
curl -X POST http://localhost:8000/api/auth/register \
  -H "Content-Type: application/json" \
  -d '{
    "name": "John Doe",
    "phone": "+94771234567",
    "password": "password123",
    "email": "john@example.com"
  }'
```

**Request Body:**
- `name` - Required, customer name
- `phone` - Required, unique phone number (used for login)
- `password` - Required, min 8 characters
- `email` - Optional

**Response:**
```json
{
  "success": true,
  "message": "User registered successfully",
  "data": {
    "user": {
      "id": "user_1",
      "name": "John Doe",
      "phone": "+94771234567",
      "email": "john@example.com",
      "role": "customer",
      "phoneVerified": false,
      "hasExistingCustomer": true,
      "customerId": 123
    },
    "token": "1|abc123..."
  }
}
```

**Key Fields:**
- `hasExistingCustomer` - `true` if linked to existing customer in database
- `customerId` - ID of linked customer (can be used to fetch orders)

---

### 2. Login (Phone-Based)

```bash
curl -X POST http://localhost:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "phone": "+94771234567",
    "password": "password123"
  }'
```

**Request Body:**
- `phone` - Required, phone number
- `password` - Required

**Response:**
```json
{
  "success": true,
  "message": "Login successful",
  "data": {
    "user": {
      "id": "user_1",
      "name": "John Doe",
      "phone": "+94771234567",
      "email": "john@example.com",
      "role": "customer",
      "phoneVerified": true,
      "hasExistingCustomer": true,
      "customerId": 123
    },
    "token": "1|abc123..."
  }
}
```

---

## New Customer Orders & Repairs Endpoints

### 1. Get Customer Orders & Repairs

Fetches all orders and repairs for the authenticated user's linked customer.

```bash
TOKEN="your_token_here"

curl -X GET http://localhost:8000/api/customer/orders \
  -H "Authorization: Bearer $TOKEN"
```

**Query Parameters:**
- `page` - Page number (default: 1)
- `limit` - Items per page (default: 20)
- `type` - Filter by type: `orders` or `repairs` (default: both)

**Response:**
```json
{
  "success": true,
  "data": {
    "customer": {
      "id": 123,
      "name": "John Doe",
      "phone": "+94771234567",
      "email": "john@example.com",
      "address": "No. 123, Main Street, Colombo 03",
      "storeCredit": 5000.00
    },
    "orders": {
      "items": [
        {
          "id": 1,
          "orderNumber": "ORD-001",
          "date": "2025-01-15T10:30:00Z",
          "status": "completed",
          "total": 15000.00
        }
      ],
      "pagination": {
        "total": 10,
        "page": 1,
        "limit": 20,
        "totalPages": 1
      }
    },
    "repairs": {
      "items": [
        {
          "id": 1,
          "billNo": "REP-001",
          "modelNo": "UA55AU7700",
          "serialNo": "SN123456",
          "fault": "Display issue",
          "status": "Completed",
          "total": 25000.00,
          "advance": 10000.00,
          "createdAt": "2025-01-10T08:00:00Z"
        }
      ],
      "pagination": {
        "total": 5,
        "page": 1,
        "limit": 20,
        "totalPages": 1
      }
    }
  }
}
```

**Filter Examples:**

Get only orders:
```bash
curl -X GET "http://localhost:8000/api/customer/orders?type=orders" \
  -H "Authorization: Bearer $TOKEN"
```

Get only repairs:
```bash
curl -X GET "http://localhost:8000/api/customer/orders?type=repairs" \
  -H "Authorization: Bearer $TOKEN"
```

---

### 2. Get Specific Order

```bash
curl -X GET http://localhost:8000/api/customer/orders/1 \
  -H "Authorization: Bearer $TOKEN"
```

**Response:**
```json
{
  "success": true,
  "data": {
    "id": 1,
    "orderNumber": "ORD-001",
    "date": "2025-01-15T10:30:00Z",
    "status": "completed",
    "total": 15000.00,
    "items": [
      {
        "productId": 1,
        "name": "Samsung Panel",
        "quantity": 1,
        "price": 15000.00
      }
    ],
    "paymentMethod": "cash",
    "notes": "Delivered successfully"
  }
}
```

---

### 3. Get Specific Repair

```bash
curl -X GET http://localhost:8000/api/customer/repairs/1 \
  -H "Authorization: Bearer $TOKEN"
```

**Response:**
```json
{
  "success": true,
  "data": {
    "id": 1,
    "billNo": "REP-001",
    "modelNo": "UA55AU7700",
    "serialNo": "SN123456",
    "fault": "Display issue with vertical lines",
    "status": "Completed",
    "total": 25000.00,
    "advance": 10000.00,
    "cost": 20000.00,
    "delivery": 1000.00,
    "note": "Panel replaced, tested successfully",
    "technician": "Tech-01",
    "warranty": 180,
    "paidDate": "2025-01-15T14:00:00Z",
    "repairedDate": "2025-01-15T16:00:00Z",
    "hasMultipleFault": false,
    "spares": [
      {
        "name": "LED Panel",
        "quantity": 1,
        "price": 20000.00
      }
    ],
    "createdAt": "2025-01-10T08:00:00Z"
  }
}
```

---

### 4. Get Customer Statistics

```bash
curl -X GET http://localhost:8000/api/customer/stats \
  -H "Authorization: Bearer $TOKEN"
```

**Response:**
```json
{
  "success": true,
  "data": {
    "totalOrders": 10,
    "totalRepairs": 5,
    "pendingRepairs": 2,
    "completedRepairs": 3,
    "storeCredit": 5000.00
  }
}
```

---

## Registration Flow

### Scenario 1: New Customer (No Existing Record)

1. User registers with phone `+94771234567`
2. System checks `customers` table - no match found
3. User account created with `customer_id = null`
4. User receives token and can use booking/cart features

**Response:**
```json
{
  "hasExistingCustomer": false,
  "customerId": null
}
```

---

### Scenario 2: Existing Customer

1. User registers with phone `+94771234567`
2. System finds customer with matching phone in `customers` table
3. User account created with `customer_id = 123`
4. User can now access all historical orders/repairs via API

**Response:**
```json
{
  "hasExistingCustomer": true,
  "customerId": 123
}
```

---

## Error Handling

### No Customer Linked

If user tries to access orders without a linked customer:

```json
{
  "success": false,
  "message": "No customer account linked to this user",
  "error": "NO_CUSTOMER_LINKED"
}
```

### Duplicate Phone Number

If phone number already registered:

```json
{
  "success": false,
  "message": "Phone number already registered",
  "error": "DUPLICATE_PHONE"
}
```

---

## Testing Examples

### 1. Register with existing customer phone

```bash
# This phone exists in customers table
curl -X POST http://localhost:8000/api/auth/register \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Test Customer",
    "phone": "+94771234567",
    "password": "password123"
  }'
```

### 2. Login and get token

```bash
curl -X POST http://localhost:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "phone": "+94771234567",
    "password": "password123"
  }'
```

Save the token from response!

### 3. Fetch all orders and repairs

```bash
TOKEN="1|abc123..."

curl -X GET http://localhost:8000/api/customer/orders \
  -H "Authorization: Bearer $TOKEN"
```

### 4. Get customer stats

```bash
curl -X GET http://localhost:8000/api/customer/stats \
  -H "Authorization: Bearer $TOKEN"
```

---

## Database Schema Updates

### Users Table (Updated)

```sql
ALTER TABLE users 
ADD COLUMN phone VARCHAR(20) UNIQUE,
ADD COLUMN customer_id BIGINT UNSIGNED NULL,
ADD COLUMN role VARCHAR(20) DEFAULT 'customer',
ADD COLUMN phone_verified BOOLEAN DEFAULT FALSE,
ADD COLUMN notification_preferences JSON,
ADD FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE SET NULL;
```

### Relationship

```
users.customer_id → customers.id
```

---

## Benefits

1. **Seamless Integration**: Existing customers automatically linked on registration
2. **Unified History**: Access all past orders and repairs via API
3. **No Data Migration**: Uses existing customer data
4. **Store Credit**: Can view and use existing store credit
5. **Consistent Experience**: Web app and mobile app show same data

---

## Migration from Email to Phone

If you have existing users with emails, you can add phone numbers:

```sql
UPDATE users 
SET phone = '+94771234567' 
WHERE email = 'customer@example.com';
```

---

## Admin Features

Admins can still use email for login (backward compatible):
- Admin login: `wefixtvrepair@gmail.com` / `admin123`

Customers MUST use phone for login:
- Customer login: `+94771234567` / `password123`

---

## Next Steps

1. **SMS Integration**: Set up SMS service for OTP verification
2. **Link Existing Users**: Add phone numbers to existing user accounts
3. **Customer Sync**: Regularly sync customer data
4. **Mobile App**: Build mobile app using these endpoints

---

## Support

For integration support:
- Email: support@wefix.lk
- Phone: +94 77 330 0905
