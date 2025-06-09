FROM ubuntu:latest

# Install Apache, PHP and required modules
RUN apt-get update && apt-get install -y apache2 php php-fpm libapache2-mod-php php-curl \
    && rm -rf /var/lib/apt/lists/*

# Enable required Apache modules
RUN a2enmod rewrite proxy proxy_fcgi

# Configure PHP to work with Apache directly
RUN a2enconf php*-fpm

# Enable .htaccess support
RUN sed -i 's/AllowOverride None/AllowOverride All/g' /etc/apache2/apache2.conf

# Create a minimal Apache site config
RUN echo "<VirtualHost *:80>\n\
    ServerAdmin webmaster@localhost\n\
    DocumentRoot /var/www/html\n\
    ErrorLog \${APACHE_LOG_DIR}/error.log\n\
    CustomLog \${APACHE_LOG_DIR}/access.log combined\n\
    <Directory /var/www/html>\n\
        Options Indexes FollowSymLinks\n\
        AllowOverride All\n\
        Require all granted\n\
    </Directory>\n\
</VirtualHost>" > /etc/apache2/sites-available/000-default.conf

# Create a backup PHP test file in case your app doesn't have one
RUN echo "<?php\necho '<h1>Template Page</h1><p>Your server is working correctly!</p><p>PHP version: ' . phpversion() . '</p>';\n?>" > /var/www/html/template.php.bak

# Create a fallback index to redirect when no index exists
RUN echo "<?php\nheader('Location: /template.php');\nexit;\n?>" > /var/www/html/index.php.bak

# Create a backup .htaccess
RUN echo "RewriteEngine On\nRewriteRule ^$ /template.php [R=302,L]" > /var/www/html/.htaccess.bak

# Install Ollama and required dependencies
RUN apt-get update && apt-get install -y \
    curl \
    && curl -fsSL https://ollama.com/install.sh | sh \
    && rm -rf /var/lib/apt/lists/*

# Create a script to pull the model
RUN echo '#!/bin/bash\n\
echo "Pulling llama2 model..."\n\
ollama pull llama2\n\
echo "Model pull complete"\n' > /pull-model.sh \
    && chmod +x /pull-model.sh

# Copy all files from the build context to Apache's document root
COPY . /var/www/html/

# If template.php doesn't exist, use the backup
RUN if [ ! -f /var/www/html/template.php ]; then \
        cp /var/www/html/template.php.bak /var/www/html/template.php; \
    fi

# If index.php doesn't exist, use the backup
RUN if [ ! -f /var/www/html/index.php ]; then \
        cp /var/www/html/index.php.bak /var/www/html/index.php; \
    fi

# If .htaccess doesn't exist, use the backup
RUN if [ ! -f /var/www/html/.htaccess ]; then \
        cp /var/www/html/.htaccess.bak /var/www/html/.htaccess; \
    fi

# Fix permissions for all files
RUN chown -R www-data:www-data /var/www/html/ \
    && find /var/www/html/ -type d -exec chmod 755 {} \; \
    && find /var/www/html/ -type f -exec chmod 644 {} \; \
    && chmod 755 /var/www/html/*.php

# Set PHP config to ensure it works properly
RUN PHP_VERSION=$(php -r 'echo PHP_MAJOR_VERSION.".".PHP_MINOR_VERSION;') \
    && echo "opcache.enable=1\n\
opcache.enable_cli=1\n\
opcache.memory_consumption=128\n\
opcache.interned_strings_buffer=8\n\
opcache.max_accelerated_files=4000\n\
opcache.revalidate_freq=60\n\
output_buffering=4096\n\
implicit_flush=On\n\
output_handler=ob_gzhandler\n" >> /etc/php/${PHP_VERSION}/fpm/php.ini \
    && echo "opcache.enable=1\n\
opcache.enable_cli=1\n\
opcache.memory_consumption=128\n\
opcache.interned_strings_buffer=8\n\
opcache.max_accelerated_files=4000\n\
opcache.revalidate_freq=60\n\
output_buffering=4096\n\
implicit_flush=On\n\
output_handler=ob_gzhandler\n" >> /etc/php/${PHP_VERSION}/apache2/php.ini

# Expose ports 80 (Apache) and 11434 (Ollama)
EXPOSE 80 11434

# Create a simple and reliable startup script
RUN echo '#!/bin/bash\n\
echo "Starting Ollama server..."\n\
ollama serve &\n\
sleep 5\n\
\n\
echo "Pulling llama2 model..."\n\
ollama pull llama2\n\
\n\
echo "Starting PHP-FPM..."\n\
PHP_VERSION=$(php -r "echo PHP_MAJOR_VERSION.\".\".PHP_MINOR_VERSION;")\n\
service php${PHP_VERSION}-fpm start\n\
status=$?\n\
if [ $status -ne 0 ]; then\n\
    echo "Failed to start PHP-FPM: $status"\n\
    exit $status\n\
fi\n\
\n\
echo "Starting Apache..."\n\
exec apache2ctl -D FOREGROUND\n' > /start.sh && chmod +x /start.sh

CMD ["/start.sh"]