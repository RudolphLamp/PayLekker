<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PayLekker API Documentation</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .endpoint { background: #f8f9fa; padding: 3px 6px; border-radius: 3px; font-family: monospace; }
        .method-post { color: #fff; background: #28a745; padding: 2px 8px; border-radius: 3px; font-size: 0.8em; }
        .method-get { color: #fff; background: #17a2b8; padding: 2px 8px; border-radius: 3px; font-size: 0.8em; }
        pre { background: #f8f9fa; padding: 15px; border-radius: 5px; }
    </style>
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card">
                    <div class="card-header text-center">
                        <h1 class="mb-0">💰 PayLekker API</h1>
                        <p class="text-muted">South African Digital Banking API</p>
                        <p class="mb-0">Hosted on <strong>pay.sewdani.co.za</strong></p>
                    </div>
                    <div class="card-body">
                        
                        <div class="alert alert-info">
                            <h5>🚀 Welcome to PayLekker API</h5>
                            <p class="mb-0">A complete authentication system for your South African digital banking app. All endpoints return JSON responses and use JWT token authentication.</p>
                        </div>
                        
                        <h3>📚 API Endpoints</h3>
                        
                        <!-- Registration Endpoint -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5><span class="method-post">POST</span> /register.php</h5>
                                <p class="mb-0">Create a new user account</p>
                            </div>
                            <div class="card-body">
                                <h6>Request Body:</h6>
                                <pre>{
  "name": "John Doe",
  "email": "john@example.com",
  "password": "securepassword",
  "phone": "+27123456789"  // optional
}</pre>
                                
                                <h6>Success Response (201):</h6>
                                <pre>{
  "success": true,
  "message": "User registered successfully",
  "data": {
    "user": {
      "id": 1,
      "name": "John Doe",
      "email": "john@example.com",
      "phone": "+27123456789",
      "balance": "0.00"
    },
    "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9..."
  }
}</pre>
                            </div>
                        </div>
                        
                        <!-- Login Endpoint -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5><span class="method-post">POST</span> /login.php</h5>
                                <p class="mb-0">Authenticate user and receive JWT token</p>
                            </div>
                            <div class="card-body">
                                <h6>Request Body:</h6>
                                <pre>{
  "email": "john@example.com",
  "password": "securepassword"
}</pre>
                                
                                <h6>Success Response (200):</h6>
                                <pre>{
  "success": true,
  "message": "Login successful",
  "data": {
    "user": {
      "id": 1,
      "name": "John Doe",
      "email": "john@example.com",
      "phone": "+27123456789",
      "balance": "0.00",
      "created_at": "2024-01-15 10:30:00"
    },
    "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9..."
  }
}</pre>
                            </div>
                        </div>
                        
                        <!-- Profile Endpoint -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5><span class="method-get">GET</span> /profile.php</h5>
                                <p class="mb-0">Get current user profile (requires authentication)</p>
                            </div>
                            <div class="card-body">
                                <h6>Headers:</h6>
                                <pre>Authorization: Bearer YOUR_JWT_TOKEN</pre>
                                
                                <h6>Success Response (200):</h6>
                                <pre>{
  "success": true,
  "message": "User profile retrieved successfully",
  "data": {
    "user": {
      "id": 1,
      "name": "John Doe",
      "email": "john@example.com",
      "phone": "+27123456789",
      "balance": "100.50",
      "created_at": "2024-01-15 10:30:00",
      "updated_at": "2024-01-16 15:45:00"
    }
  }
}</pre>
                            </div>
                        </div>
                        
                        <!-- Logout Endpoint -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5><span class="method-post">POST</span> /logout.php</h5>
                                <p class="mb-0">Logout user (requires authentication)</p>
                            </div>
                            <div class="card-body">
                                <h6>Headers:</h6>
                                <pre>Authorization: Bearer YOUR_JWT_TOKEN</pre>
                                
                                <h6>Success Response (200):</h6>
                                <pre>{
  "success": true,
  "message": "Logged out successfully",
  "data": {
    "message": "Please remove the token from your client storage"
  }
}</pre>
                            </div>
                        </div>
                        
                        <h3>🔐 Authentication</h3>
                        <div class="alert alert-warning">
                            <h6>JWT Token Usage</h6>
                            <p>Protected endpoints require a JWT token in the Authorization header:</p>
                            <code>Authorization: Bearer YOUR_JWT_TOKEN</code>
                            <p class="mt-2 mb-0">Tokens expire after 24 hours. Store them securely in your client application.</p>
                        </div>
                        
                        <h3>⚠️ Error Responses</h3>
                        <div class="card">
                            <div class="card-body">
                                <p>All error responses follow this format:</p>
                                <pre>{
  "success": false,
  "error": "Error message description",
  "timestamp": "2024-01-15 10:30:00"
}</pre>
                                
                                <h6>Common HTTP Status Codes:</h6>
                                <ul>
                                    <li><strong>200:</strong> Success</li>
                                    <li><strong>201:</strong> Created (registration)</li>
                                    <li><strong>400:</strong> Bad Request (validation errors)</li>
                                    <li><strong>401:</strong> Unauthorized (invalid token/credentials)</li>
                                    <li><strong>404:</strong> Not Found</li>
                                    <li><strong>405:</strong> Method Not Allowed</li>
                                    <li><strong>409:</strong> Conflict (email already exists)</li>
                                    <li><strong>500:</strong> Internal Server Error</li>
                                </ul>
                            </div>
                        </div>
                        
                        <div class="mt-4">
                            <h3>🧪 Test Your API</h3>
                            <div class="btn-group">
                                <a href="test.php" class="btn btn-primary">Run Test Suite</a>
                                <a href="https://github.com/RudolphLamp/PayLekker" class="btn btn-secondary">View Source Code</a>
                            </div>
                        </div>
                        
                        <div class="mt-4">
                            <div class="alert alert-success">
                                <h5>✅ Ready for Production</h5>
                                <p class="mb-0">Your PayLekker API is ready to be used by web applications, mobile apps, or any HTTP client. The authentication system is secure and follows industry standards.</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card-footer text-center text-muted">
                        <small>PayLekker API v1.0 | Built for South African Digital Banking</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>