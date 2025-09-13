# PayLekker Setup Instructions 🚀

**Professional Financial Inclusion Platform - Ready in Minutes**

> *"I am because we are"* - Ubuntu Philosophy  
> Democratizing financial services for everyone, everywhere.

## 🎯 Live Demo
**Experience PayLekker right now:** [https://pay.sewdani.co.za](https://pay.sewdani.co.za)

---

## � Docker Setup (Recommended)

**The easiest way to get PayLekker running - perfect for development and production.**

### **Prerequisites**
- Docker & Docker Compose installed
- Git

### **One-Command Launch**
```bash
# Clone and run PayLekker
git clone https://github.com/RudolphLamp/PayLekker.git
cd PayLekker
docker build -t paylekker .
docker run -p 8000:8000 paylekker
```

**Access your PayLekker instance at:** `http://localhost:8000`

The Docker container automatically:
- ✅ Sets up MySQL database with demo data
- ✅ Configures all required tables and relationships
- ✅ Loads sample transactions and game challenges
- ✅ Starts Python HTTP server optimized for PHP

---

## 💻 Manual Setup

### **Prerequisites**
- **PHP 8.0+** with PDO extension
- **MySQL 8.0+** or **MariaDB 10.6+**
- **Python 3.7+** (for development server)

### **Installation Commands**
```bash
# macOS with Homebrew
brew install php mysql python3

# Ubuntu/Debian
sudo apt update
sudo apt install php8.0 php8.0-mysql php8.0-pdo mysql-server python3

# Windows (use WSL2 or XAMPP)
# Download PHP, MySQL, Python from official websites
```

### **Step-by-Step Setup**

#### **1. Clone Repository**
```bash
git clone https://github.com/RudolphLamp/PayLekker.git
cd PayLekker
```

#### **2. Database Configuration**
```bash
# Start MySQL service
sudo systemctl start mysql  # Linux
brew services start mysql   # macOS

# Create database and user
mysql -u root -p
```

```sql
CREATE DATABASE paylekker_db;
CREATE USER 'paylekker_user'@'localhost' IDENTIFIED BY 'your_secure_password';
GRANT ALL PRIVILEGES ON paylekker_db.* TO 'paylekker_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

#### **3. Automated Setup (Recommended)**
**Use our comprehensive setup script:**
```bash
# Navigate to src directory
cd src/

# Run the master setup script
php setup.php
```

**The setup script automatically:**
- ✅ Creates all required database tables
- ✅ Sets up user wallet system
- ✅ Initializes game challenges and achievements
- ✅ Loads demo data for testing
- ✅ Validates all configurations

#### **4. Manual Database Configuration (Alternative)**
**If you prefer manual setup, edit `src/database.php`:**
```php
<?php
$host = 'localhost';
$dbname = 'paylekker_db';
$username = 'paylekker_user';
$password = 'your_secure_password';
?>
```

#### **5. Launch PayLekker**
```bash
# Method 1: PHP Built-in Server (Development)
cd src/
php -S localhost:8000

# Method 2: Python Server (Recommended)
cd src/
python3 -m http.server 8000 --cgi

# Method 3: Apache/Nginx (Production)
# Configure virtual host to point to src/ directory
```

**🎉 Access PayLekker:** `http://localhost:8000`

---

## 🔧 Alternative Setup Methods

### **XAMPP/MAMP (Beginner-Friendly)**
1. **Install XAMPP/MAMP:** Download from [apachefriends.org](https://www.apachefriends.org/)
2. **Start Services:** Launch Apache and MySQL
3. **Clone PayLekker:** Place in `htdocs/` or `www/` folder
4. **Setup Database:** 
   - Access phpMyAdmin (`http://localhost/phpmyadmin`)
   - Create database `paylekker_db`
   - Run `http://localhost/PayLekker/src/setup.php`
5. **Launch:** Visit `http://localhost/PayLekker/src/`

### **Production Server Setup**
```bash
# Clone to web root
sudo git clone https://github.com/RudolphLamp/PayLekker.git /var/www/html/paylekker

# Set permissions
sudo chown -R www-data:www-data /var/www/html/paylekker
sudo chmod -R 755 /var/www/html/paylekker

# Configure database
cd /var/www/html/paylekker/src
sudo php setup.php

# Configure Apache virtual host
sudo nano /etc/apache2/sites-available/paylekker.conf
```

**Apache Virtual Host Example:**
```apache
<VirtualHost *:80>
    ServerName your-domain.com
    DocumentRoot /var/www/html/paylekker/src
    
    <Directory /var/www/html/paylekker/src>
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
    
    ErrorLog ${APACHE_LOG_DIR}/paylekker_error.log
    CustomLog ${APACHE_LOG_DIR}/paylekker_access.log combined
</VirtualHost>
```

---

## 🔒 Security Configuration

### **Database Security**
```sql
-- Create secure user with limited privileges
CREATE USER 'paylekker_app'@'localhost' IDENTIFIED BY 'strong_random_password_here';
GRANT SELECT, INSERT, UPDATE, DELETE ON paylekker_db.* TO 'paylekker_app'@'localhost';
FLUSH PRIVILEGES;
```

### **File Permissions**
```bash
# Set secure permissions (Linux/macOS)
chmod 644 src/*.php
chmod 600 src/database.php  # Protect database config
chmod 755 src/assets/
```

### **Environment Variables (Production)**
```bash
# Create .env file for sensitive data
echo "DB_HOST=localhost" > .env
echo "DB_NAME=paylekker_db" >> .env
echo "DB_USER=paylekker_app" >> .env
echo "DB_PASS=your_secure_password" >> .env
echo "JWT_SECRET=your_jwt_secret_key" >> .env
chmod 600 .env
```

---

## 🧪 Testing Your Installation

### **Quick Health Check**
```bash
# Test database connection
php -r "
include 'src/database.php';
try {
    \$pdo = new PDO(\"mysql:host=\$host;dbname=\$dbname\", \$username, \$password);
    echo 'Database connection: ✅ SUCCESS\n';
} catch (Exception \$e) {
    echo 'Database connection: ❌ FAILED - ' . \$e->getMessage() . '\n';
}
"

# Test web server
curl -I http://localhost:8000
```

### **Demo Data Verification**
1. **Register Account:** Create test user account
2. **Login:** Verify JWT authentication works
3. **Dashboard:** Check transaction history loads
4. **Add Funds:** Test wallet functionality
5. **Mini Games:** Try Flappy Bird game
6. **Transfer:** Send money between accounts

---

## 🆘 Troubleshooting

### **Common Issues**

**🔍 "Database connection failed"**
```bash
# Check MySQL service
sudo systemctl status mysql     # Linux
brew services list | grep mysql # macOS

# Verify credentials
mysql -u paylekker_user -p paylekker_db
```

**🔍 "Page not found" errors**
```bash
# Check PHP extensions
php -m | grep -E "(pdo|mysql)"

# Verify file permissions
ls -la src/
```

**🔍 "JWT token invalid" errors**
```php
// Check JWT secret in jwt.php
// Ensure consistent secret key across all files
```

**🔍 Docker build fails**
```bash
# Clean Docker cache and rebuild
docker system prune -a
docker build --no-cache -t paylekker .
```

### **Performance Optimization**
```php
// Enable PHP OPcache (production)
opcache.enable=1
opcache.memory_consumption=128
opcache.interned_strings_buffer=8

// MySQL optimization
innodb_buffer_pool_size=128M
query_cache_type=1
query_cache_size=32M
```

---

## 📞 Support & Community

**Need help? We're here for you:**

- 🐛 **Bug Reports:** [GitHub Issues](https://github.com/RudolphLamp/PayLekker/issues)
- 💬 **Community:** [Discussions](https://github.com/RudolphLamp/PayLekker/discussions)
- 📧 **Email:** [rudolph@payLekker.ai](mailto:rudolph@payLekker.ai)
- 🌟 **Live Demo:** [https://pay.sewdani.co.za](https://pay.sewdani.co.za)

**Contributing to PayLekker:**
1. Fork the repository
2. Create feature branch: `git checkout -b feature/amazing-feature`
3. Make your changes
4. Run tests: `php src/test_game_system.php`
5. Submit Pull Request

---

*"PayLekker - Making financial inclusion accessible to everyone, everywhere."*

**Ready to democratize financial services?** 🚀
Header always set X-Content-Type-Options nosniff
Header always set X-Frame-Options DENY
Header always set X-XSS-Protection "1; mode=block"
```

**Nginx Configuration:**
```nginx
location / {
    try_files $uri $uri/ /index.php?$query_string;
}

location ~ \.php$ {
    fastcgi_pass 127.0.0.1:9000;
    fastcgi_index index.php;
    include fastcgi_params;
}
```

### **Production Checklist**
- [ ] Update database credentials in `src/database.php`
- [ ] Enable HTTPS/SSL certificate
- [ ] Set secure JWT secret key
- [ ] Configure proper file permissions (755 for directories, 644 for files)
- [ ] Enable production error handling (disable error display)
- [ ] Set up database backups
- [ ] Configure web server security headers

---

## 🧪 Testing Your Setup

### **Verify Everything Works:**
1. **Homepage Loading:** Visit `http://localhost:8000` - should show PayLekker landing page
2. **Database Connection:** Registration should work without errors
3. **API Endpoints:** Login should return JWT token
4. **File Permissions:** All pages should load correctly

### **Demo Data Setup:**
```bash
# Optional: Load demo users and transactions
php load_demo_data.php
```

---

## 🆘 Troubleshooting

### **Common Issues & Solutions:**

**"Database connection failed"**
- Check MySQL is running: `sudo systemctl status mysql` (Linux) or `brew services list | grep mysql` (macOS)
- Verify credentials in `src/database.php`
- Ensure database and user exist

**"Permission denied" errors**
```bash
# Fix file permissions
chmod -R 755 /path/to/PayLekker
chmod -R 644 /path/to/PayLekker/src/*.php
```

**"PDO extension not found"**
```bash
# Install PHP PDO extension
sudo apt install php-pdo php-mysql  # Ubuntu/Debian
brew install php                     # macOS (includes PDO)
```

**Port 8000 already in use**
```bash
# Use different port
php -S localhost:8080 -t src/

# Or kill existing process
lsof -ti:8000 | xargs kill
```

---

## 🎯 Next Steps After Setup

1. **Register** your first account
2. **Explore** the dashboard and features
3. **Test** money transfers between demo accounts
4. **Try** the AI financial assistant
5. **Review** the codebase in `src/` directory

**Ready to experience financial inclusion in action!** 🇿🇦💰
