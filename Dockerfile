# Utilise l'image officielle PHP avec Apache intégré
FROM php:8.2-apache

# Copie tout le contenu de ton site dans le dossier web d'Apache
COPY . /var/www/html

# Donne les bons droits aux fichiers (optionnel mais recommandé)
RUN chown -R www-data:www-data /var/www/html

# Expose le port 80 (obligatoire pour que Koyeb puisse y accéder)
EXPOSE 80

# Démarre Apache automatiquement
CMD ["apache2-foreground"]
