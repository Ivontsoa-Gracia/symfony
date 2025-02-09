# Utiliser une version PHP compatible (par exemple PHP 8.2)
FROM php:8.2-fpm

# Installer les dépendances requises pour Symfony
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    zlib1g-dev \
    git \
    unzip \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo pdo_mysql

# Installer Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Créer un utilisateur non-root et changer les permissions
RUN useradd -ms /bin/bash symfonyuser
USER symfonyuser
WORKDIR /var/www/html

# Copier les fichiers du projet dans le conteneur
COPY --chown=symfonyuser:symfonyuser . /var/www/html

# Installer les dépendances de Symfony avec Composer
RUN composer install --no-dev --optimize-autoloader

# Exposer le port de l'application
EXPOSE 9000

# Démarrer le serveur PHP intégré
CMD ["php-fpm"]
