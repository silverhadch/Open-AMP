#!/bin/sh

# Variables
PHP_VERSION="8.3"
PHP_DAEMON="php${PHP_VERSION}_fpm"

# Function to install Apache-httpd
install_httpd() {
    echo "Installing Apache-httpd..."
    pkg_add apache-httpd
    rcctl enable apache2
    rcctl start apache2
    pkg_add php-apache
    pkg_add php-mysqli 

    # Check if httpd started successfully
    if rcctl check apache2; then
        echo "httpd started successfully."
    else
        echo "Failed to start httpd. Check the configuration and logs."
        exit 1
    fi
}

# Function to install MySQL
install_mysql() {
    echo "Installing MySQL..."
    pkg_add mariadb-server 
    pkg_add php-mysql
    echo "Configuring MySQL..."

    # Setting up PHP configuration for MySQL
    ln -sf /var/www/conf/modules.sample/php-${PHP_VERSION}.conf /var/www/conf/modules/php.conf 
    ln -sf /etc/php-${PHP_VERSION}.sample/mysql.ini /etc/php-${PHP_VERSION}/mysql.ini

    # Initializing the MySQL database
    mysql_install_db   

    rcctl enable mysqld
    rcctl start mysqld

    # Secure MySQL installation
    mysql_secure_installation  
    echo "Log in for the first time and then type 'exit':"
    mysql -u root -p 
}

# Function to install phpMyAdmin
install_phpmyadmin() {
    echo "Installing phpMyAdmin..." 
    pkg_add phpMyAdmin 

    echo "Configuring phpMyAdmin..."
    ln -sf /var/www/conf/modules.sample/php-${PHP_VERSION}.conf /var/www/conf/modules/php.conf
    ln -sf /etc/php-${PHP_VERSION}.sample/mysqli.ini /etc/php-${PHP_VERSION}/

    ln -sf /etc/php-${PHP_VERSION}.sample/gd.ini /etc/php-${PHP_VERSION}/gd.ini 
    ln -sf /etc/php-${PHP_VERSION}.sample/mcrypt.ini /etc/php-${PHP_VERSION}/mcrypt.ini 
    ln -s /var/www/phpMyAdmin /var/www/htdocs/phpMyAdmin

    rcctl restart apache2
}

# Replace default index.html with index.php from repo
replace_index() {
    echo "Replacing /var/www/index.html with index.php from repo..."
    rm -f /var/www/index.html
    rm -f /var/www/htdocs/index.html
    cp index.php /var/www/
    cp index.php /var/www/htdocs
}

# Main process
install_httpd
install_mysql
install_phpmyadmin
replace_index
rcctl restart apache2 ${PHP_DAEMON}

echo "LAMPP setup complete! Access your project management at: http://localhost/"
