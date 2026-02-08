# CHAPITRE 5 : RÉALISATION

## 5.1 Environnement de Développement

### 5.1.1 Technologies Frontend

**HTML5 & CSS3**
- Structuration sémantique des pages
- CSS moderne avec variables CSS (custom properties)
- Flexbox et Grid pour les layouts responsives
- Animations et transitions CSS

**JavaScript (Vanilla)**
- Pas de framework lourd pour optimiser les performances
- Interactions dynamiques (modals, dropdowns, accordions)
- Validation côté client des formulaires
- AJAX pour certaines requêtes asynchrones

**Typographies**
- **Crimson Text** : Police serif élégante pour les titres
- **Lora** : Police serif lisible pour le corps de texte
- Import via Google Fonts

**Th

ème Visuel : "Bibliothèque Classique"**
- Palette de couleurs chaudes (parchmin, bois, brass)
- Backgrounds texturés évoquant le papier ancien
- Ombres douces pour la profondeur
- Icons et émojis pour l'ergonomie

### 5.1.2 Technologies Backend

**PHP 7.4+**
- Langage serveur principal
- Architecture MVC (Modèle-Vue-Contrôleur)
- POO (Programmation Orientée Objet)
- Namespaces et autoloading

**Base de Données : MySQL 5.7+ / MariaDB 10.3+**
- SGBD relationnel robuste
- Connexion via PDO (PHP Data Objects)
- Requêtes préparées pour la sécurité
- Transactions pour l'intégrité des données

**Serveur : Apache 2.4+ (via XAMPP)**
- Module mod_rewrite pour les URLs propres
- Configuration .htaccess
- Support HTTPS (certificat SSL)
- Gestion des sessions PHP

### 5.1.3 APIs Externes

**OpenLibrary API**
- URL : `https://openlibrary.org/api/`
- Usage : Enrichissement automatique du catalogue
- Données récupérées : Titre, auteur, description, couverture, ISBN
- Format : JSON
- Rate limit : Pas de limite stricte

**Google Gemini AI API**
- Usage : Génération de recommandations personnalisées
- Modèle : `gemini-pro`
- Format : JSON
- Authentification : API Key

### 5.1.4 Outils de Développement

**Éditeur de Code**
- Visual Studio Code
- Extensions : PHP Intelephense, ESLint, Prettier

**Gestion de Version**
- Git 2.40+
- GitHub pour le repository distant
- Branches : `main`, `develop`, `feature/*`

**Base de Données**
- phpMyAdmin pour la gestion visuelle
- MySQL Workbench pour la modélisation

**Autres Outils**
- Postman : Tests d'API
- Browser DevTools : Débogage frontend
- PlantUML : Génération de diagrammes UML

### 5.1.5 Déploiement

**Environnement de Production**
- Hébergeur : GitHub Pages (frontend) + VPS/Hosting PHP
- Serveur : Apache/Nginx
- PHP-FPM pour les performances
- Base de données : MySQL distant

**CI/CD**
- GitHub Actions pour l'automatisation
- Tests automatiques avant déploiement
- Déploiement automatique sur push `main`

## 5.2 Architecture du Système

### 5.2.1 Architecture Globale

Le système suit une architecture **3-tiers (trois couches)** :

```
┌─────────────────────────────────────────┐
│         COUCHE PRÉSENTATION             │
│   (Frontend - HTML/CSS/JavaScript)      │
│                                         │
│  - Pages web                            │
│  - Formulaires                          │
│  - Interface utilisateur                │
└────────────────┬────────────────────────┘
                 │ HTTP Requests
                 ▼
┌─────────────────────────────────────────┐
│         COUCHE MÉTIER (Backend)         │
│          (PHP - MVC Pattern)            │
│                                         │
│  Controllers:                           │
│   - LoginController                     │
│   - BookController                      │
│   - BorrowingController                 │
│   - ShelfController                     │
│                                         │
│  Models:                                │
│   - User, Book, Borrowing, etc.         │
│                                         │
│  Services:                              │
│   - AI Recommendation Service           │
│   - OpenLibrary API Service             │
└────────────────┬────────────────────────┘
                 │ SQL Queries
                 ▼
┌─────────────────────────────────────────┐
│         COUCHE DONNÉES                  │
│          (MySQL Database)               │
│                                         │
│  Tables:                                │
│   - users, books, categories            │
│   - borrowings, personal_shelves        │
│   - forum_posts, messages               │
└─────────────────────────────────────────┘
```

