FROM debian:12.8

ARG UID
ARG UNAME

RUN apt update && apt upgrade -y

RUN adduser -u ${UID} --disabled-password --gecos "" $UNAME
RUN mkdir /home/$UNAME/.ssh
RUN chown -R $UNAME:$UNAME /home/$UNAME/

RUN apt-get install nginx -y
RUN usermod -a -G www-data $UNAME

COPY ../docker/daily-trends-api.conf /etc/nginx/sites-available/daily-trends-api.conf
RUN ln -s /etc/nginx/sites-available/daily-trends-api.conf /etc/nginx/sites-enabled/daily-trends-api.conf

RUN apt install software-properties-common -y

ENV TZ=Europe/Madrid
RUN apt install tzdata -y

RUN apt install curl -y
RUN dpkg -l | grep php | tee packages.txt
RUN apt install apt-transport-https
RUN curl -sSLo /usr/share/keyrings/deb.sury.org-php.gpg https://packages.sury.org/php/apt.gpg
RUN sh -c 'echo "deb [signed-by=/usr/share/keyrings/deb.sury.org-php.gpg] https://packages.sury.org/php/ $(lsb_release -sc) main" > /etc/apt/sources.list.d/php.list'
RUN apt update

RUN apt install php8.4-fpm php8.4-common php8.4-xml php8.4-xmlrpc php8.4-curl php8.4-gd php8.4-cli php8.4-dev php8.4-imap php8.4-mbstring php8.4-opcache php8.4-soap php8.4-zip php8.4-redis php8.4-intl -y

RUN apt-get install -y libssl-dev pkg-config php-pear php8.4-dev
RUN pecl install mongodb
RUN echo "extension=mongodb.so" > /etc/php/8.4/mods-available/mongodb.ini
RUN phpenmod mongodb

RUN curl -fsSL https://deb.nodesource.com/setup_22.x | bash -
RUN apt install nodejs -y

RUN curl --insecure https://getcomposer.org/composer.phar -o /usr/bin/composer && chmod +x /usr/bin/composer
RUN composer self-update

RUN curl -sS https://get.symfony.com/cli/installer | bash
RUN mv /root/.symfony5/bin/symfony /usr/local/bin/symfony

COPY ../docker/php.ini /etc/php/8.4/fpm/php.ini

WORKDIR /var/www/daily-trends-api

CMD /etc/init.d/php8.4-fpm start && nginx -g "daemon off;"
