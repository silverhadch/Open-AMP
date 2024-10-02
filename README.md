# Open-AMP: OpenBSD Apache-MySQL-PHP Setup

This repository contains a script to automate the setup of Apache, MySQL (MariaDB), and PHP on OpenBSD. This port was created by myself, and it configures a basic LAMPP stack similar to other LAMP-like environments but tailored for OpenBSD.

## Features
- Installs **Apache HTTP Server** (not the native httpd)
- Configures **MariaDB (MySQL)** with PHP MySQL integration
- Adds **phpMyAdmin** for easy database management
- Example configuration files provided
- Serve Projects on localhost in /var/www/htdocs

---

## Installation

1. **Clone the Repository:**
   ```sh
   git clone https://github.com/silverhadch/Open-AMP
   cd Open-AMP

2. Run the Setup Script:
Simply execute the script as root (via doas or sudo) to install and configure the LAMPP environment.

doas ./setup.sh


3. Manual Adjustments:
The script uses version numbers that currently need to be adjusted manually. You can modify the script directly to align with the installed versions of PHP and other software. I plan to automate this in future releases.


4. Example httpd.conf:
An example httpd.conf file has been provided in the repository. You can copy this to /etc/httpd.conf to start your server configuration.

cp httpd.conf /etc/httpd.conf




---

Example httpd.conf

# Basic Apache HTTP Server Configuration

User root
Group daemon

ServerRoot "/usr/www/htdocs"
Listen 80
DocumentRoot "/var/www"
DirectoryIndex index.html index.php

<Directory "/var/www">
    Options Indexes FollowSymLinks
    AllowOverride None
    Require all granted
</Directory>

ErrorLog "/var/log/httpd-error.log"
CustomLog "/var/log/httpd-access.log" common

LoadModule mime_module libexec/apache/httpd/modules/mod_mime.so
LoadModule dir_module libexec/apache/httpd/modules/mod_dir.so
LoadModule php_module libexec/apache/httpd/modules/libphp.so

AddType application/x-httpd-php .php

Feel free to modify this as needed for your own projects.


---

Future Work

Automate version detection for PHP, Apache, and MySQL to remove the need for manual version changes.



---

Access

Once the script is complete, you can access your environment at:
http://localhost/
phpMyAdmin can be accessed at:
http://localhost/phpMyAdmin


---

Contributions

Contributions and feedback are welcome!

You can copy and paste this Markdown directly into your GitHub README file. It will render correctly with headers, bullet points, and code blocks.