### 5.2.2 Pattern MVC (Modèle-Vue-Contrôleur)

**Modèle (Model)**
- Représente les données et la logique métier
- Interaction avec la base de données
- Validation des données
- Fichiers : `/models/*.php`

Exemple :
```php
class Book {
    public static function findById($id) {
        // Logique de récupération
    }
    public static function search($query) {
        // Logique de recherche
    }
}
```

**Vue (View)**
- Présentation des données à l'utilisateur
- Templates HTML avec PHP intégré
- Séparation de la présentation et de la logique
- Fichiers : `/user/*.php`, `/admin/*.php`

**Contrôleur (Controller)**  
- Gère les requêtes utilisateur
- Fait le lien entre Modèle et Vue
- Contrôle les accès et permissions
- Fichiers : logic dans chaque page PHP

### 5.2.3 Organisation des Dossiers

```
v1/
├── admin/                  # Pages administration
│   ├── dashboard.php
│   ├── manage_books.php
│   └── manage_users.php
├── user/                   # Pages utilisateurs
│   ├── dashboard.php
│   ├── browse_books.php
│   ├── my_library.php
│   └── recommendations.php
├── config/                 # Configuration
│   ├── db.php             # Connexion BDD
│   └── ai_config.php      # Config API IA
├── includes/              # Fichiers communs
│   ├── header.php
│   ├── footer.php
│   ├── auth_check.php
│   └── nav_user.php
├── css/                   # Feuilles de style
│   ├── style.css
│   ├── library-theme.css
│   ├── navigation.css
│   └── auth-library.css
├── js/                    # Scripts JavaScript
├── uploads/               # Images uploadées
│   └── covers/           # Couvertures de livres
├── docs/                  # Documentation
│   ├── uml/              # Diagrammes UML
│   └── RAPPORT_*.md      # Rapports
├── login.php             # Pages publiques
├── register.php
├── index.php
└── .gitignore
```

### 5.2.4 Schéma de Base de Données

**Tables Principales :**

1. **users** (Utilisateurs)
   - id (PK), name, email (unique), password_hash
   - role, is_active, created_at, updated_at

2. **books** (Livres)
   - id (PK), title, author, isbn
   - category_id (FK), description, cover_image
   - total_copies, available_copies, publication_year

3. **categories** (Catégories)
   - id (PK), name, description, created_at

4. **borrowings** (Emprunts)
   - id (PK), user_id (FK), book_id (FK)
   - borrow_date, due_date, return_date
   - status, created_at

5. **personal_shelves** (Bibliothèques Personnelles)
   - id (PK), user_id (FK), name
   - description, is_public, created_at

6. **shelf_books** (Livres dans les Bibliothèques)
   - id (PK), shelf_id (FK), book_id (FK)
   - added_at, notes

7. **forum_posts** (Posts Forum)
   - id (PK), user_id (FK), title
   - content, category, views, created_at

8. **forum_comments** (Commentaires)
   - id (PK), post_id (FK), user_id (FK)
   - content, created_at

9. **messages** (Messagerie)
   - id (PK), sender_id (FK), receiver_id (FK)
   - subject, message, is_read, created_at

**Relations :**
- users 1→N borrowings
- books 1→N borrowings
- books N→1 categories
- users 1→N personal_shelves
- personal_shelves 1→N shelf_books
- books 1→N shelf_books

