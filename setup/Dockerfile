# Dockerfile

FROM ubuntu:20.04
LABEL Maintainer="Michele Damico <michele.damico@linfaservice.it>"
LABEL Description="spid-php"

ENV DEBIAN_FRONTEND=noninteractive
ENV DEBCONF_NOWARNINGS=yes 

# Install apache and php7
RUN \
    apt-get update && \
    apt-get -yq install \
        curl \
        git \
        apache2 \
        libapache2-mod-php \
        php-sqlite3 \
        php-gmp \
        php-mbstring \
        php-curl \
        php-xml \
        php-zip
    
# Apache conf - set default port to 443
RUN sed -i  's/:443/:443/g' /etc/apache2/sites-available/000-default.conf
RUN sed -i  's/ 443/ 443/g' /etc/apache2/ports.conf

# Apache conf - allow .htaccess with RewriteEngine
RUN a2enmod rewrite
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

# Apache conf - autorise .htaccess files
RUN sed -i '/<Directory \/var\/www\/>/,/<\/Directory>/ s/AllowOverride None/AllowOverride All/' /etc/apache2/apache2.conf
# for production :
RUN echo "ServerTokens Prod\n" >> /etc/apache2/apache2.conf
RUN echo "ServerSignature Off\n" >> /etc/apache2/apache2.conf

# Apache conf - enable SSL
RUN a2enmod ssl
RUN echo "SSLEngine on" >> /etc/apache2/apache2.conf
RUN echo "SSLCertificateFile /home/spid-php/cert/spid-sp.crt" >> /etc/apache2/apache2.conf
RUN echo "SSLCertificateKeyFile /home/spid-php/cert/spid-sp.pem" >> /etc/apache2/apache2.conf

# Apache conf - SAML endpoint
RUN echo "Redirect '/acs' '/spid/module.php/saml/sp/saml2-acs.php/service'" >> /etc/apache2/apache2.conf
RUN echo "Redirect '/slo' '/spid/module.php/saml/sp/saml2-logout.php/service'" >> /etc/apache2/apache2.conf
RUN echo "Redirect '/metadata.xml' '/spid/module.php/saml/sp/metadata.php/service'" >> /etc/apache2/apache2.conf

# Apache custom log
RUN echo "CustomLog /home/access.log combined" >> /etc/apache2/apache2.conf

# Install Composer
RUN curl -sS https://getcomposer.org/installer -o /tmp/composer-setup.php && \
    php /tmp/composer-setup.php --install-dir=/usr/local/bin --filename=composer

# Prepare spid-php
RUN cd /home && \
    git clone https://github.com/italia/spid-php.git

# Check OpenSSL Version
RUN openssl version

# Setup spid-php
# RUN cd /home/spid-php && composer install

# Cleaning
# RUN apt-get clean && apt-get autoremove -y

#change working directory to root
WORKDIR /home/spid-php

# Set permissions
RUN chown -R www-data:www-data /var/www
RUN chmod 755 -R /var/www
RUN find /var/www -type d -exec chmod 775 {} +
RUN find /var/www -type f -exec chmod 664 {} +

EXPOSE 80

ENTRYPOINT ["/bin/sh", "-c", " \
            cp /home/config/* /home/spid-php/ && \
            echo \"Assure that I exist, to delete me\" > /var/www/html/index.html && \
            rm /var/www/html/index.html && \
            echo \"<?php header('Location: metadata.xml'); ?>\" > /var/www/html/index.php && \
            cd /home/spid-php && composer uninstall && composer install && \
            apache2ctl start && \
            echo Web server started && \
            echo && \
            echo SETUP COMPLETED && \
            echo && \
            echo Listening access log... && \
            tail -f /home/access.log"]