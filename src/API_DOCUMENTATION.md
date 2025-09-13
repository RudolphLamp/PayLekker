# PayLekker API Documentation

## Overview
The PayLekker API provides secure, RESTful endpoints for user authentication, money transfers, budgeting, and chatbot support. All endpoints return JSON responses.

## Base URL
```
https://pay.sewdani.co.za/api/
```

## Authentication
Most endpoints require JWT token authentication. Include the token in the Authorization header:
```
Authorization: Bearer <your-jwt-token>
```

## Endpoints

### Authentication

#### POST /auth/register
Register a new user account.

**Request Body:**
```json
{
    "email": "user@example.com",
    "password": "securepassword",
    "first_name": "John",
    "last_name": "Doe",
    "phone": "+27123456789"
}
```

**Response:**
```json
{
    "message": "User registered successfully",
    "user": {
        "id": 1,
        "email": "user@example.com",
        "first_name": "John",
        "last_name": "Doe",
        "phone": "+27123456789",
        "balance": 0.00
    },
    "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9..."
}
```

#### POST /auth/login
Login with existing account.

**Request Body:**
```json
{
    "email": "user@example.com",
    "password": "securepassword"
}
```

**Response:**
```json
{
    "message": "Login successful",
    "user": {
        "id": 1,
        "email": "user@example.com",
        "first_name": "John",
        "last_name": "Doe",
        "balance": 1500.00
    },
    "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9..."
}
```

#### POST /auth/logout
Logout and invalidate token.

**Headers:** `Authorization: Bearer <token>`

**Response:**
```json
{
    "message": "Logout successful"
}
```

### Money Transfers

#### POST /transfer
Send money to another user.

**Headers:** `Authorization: Bearer <token>`

**Request Body:**
```json
{
    "recipient_email": "recipient@example.com",
    "amount": 100.00,
    "description": "Payment for lunch"
}
```

**Response:**
```json
{
    "message": "Transfer completed successfully",
    "transaction": {
        "id": 123,
        "amount": 100.00,
        "recipient": {
            "email": "recipient@example.com",
            "name": "Jane Smith"
        },
        "description": "Payment for lunch",
        "status": "completed",
        "created_at": "2025-09-13 10:30:00"
    }
}
```

#### GET /transactions
Get transaction history with pagination.

**Headers:** `Authorization: Bearer <token>`

**Query Parameters:**
- `page` (optional): Page number (default: 1)
- `limit` (optional): Items per page (default: 20, max: 50)

**Response:**
```json
{
    "transactions": [
        {
            "id": 123,
            "amount": 100.00,
            "description": "Payment for lunch",
            "status": "completed",
            "type": "sent",
            "other_party": {
                "email": "recipient@example.com",
                "name": "Jane Smith"
            },
            "created_at": "2025-09-13 10:30:00"
        }
    ],
    "pagination": {
        "current_page": 1,
        "per_page": 20,
        "total": 45,
        "total_pages": 3
    }
}
```

### Budget Management

#### GET /budget
Get user's budgets.

**Headers:** `Authorization: Bearer <token>`

**Response:**
```json
{
    "budgets": [
        {
            "id": 1,
            "category": "Food",
            "budget_amount": 2000.00,
            "spent_amount": 1200.00,
            "remaining_amount": 800.00,
            "progress_percentage": 60.00,
            "budget_period": "monthly",
            "start_date": "2025-09-01",
            "end_date": "2025-09-30",
            "status": "active"
        }
    ],
    "total_budgets": 1
}
```

#### POST /budget
Create a new budget.

**Headers:** `Authorization: Bearer <token>`

**Request Body:**
```json
{
    "category": "Food",
    "budget_amount": 2000.00,
    "budget_period": "monthly"
}
```

**Response:**
```json
{
    "message": "Budget created successfully",
    "budget": {
        "id": 1,
        "category": "Food",
        "budget_amount": 2000.00,
        "spent_amount": 0.00,
        "remaining_amount": 2000.00,
        "budget_period": "monthly",
        "start_date": "2025-09-13",
        "end_date": "2025-10-13",
        "status": "active"
    }
}
```

#### PUT /budget
Update existing budget.

**Headers:** `Authorization: Bearer <token>`

**Request Body:**
```json
{
    "id": 1,
    "budget_amount": 2500.00,
    "category": "Food & Dining"
}
```

#### DELETE /budget
Delete a budget.

**Headers:** `Authorization: Bearer <token>`

**Query Parameters:**
- `id`: Budget ID to delete

### Spending Analysis

#### GET /spending
Get spending analysis and insights.

**Headers:** `Authorization: Bearer <token>`

**Response:**
```json
{
    "spending_analysis": {
        "last_30_days": [
            {
                "category": "Transfer",
                "total_spent": 1500.00,
                "transaction_count": 12
            }
        ],
        "budget_vs_spending": [
            {
                "category": "Food",
                "budget_amount": 2000.00,
                "spent_amount": 1200.00,
                "remaining": 800.00
            }
        ],
        "analysis_date": "2025-09-13 14:30:00"
    }
}
```

### Chatbot Support

#### POST /chatbot
Chat with the PayLekker support bot.

**Headers:** `Authorization: Bearer <token>`

**Request Body:**
```json
{
    "message": "What is my account balance?"
}
```

**Response:**
```json
{
    "message": "Let me check your account balance for you, John.\n\nYour current balance is R1,500.00",
    "intent": "balance_inquiry",
    "confidence": 0.8,
    "suggestions": [
        "Send money",
        "View transactions",
        "Create budget"
    ]
}
```

### Health Check

#### GET /health
Check API health status.

**Response:**
```json
{
    "status": "healthy",
    "timestamp": "2025-09-13 14:30:00",
    "version": "1.0.0",
    "service": "PayLekker API"
}
```

## Error Responses

All error responses follow this format:
```json
{
    "error": "Error message",
    "timestamp": "2025-09-13 14:30:00"
}
```

### HTTP Status Codes
- `200` - Success
- `201` - Created
- `400` - Bad Request
- `401` - Unauthorized
- `404` - Not Found
- `405` - Method Not Allowed
- `409` - Conflict
- `429` - Too Many Requests
- `500` - Internal Server Error

## Rate Limiting
- 100 requests per hour per IP address
- Exceeded requests return `429 Too Many Requests`

## Security Features
- JWT token authentication
- Password hashing with bcrypt
- SQL injection protection
- XSS protection headers
- CORS support
- Rate limiting
- Input validation and sanitization

## Setup Instructions

1. Upload all files to your web server
2. Configure database credentials in `config/database.php`
3. Run `php setup_database.php` to create tables
4. Test endpoints with your preferred REST client
5. Build your frontend application