# PayLekker - Docker Configuration
# Multi-stage build for optimized production deployment

FROM php:8.2-cli as php-setup

# Install required PHP extensions and system dependencies
RUN apt-get update && apt-get install -y \
    mariadb-server \
    mariadb-client \
    python3 \
    python3-pip \
    curl \
    && docker-php-ext-install pdo pdo_mysql mysqli \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Set working directory
WORKDIR /app

# Copy application files
COPY . .

# Create MySQL data directory and set permissions
RUN mkdir -p /var/lib/mysql /var/log/mysql /run/mysqld \
    && chown -R mysql:mysql /var/lib/mysql /var/log/mysql /run/mysqld

# Initialize MySQL database
RUN service mysql start \
    && mysql -e "CREATE DATABASE IF NOT EXISTS paylekker CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;" \
    && mysql -e "CREATE USER IF NOT EXISTS 'paylekker_user'@'localhost' IDENTIFIED BY 'secure_password';" \
    && mysql -e "GRANT ALL PRIVILEGES ON paylekker.* TO 'paylekker_user'@'localhost';" \
    && mysql -e "FLUSH PRIVILEGES;" \
    && service mysql stop

# Create startup script
RUN echo '#!/bin/bash\n\
set -e\n\
\n\
echo "Starting PayLekker Docker Container..."\n\
echo "================================"\n\
\n\
# Start MySQL\n\
service mysql start\n\
echo "✓ MySQL started"\n\
\n\
# Wait for MySQL to be ready\n\
echo "Waiting for MySQL to be ready..."\n\
while ! mysqladmin ping -h localhost --silent; do\n\
    sleep 1\n\
done\n\
echo "✓ MySQL is ready"\n\
\n\
# Run PayLekker setup if not already done\n\
if [ ! -f /app/.setup_complete ]; then\n\
    echo "Running PayLekker setup..."\n\
    cd /app\n\
    php setup.php --with-demo-data\n\
    touch /app/.setup_complete\n\
    echo "✓ PayLekker setup completed"\n\
fi\n\
\n\
# Start PHP development server\n\
echo "Starting web server on port 8000..."\n\
echo ""\n\
echo "🚀 PayLekker is now running at:"\n\
echo "   http://localhost:8000"\n\
echo ""\n\
echo "📧 Demo accounts:"\n\
echo "   • demo@paylekker.co.za / demo123"\n\
echo "   • family@paylekker.co.za / family123"\n\
echo ""\n\
echo "🌐 Live demo available at: https://pay.sewdani.co.za"\n\
echo "================================"\n\
\n\
cd /app/src\n\
php -S 0.0.0.0:8000\n\
' > /app/start.sh && chmod +x /app/start.sh

# Expose port
EXPOSE 8000

# Health check
HEALTHCHECK --interval=30s --timeout=10s --start-period=40s --retries=3 \
    CMD curl -f http://localhost:8000/ || exit 1

# Set the startup script as entrypoint
ENTRYPOINT ["/app/start.sh"]
