# Use the MySQL base image
FROM mysql:latest

# Install Apache and PHP packages
RUN apt-get update && \
    apt-get install -y apache2 php libapache2-mod-php php-mysql

# Copy PHP files to the Apache document root
COPY index.php /var/www/html/

# Expose ports for MySQL and Apache
EXPOSE 3306 80

# Start Apache and MySQL services
CMD service apache2 start && service mysql start && tail -f /dev/null