## 5.3 Fonctionnalités Implémentées

### 5.3.1 Module d'Authentification et Gestion des Utilisateurs

**Inscription (register.php)**
- Formulaire : nom, email, mot de passe, confirmation
- Validations :
  - Email unique (vérification en BDD)
  - Mot de passe ≥ 6 caractères
  - Correspondance password/confirmation
- Hashage : `password_hash()` avec bcrypt (cost 12)
- Redirection vers login après succès

**Connexion (login.php)**
- Formulaire : email, mot de passe
- Vérification : `password_verify()`
- Création de session : `$_SESSION['user']`
- Redirection selon rôle :
  - Admin → `/admin/dashboard.php`
  - User → `/user/dashboard.php`
- Protection brute-force (future : rate limiting)

**Gestion du Profil**
- Modification nom, email, mot de passe
- Upload d'avatar (future feature)
- Historique de lecture

**Contrôle d'Accès**
- Middleware : `includes/auth_check.php`
- Vérification de session sur chaque page protégée
- RBAC : distinction user/admin
- Redirection vers login si non authentifié

### 5.3.2 Module de Gestion du Catalogue

**Ajout de Livre (Admin)**
- Formulaire complet : titre, auteur, ISBN, catégorie, description, année
- Upload de couverture : `/uploads/covers/`
- Validation :
  - Champs obligatoires
  - ISBN unique
  - Format image (JPG, PNG)
- Insertion en BDD

**Import depuis OpenLibrary API**
- Saisie ISBN
- Requête : `GET https://openlibrary.org/api/books?bibkeys=ISBN:{isbn}`
- Parsing JSON
- Pré-remplissage formulaire
- Téléchargement automatique de la couverture

**Recherche Avancée**
- Barre de recherche globale
- Filtres :
  - Par titre (LIKE %query%)
  - Par auteur
  - Par catégorie (dropdown)
  - Par disponibilité (available_copies > 0)
- Tri : Pertinence, Date, Popularité
- Pagination : 20 résultats/page
- Affichage : Card avec couverture, titre, auteur, disponibilité

**Détails du Livre**
- Page dédiée : `/user/book_details.php?id=`
- Affichage complet :
  - Couverture grand format
  - Informations complètes
  - Nombre d'exemplaires disponibles/total
  - Bouton "Emprunter" (si disponible et connecté)
  - Bouton "Ajouter à ma bibliothèque"

**Modification/Suppression (Admin)**
- Édition de tous les champs
- Soft delete : is_deleted=1
- Conservation de l'historique

### 5.3.3 Module de Gestion des Emprunts

**Emprunter un Livre**
Workflow complet :

1. **Vérifications** :
   ```php
   // Disponibilité
   if ($book['available_copies'] <= 0) {
       return error("Livre non disponible");
   }
   
   // Pas de retard
   $overdueCount = getOverdueCount($user_id);
   if ($overdueCount > 0) {
       return error("Retournez vos livres en retard");
   }
   
   // Limite emprunts
   $activeCount = getActiveBorrowingsCount($user_id);
   if ($activeCount >= 5) {
       return error("Limite de 5 emprunts atteinte");
   }
   ```

2. **Transaction** :
   ```php
   DB::beginTransaction();
   
   // Créer emprunt
   $due_date = date('Y-m-d', strtotime('+14 days'));
   INSERT INTO borrowings (user_id, book_id, due_date, status)
   
   // Décrémenter copies
   UPDATE books SET available_copies = available_copies - 1
   
   DB::commit();
   ```

3. **Notification** :
   - Email de confirmation
   - Affichage dans "Mes emprunts"

**Retourner un Livre**
- Enregistrement date de retour
- Calcul pénalité si retard
- Incrémentation available_copies
- Mise à jour statut : 'returned'
- Ajout à l'historique

