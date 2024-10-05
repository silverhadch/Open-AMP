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
```sh
   doas ./install.sh
```

3. Example httpd.conf:
An example httpd.conf file has been provided in the repository. You can copy this to /etc/httpd.conf to start your server configuration.

cp httpd.conf /etc/httpd.conf


---

## Future Work

Bug Fixes and maybe more Features.

---

## Access

Once the script is complete, you can access your environment at:
http://localhost/
phpMyAdmin can be accessed at:
http://localhost/phpMyAdmin


---

## Contributions

Contributions and feedback are welcome!

