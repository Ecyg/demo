FROM ubuntu:latest

# Install Apache and PHP
RUN apt update && apt install -y apache2 php libapache2-mod-php && rm -rf /var/lib/apt/lists/*

# Enable .htaccess support
RUN a2enmod rewrite

# Allow .htaccess to override Apache settings
RUN sed -i 's/AllowOverride None/AllowOverride All/' /etc/apache2/apache2.conf

# Set the default DirectoryIndex to index.php
RUN echo "<IfModule dir_module>\n    DirectoryIndex index.php index.html index.htm\n</IfModule>" > /etc/apache2/mods-enabled/dir.conf

# Ensure the correct DocumentRoot in Apache config
RUN echo "<VirtualHost *:80>\n    DocumentRoot /var/www/html\n    <Directory /var/www/html>\n        AllowOverride All\n        Require all granted\n    </Directory>\n</VirtualHost>" > /etc/apache2/sites-available/000-default.conf

# Create .htaccess file for redirect
RUN echo "RewriteEngine On\nRewriteCond %{REQUEST_URI} ^/$\nRewriteRule ^$ /index.php [L,R=301]" > /var/www/html/.htaccess

# Add vulnerable PHP application and ensure index.php exists
COPY index.php /var/www/html/index.php
COPY vulnerable_app.php /var/www/html/vulnerable_app.php
COPY xss_hpp.php /var/www/html/xss_hpp.php
COPY type_juggling.php /var/www/html/type_juggling.php

# Expose port 80
EXPOSE 80

# Restart Apache to apply changes
RUN service apache2 restart

# Keep Apache running in the foreground
CMD ["/usr/sbin/apache2ctl", "-D", "FOREGROUND"]
