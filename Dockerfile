# Image officielle PHP + Apache
FROM php:8.2-apache

# Installer les extensions PHP nécessaires (MySQL, PDO, etc.)
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Copier les fichiers de ton site dans le dossier web
COPY . /var/www/html

# Donner les bons droits à Apache
RUN chown -R www-data:www-data /var/www/html

# Exposer le port HTTP
EXPOSE 80

# Lancer Apache au démarrage
CMD ["apache2-foreground"]
