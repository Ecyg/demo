FROM ubuntu:latest

# Install Apache, PHP-FPM and required modules
RUN apt update && apt install -y apache2 php-fpm php libapache2-mod-php php-curl \
    && rm -rf /var/lib/apt/lists/*

# Enable required Apache modules
RUN a2enmod rewrite proxy proxy_fcgi

# Enable .htaccess support
RUN sed -i 's/AllowOverride None/AllowOverride All/' /etc/apache2/apache2.conf

# Set the default DirectoryIndex to index.php
RUN echo "<IfModule dir_module>\n    DirectoryIndex index.php index.html index.htm\n</IfModule>" > /etc/apache2/mods-enabled/dir.conf

# Configure Apache to handle PHP via PHP-FPM
RUN echo "<VirtualHost *:80>\n\
    DocumentRoot /var/www/html\n\
    <Directory /var/www/html>\n\
        AllowOverride All\n\
        Require all granted\n\
    </Directory>\n\
    <FilesMatch \\.php$>\n\
        SetHandler proxy:fcgi://127.0.0.1:9000\n\
    </FilesMatch>\n\
</VirtualHost>" > /etc/apache2/sites-available/000-default.conf

# Create .htaccess to redirect "/" to "/template.php"
RUN echo "RewriteEngine On\nRewriteCond %{REQUEST_URI} ^/$\nRewriteRule ^$ /template.php [R=302,L]" > /var/www/html/.htaccess

# Find the PHP version and configure PHP-FPM properly
RUN PHP_VERSION=$(php -r 'echo PHP_MAJOR_VERSION.".".PHP_MINOR_VERSION;') && \
    echo "[www]\n\
user = www-data\n\
group = www-data\n\
listen = 127.0.0.1:9000\n\
pm = dynamic\n\
pm.max_children = 5\n\
pm.start_servers = 2\n\
pm.min_spare_servers = 1\n\
pm.max_spare_servers = 3\n\
; Vulnerable configuration - larger buffer sizes\n\
request_terminate_timeout = 300\n\
security.limit_extensions = .php\n\
; Setting a high value makes smuggling easier\n\
request_slowlog_timeout = 60s\n\
slowlog = /var/log/php-fpm.slow.log" > /etc/php/${PHP_VERSION}/fpm/pool.d/www.conf

# Add application files
COPY . /var/www/html/

# Expose port 80
EXPOSE 80

# Create a startup script to run both Apache and PHP-FPM
RUN echo "#!/bin/bash\n\
PHP_VERSION=\$(php -r 'echo PHP_MAJOR_VERSION.\".\".PHP_MINOR_VERSION;')\n\
service php\${PHP_VERSION}-fpm start\n\
/usr/sbin/apache2ctl -D FOREGROUND" > /start.sh && chmod +x /start.sh

# Run the startup script
CMD ["/start.sh"]
