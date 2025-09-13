# PayLekker Setup Instructions 🚀

**Getting PayLekker running in 5 minutes** - Because financial inclusion shouldn't be hard to demo!

---

## 📦 Requirements

### **Essential Prerequisites**
- **PHP 7.4+** with PDO extension
  ```bash
  # Check your PHP version
  php --version
  
  # On macOS with Homebrew
  brew install php
  
  # On Ubuntu/Debian
  sudo apt install php php-pdo php-mysql
  ```

- **MySQL 5.7+ or MariaDB 10.2+**
  ```bash
  # On macOS with Homebrew
  brew install mysql
  brew services start mysql
  
  # On Ubuntu/Debian
  sudo apt install mysql-server
  sudo systemctl start mysql
  ```

### **Optional (Recommended)**
- **Web Server** (Apache/Nginx) - *For production deployment*
- **SSL Certificate** - *For production security*
- **Composer** - *For dependency management if extending*

---

## ⚡ Quick Start (5 Minutes)

### **Step 1: Clone & Navigate**
```bash
git clone https://github.com/RudolphLamp/PayLekker.git
cd PayLekker
```

### **Step 2: Database Setup**
```bash
# Create MySQL database
mysql -u root -p
CREATE DATABASE paylekker;
CREATE USER 'paylekker_user'@'localhost' IDENTIFIED BY 'secure_password';
GRANT ALL PRIVILEGES ON paylekker.* TO 'paylekker_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;

# Initialize database tables
cd src/
php setup_database.php
```

### **Step 3: Configure Database Connection**
Edit `src/database.php` with your credentials:
```php
$host = 'localhost';
$dbname = 'paylekker';
$username = 'paylekker_user';
$password = 'secure_password';
```

### **Step 4: Launch PayLekker**
```bash
# Start development server
php -S localhost:8000 -t src/

# Open in browser
open http://localhost:8000
```

**🎉 PayLekker is now running! Register an account and start exploring.**

---

## 🔧 Alternative Setup Methods

### **Method 1: Using XAMPP/MAMP (Beginner-Friendly)**
1. Download and install [XAMPP](https://www.apachefriends.org/) or [MAMP](https://www.mamp.info/)
2. Start Apache and MySQL services
3. Clone PayLekker to your `htdocs` or `www` folder
4. Create database via phpMyAdmin
5. Run `setup_database.php` through the web interface

### **Method 2: Docker Setup (Advanced)**
```bash
# Build and run with Docker
docker build -t paylekker .
docker run -p 8000:8000 paylekker

# Or use docker-compose (if configured)
docker-compose up
```

---

## ⚙️ Production Deployment

### **Web Server Configuration**

**Apache (.htaccess example):**
```apache
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ index.php [QSA,L]

# Security headers
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
