FROM bitnami/minideb:jessie

#RUN timedatectl set-timezone Etc/UTC

RUN install_packages \
  curl \
  wget \
  git \
  apt-transport-https \
  lsb-release \
  ca-certificates \
  sudo \
  vim \
  netcat

#RUN echo "deb http://packages.dotdeb.org jessie all" >> /etc/apt/sources.list.d/dotdeb.org.list && \
#  echo "deb-src http://packages.dotdeb.org jessie all1" >> /etc/apt/sources.list.d/dotdeb.org.list && \
#  wget -O- http://www.dotdeb.org/dotdeb.gpg | apt-key add -

RUN wget -O /etc/apt/trusted.gpg.d/php.gpg https://packages.sury.org/php/apt.gpg && \
    echo "deb https://packages.sury.org/php/ $(lsb_release -sc) main" > /etc/apt/sources.list.d/php.list

RUN install_packages \
    php7.1-fpm \
    php7.1-cli \
    php7.1-mcrypt \
    php7.1-mysql \
    php7.1-apcu \
    php7.1-gd \
    php7.1-imagick \
    php7.1-curl \
    php7.1-intl \
    php7.1-xml \
    php7.1-zip \
    php7.1-mbstring \
    php7.0-redis

ADD symfony.ini /etc/php/7.1/fpm/conf.d/
ADD symfony.ini /etc/php/7.1/cli/conf.d/

ADD symfony.pool.conf /etc/php/7.1/fpm/pool.d/

RUN rm -f /etc/php/7.1/fpm/pool.d/www.conf
RUN sed -i "/pid = .*/c\;pid = /run/php/php7.0-fpm.pid" /etc/php/7.1/fpm/php-fpm.conf \
    && sed -i "/;daemonize = .*/c\daemonize = no" /etc/php/7.1/fpm/php-fpm.conf \
    && sed -i "/error_log = .*/c\error_log = /proc/self/fd/2" /etc/php/7.1/fpm/php-fpm.conf \
    && usermod -u 1000 www-data

RUN curl -s https://deb.nodesource.com/setup_8.x | bash
RUN apt install nodejs -y
RUN npm install -g less webpack

RUN mkdir -p /var/www
RUN chown -R www-data:1000 /var/www

ADD sfconsole /usr/bin/
ADD composer /usr/bin/
ADD phpunit /usr/bin/

CMD ["/usr/sbin/php-fpm7.1"]

EXPOSE 9000
