# 🚀 Projet Symfony - AppFoodBoxApp

## 📖 Description
AppFoodBox est une application permettant de :  
✅ Lister les repas déjà mangés  
⭐ Leur attribuer une note  
👥 Consulter le profil de vos amis  

## 👥 Collaborateur
Rackyrr -> Valentin Lestrelin  
chiyeb -> chiheb bradai  
sandevistan14 -> Robin Lerouge  

## ⚙️ Installation

**1. Clonez le dépôt 🛠️**
   ```bash
   git clone https://github.com/FoodBoxOrg/AppFoodBox
   cd AppFoodBox
   ```

**2. Installation des dépendances 📦**
   ```bash
   composer install
   ```

**3. Build Tailwind 🎨**
   ```bash
   php bin/console tailwind:build
   ```

**4. Générer une migration de base de données 🛠️**
   ```bash
   php bin/console make:migration
   ```

**5. Exécuter les migrations de base de données 🗄️**
   ```bash
   php bin/console doctrine:migrations:migrate
   ```

**6. Charger les données de test 🗂️**
   ```bash
   php bin/console doctrine:fixtures:load
   ```
⚠️ Attention : Cette commande réinitialisera les données existantes en base !

**7. Démarrez le serveur 🚀** 
   ```bash
   symfony serve
   ```

   L'application sera disponible à l'adresse [http://localhost:8000](http://localhost:8000).
