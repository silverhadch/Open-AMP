#!/bin/sh

# Variables
PHP_VERSION="8.3"
PHP_DAEMON="php$(echo $PHP_VERSION | tr -d .)_fpm"

# Function to install Apache-httpd
install_httpd() {
    echo "Installing Apache-httpd..."
    pkg_add apache-httpd
    rcctl enable apache2
    pkg_add php-apache
    pkg_add php-mysqli 

    # Check if Apache started successfully
    rcctl start apache2
    if rcctl check apache2; then
        echo "Apache-httpd started successfully."
    else
        echo "Failed to start Apache-httpd. Check the configuration and logs."
        exit 1
    fi
}

# Function to install MySQL (MariaDB)
install_mysql() {
    echo "Installing MySQL (MariaDB)..."
    pkg_add mariadb-server
    pkg_add php-mysql

    # Initialize MySQL
    mysql_install_db   
    rcctl enable mysqld
    rcctl start mysqld

    # Secure MySQL installation
    mysql_secure_installation  
    echo "Log in for the first time and type 'exit':"
    mysql -u root -p
}

# Function to install and configure PHP with PHP-FPM
install_php() {
    echo "Installing PHP and configuring PHP-FPM..."
    pkg_add php php-fpm php-mysqli

    # Configure PHP for Apache and MySQL
    ln -sf /etc/php-${PHP_VERSION}.sample/mysql.ini /etc/php-${PHP_VERSION}/mysql.ini
    ln -sf /etc/php-${PHP_VERSION}.sample/mysqli.ini /etc/php-${PHP_VERSION}/mysqli.ini
    ln -sf /etc/php-${PHP_VERSION}.sample/fpm.conf /etc/php-${PHP_VERSION}/fpm.conf

    # Enable and start PHP-FPM
    rcctl enable ${PHP_DAEMON}
    rcctl start ${PHP_DAEMON}

    # Check if PHP-FPM started successfully
    if rcctl check ${PHP_DAEMON}; then
        echo "PHP-FPM started successfully."
    else
        echo "Failed to start PHP-FPM."
        exit 1
    fi
}

# Function to install phpMyAdmin
install_phpmyadmin() {
    echo "Installing phpMyAdmin..."
    pkg_add phpMyAdmin

    # Configure phpMyAdmin
    ln -sf /etc/php-${PHP_VERSION}.sample/gd.ini /etc/php-${PHP_VERSION}/gd.ini
    ln -sf /etc/php-${PHP_VERSION}.sample/mcrypt.ini /etc/php-${PHP_VERSION}/mcrypt.ini
    ln -s /var/www/phpMyAdmin /var/www/htdocs/phpMyAdmin

    echo "phpMyAdmin installed and configured."
}

# Function to copy index.php and other necessary files
replace_index() {
    echo "Replacing /var/www/index.html with index.php from repository..."
    rm -f /var/www/htdocs/index.html
    cp index.php /var/www/htdocs/
}

# Main Process
install_httpd      # Install and configure Apache
install_mysql      # Install and configure MySQL (MariaDB)
install_php        # Install and configure PHP with FPM
install_phpmyadmin # Install and configure phpMyAdmin
replace_index      # Replace index.html with index.php from repository

# Restart services to apply configurations
echo "Restarting services..."
rcctl restart apache2
rcctl restart ${PHP_DAEMON}

echo "LAMPP setup complete! Access your project management at: http://localhost/"