**Prolonger un Emprunt**
- Vérification : prolongé=false
- Ajout de 7 jours à due_date
- Marquage : prolongé=true
- Une seule prolongation autorisée

**Dashboard "Mes Emprunts"**
- Liste emprunts actifs
- Affichage :
  - Couverture du livre
  - Titre, auteur
  - Date d'emprunt
  - Date de retour (rouge si retard)
  - Boutons : Prolonger, Retourner
- Statistiques :
  - Emprunts en cours : X/5
  - Livres en retard : X

### 5.3.4 Module Bibliothèques Personnelles

**Création d'Étagère**
- Formulaire : nom, description
- Choix visibilité : publique/privée
- Validation nom unique par utilisateur

**Ajout de Livres**
- Bouton "+" sur chaque fiche livre
- Sélection de l'étagère de destination
- Note personnelle optionnelle
- Vérification : pas de doublon

**Consultation**
- Page `/user/my_library.php`
- Liste des étagères créées
- Clic sur étagère → liste des livres
- Affichage :
  - Couvertures
  - Mes notes
  - Date d'ajout
- Actions : Retirer, Modifier note

**Organisation**
- Drag & drop pour réorganiser (future)
- Tri : Date, Titre, Auteur
- Recherche dans la bibliothèque

### 5.3.5 Module Recommandations IA

**Collecte du Contexte Utilisateur**
```php
// Historique de lecture
$history = getBorrowingHistory($user_id);

// Catégories préférées
$favoriteCategories = getFavoriteCategories($user_id);

// Bibliothèques personnelles
$shelves = getPersonalShelves($user_id);
```

**Construction du Prompt**
```php
$prompt = "Tu es un bibliothécaire expert. 
Basé sur l'historique de lecture suivant :
- {liste des livres empruntés}

Et les catégories préférées :
- {liste des catégories}

Recommande 5 livres similaires disponibles dans notre catalogue.
Pour chaque livre, explique pourquoi tu le recommandes.";
```

**Appel API Gemini**
```php
$apiKey = AI_API_KEY;
$url = "https://generativelanguage.googleapis.com/v1/models/gemini-pro:generateContent";

$response = callGeminiAPI($prompt, $apiKey);
$recommendations = parseRecommendations($response);
```

**Filtrage et Affichage**
- Extraction des titres recommandés
- Recherche en BDD locale
- Filtrage : livres disponibles uniquement
- Affichage :
  - Couverture
  - Titre, auteur
  - Raison de la recommandation (IA)
  - Bouton "Emprunter" ou "Ajouter à bibliothèque"

### 5.3.6 Module Forum et Communication

**Forum**
- Création de posts : titre, contenu, catégorie
- Commentaires : réponses aux posts
- Vues : compteur d'affichage
- Modération admin : épingler, supprimer

**Messagerie Interne**
- Envoi message : destinataire, sujet, contenu
- Boîte de réception
- Marquage lu/non lu
- Notifications

### 5.3.7 Module Administration

**Dashboard Admin**
Statistiques affichées :
- Nombre total de livres
- Nombre d'utilisateurs actifs
- Emprunts en cours
- Emprunts en retard
- Top 10 livres les plus empruntés
- Graphique d'activité (future : Chart.js)

**Gestion des Retards**
- Détection automatique : `due_date < CURDATE()`
- Liste :
  - Nom utilisateur
  - Livre
  - Jours de retard
- Actions :
  - Envoyer rappel email
  - Bloquer compte (désactiver)
  - Marquer comme retourné

**Gestion des Utilisateurs**
- Liste complète
- Filtres : actifs, désactivés, rôle
- Actions :
  - Activer/Désactiver
  - Changer rôle (user ↔ admin)
  - Voir emprunts de l'utilisateur

**Rapports**
- Sélection période (mois, année)
- Génération PDF (future : TCPDF/DomPDF)
- Données :
  - Total emprunts
  - Taux de retour à temps
  - Livres populaires
  - Utilisateurs actifs

---
