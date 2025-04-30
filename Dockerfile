# Use the official PHP 8.1 Apache image
FROM php:8.1-apache

# Enable Apache mod_rewrite for pretty URLs
RUN a2enmod rewrite

# Copy all project files to Apache's web directory
COPY . /var/www/html/

# Set working directory
WORKDIR /var/www/html

# Set ownership (optional, avoids permission issues)
RUN chown -R www-data:www-data /var/www/html

# Expose port 80 for web traffic
EXPOSE 80
