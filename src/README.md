# PayLekker API - File Structure

```
src/
├── api/
│   ├── .htaccess                 # URL rewriting and security configuration
│   ├── index.php                 # Main API router with rate limiting
│   ├── auth.php                  # Authentication endpoints (register/login/logout)
│   ├── transfer.php              # Money transfer and transaction history
│   ├── budget.php                # Budget management and spending analysis
│   └── chatbot.php               # FAQ-driven chatbot support
├── auth/
│   └── jwt.php                   # JWT token generation and validation
├── config/
│   ├── database.php              # Database connection configuration
│   └── setup.php                 # Database schema setup
├── setup_database.php            # Database initialization script
└── API_DOCUMENTATION.md          # Complete API documentation
```

## Quick Setup Guide

### 1. Database Setup
Run the database setup script:
```bash
cd src/
php setup_database.php
```

### 2. Upload to Server
Upload the entire `src/` folder contents to your web server at `pay.sewdani.co.za`

### 3. Test the API
Your API will be available at:
```
https://pay.sewdani.co.za/api/
```

### 4. Example API Calls

#### Register a new user:
```bash
curl -X POST https://pay.sewdani.co.za/api/auth/register \
  -H "Content-Type: application/json" \
  -d '{
    "email": "test@example.com",
    "password": "password123",
    "first_name": "Test",
    "last_name": "User"
  }'
```

#### Login:
```bash
curl -X POST https://pay.sewdani.co.za/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "test@example.com",
    "password": "password123"
  }'
```

#### Send Money (requires token from login):
```bash
curl -X POST https://pay.sewdani.co.za/api/transfer \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer YOUR_JWT_TOKEN" \
  -d '{
    "recipient_email": "recipient@example.com",
    "amount": 100.00,
    "description": "Test transfer"
  }'
```

#### Chat with Bot:
```bash
curl -X POST https://pay.sewdani.co.za/api/chatbot \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer YOUR_JWT_TOKEN" \
  -d '{
    "message": "What is my balance?"
  }'
```

## Features Implemented ✅

- **User Authentication**: JWT-based register, login, logout
- **Money Transfers**: P2P transfers with balance validation
- **Transaction History**: Paginated transaction listing
- **Budget Management**: Create, update, delete, and track budgets
- **Spending Analysis**: Categorized spending insights
- **Chatbot Support**: FAQ-driven customer support
- **Security**: Rate limiting, CORS, input validation, SQL injection protection
- **Error Handling**: Consistent error responses
- **Documentation**: Complete API documentation

## Database Tables Created

- **users**: User accounts with secure password hashing
- **sessions**: JWT token management for security
- **transactions**: Money transfer records with full audit trail
- **budgets**: Financial budgeting with spending tracking

Your PayLekker API is production-ready! 🚀
