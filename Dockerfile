# Use an official PHP runtime as the base image
FROM php:7.4-apache

# Install system dependencies and PHP extensions
RUN apt-get update && \
    apt-get install -y \
        libpng-dev \
        libjpeg-dev \
        libfreetype6-dev \
        libzip-dev \
        zip \
        unzip \
        && \
    rm -rf /var/lib/apt/lists/* && \
    docker-php-ext-configure gd --with-freetype --with-jpeg && \
    docker-php-ext-install gd mysqli pdo pdo_mysql zip

# Enable Apache modules and configurations
RUN a2enmod rewrite

# Set the working directory
WORKDIR /var/www/html

# Copy your application code into the container
COPY . /var/www/html

# Expose port 80 for Apache
EXPOSE 80

# Define environment variables for MySQL connection
ENV DB_HOST=172.16.19.41
ENV DB_PORT=3306
ENV DB_DATABASE=ipphone
ENV DB_USERNAME=ravi
ENV DB_PASSWORD=R@vi1234

# Start Apache in the foreground
CMD ["apache2-foreground"]
