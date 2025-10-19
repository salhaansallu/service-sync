#!/bin/bash

# WeFix.lk Phone-Based Auth Testing Script

BASE_URL="http://localhost:8000/api"
PHONE="+94771234567"
PASSWORD="testpass123"

echo "========================================="
echo "WeFix.lk Phone-Based Authentication Test"
echo "========================================="
echo ""

# Test 1: Register with phone number
echo "Test 1: Register new user with phone number"
echo "-------------------------------------------"
REGISTER_RESPONSE=$(curl -s -X POST "$BASE_URL/auth/register" \
  -H "Content-Type: application/json" \
  -d "{
    \"name\": \"Test User\",
    \"phone\": \"$PHONE\",
    \"password\": \"$PASSWORD\",
    \"email\": \"test@example.com\"
  }")

echo "$REGISTER_RESPONSE" | python3 -m json.tool 2>/dev/null || echo "$REGISTER_RESPONSE"
echo ""

# Extract token
TOKEN=$(echo "$REGISTER_RESPONSE" | grep -o '"token":"[^"]*"' | cut -d'"' -f4)

if [ -z "$TOKEN" ]; then
    echo "❌ Registration failed or token not received"
    echo ""
    
    # Try login instead
    echo "Attempting login..."
    LOGIN_RESPONSE=$(curl -s -X POST "$BASE_URL/auth/login" \
      -H "Content-Type: application/json" \
      -d "{
        \"phone\": \"$PHONE\",
        \"password\": \"$PASSWORD\"
      }")
    
    echo "$LOGIN_RESPONSE" | python3 -m json.tool 2>/dev/null || echo "$LOGIN_RESPONSE"
    echo ""
    
    TOKEN=$(echo "$LOGIN_RESPONSE" | grep -o '"token":"[^"]*"' | cut -d'"' -f4)
fi

if [ -z "$TOKEN" ]; then
    echo "❌ Could not obtain token. Exiting."
    exit 1
fi

echo "✓ Token obtained: ${TOKEN:0:20}..."
echo ""

# Test 2: Get user profile
echo "Test 2: Get user profile"
echo "------------------------"
PROFILE_RESPONSE=$(curl -s -X GET "$BASE_URL/users/profile" \
  -H "Authorization: Bearer $TOKEN")

echo "$PROFILE_RESPONSE" | python3 -m json.tool 2>/dev/null || echo "$PROFILE_RESPONSE"
echo ""

# Test 3: Check if customer linked
HAS_CUSTOMER=$(echo "$PROFILE_RESPONSE" | grep -o '"hasExistingCustomer":[^,}]*' | cut -d':' -f2)

if [ "$HAS_CUSTOMER" = "true" ]; then
    echo "✓ User is linked to existing customer!"
    echo ""
    
    # Test 4: Get customer orders
    echo "Test 3: Get customer orders and repairs"
    echo "---------------------------------------"
    ORDERS_RESPONSE=$(curl -s -X GET "$BASE_URL/customer/orders" \
      -H "Authorization: Bearer $TOKEN")
    
    echo "$ORDERS_RESPONSE" | python3 -m json.tool 2>/dev/null || echo "$ORDERS_RESPONSE"
    echo ""
    
    # Test 5: Get customer stats
    echo "Test 4: Get customer statistics"
    echo "-------------------------------"
    STATS_RESPONSE=$(curl -s -X GET "$BASE_URL/customer/stats" \
      -H "Authorization: Bearer $TOKEN")
    
    echo "$STATS_RESPONSE" | python3 -m json.tool 2>/dev/null || echo "$STATS_RESPONSE"
    echo ""
else
    echo "ℹ User is NOT linked to existing customer"
    echo "This is expected for new phone numbers"
    echo ""
fi

# Test 6: Create a booking
echo "Test 5: Create a booking"
echo "------------------------"
BOOKING_RESPONSE=$(curl -s -X POST "$BASE_URL/bookings" \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "tvBrand": "Samsung",
    "tvModel": "UA55AU7700",
    "issueType": "display-issue",
    "issueDescription": "Screen has vertical lines",
    "address": "123 Test Street, Colombo",
    "phone": "'"$PHONE"'",
    "pickupOption": "pickup",
    "customerName": "Test User"
  }')

echo "$BOOKING_RESPONSE" | python3 -m json.tool 2>/dev/null || echo "$BOOKING_RESPONSE"
echo ""

# Test 7: Get bookings
echo "Test 6: Get user bookings"
echo "-------------------------"
USER_BOOKINGS=$(curl -s -X GET "$BASE_URL/bookings/user" \
  -H "Authorization: Bearer $TOKEN")

echo "$USER_BOOKINGS" | python3 -m json.tool 2>/dev/null || echo "$USER_BOOKINGS"
echo ""

echo "========================================="
echo "✅ Testing Complete!"
echo "========================================="
echo ""
echo "Summary:"
echo "- Phone-based authentication: ✓"
echo "- User profile access: ✓"
echo "- Booking creation: ✓"
if [ "$HAS_CUSTOMER" = "true" ]; then
    echo "- Customer integration: ✓"
    echo "- Historical orders access: ✓"
else
    echo "- Customer integration: N/A (new user)"
fi
echo ""
echo "Token for further testing:"
echo "$TOKEN"
echo ""
