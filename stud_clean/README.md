# Projet stud_clean
## Installation du Projet
#### Pour faire fonctionner ce projet sur votre machine, vous devez commencer par installer les dépendances du projet :

```
composer install
npm i
```

Vous devez ensuite créer le fichier .env.local et saisir les informations de connexion à la base de données (ces données peuvent varier en fonction de votre environnement de développement) :

```
DATABASE_URL="mysql://root:@127.0.0.1:3306/stud_clean_db?serverVersion=5.7&charset=utf8mb4"
```

Vous devez ensuite créer la base de données et les tables et charger les fixtures :
```
php bin/console doctrine:database:drop --force
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
php bin/console doctrine:fixtures:load
```

Vous pouvez ensuite lancer le serveur de développement et la compilation des assets :

```
npm run watch
npm run server
```