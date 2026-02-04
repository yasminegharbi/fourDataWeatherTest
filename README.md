# Four Data – Weather App (Test Technique)

Application fullstack développée dans le cadre d’un test technique.
Elle permet de consulter la météo via l’API Open-Meteo et de sauvegarder des recherches en favoris.

---

## Stack technique
- Backend : Symfony (PHP 8+), Doctrine ORM, MySQL
- Frontend : Vue.js 3, Vite, Axios
- API externe : Open-Meteo

---

## Architecture
Le projet est structuré en deux parties distinctes :

fourDataTest/
  fourdata-weather-back/ # API Symfony (point d’entrée unique)
  fourdata-weather-front/ # Application Vue 3

  
Le frontend communique uniquement avec le backend, qui se charge des appels à l’API Open-Meteo.

---

## Fonctionnalités
- Recherche météo par ville
- Affichage de la météo actuelle
- Prévisions journalières (températures, précipitations)
- Gestion des favoris :
  - ajout
  - consultation
  - suppression
- Persistance des favoris en base de données

---

## Installation & exécution

### Backend (Symfony)

cd fourdata-weather-back
composer install
# configurer DATABASE_URL dans .env.local
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
symfony serve

# API disponible sur :

http://127.0.0.1:8000/api

### Frontend (Vue 3)
cd fourdata-weather-front
npm install
npm run dev


# Application disponible sur :

http://localhost:5173