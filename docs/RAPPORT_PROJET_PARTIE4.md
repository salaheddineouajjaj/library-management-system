# CHAPITRE 4 : ANALYSE ET CONCEPTION

## 4.1 Diagramme de Cas d'Utilisation Global

Le diagramme de cas d'utilisation global présente une vue d'ensemble des interactions entre les différents acteurs et le système.

### 4.1.1 Les Acteurs

**Guest (Visiteur)**
- Rôle : Personne non authentifiée visitant le site
- Actions possibles : Consulter le catalogue, s'inscrire, se connecter

**User (Utilisateur)**
- Rôle : Membre inscrit de la bibliothèque
- Hérite de : Guest
- Actions possibles : Emprunter/retourner des livres, gérer ses bibliothèques, participer au forum, recevoir des recommandations

**Admin (Administrateur)**
- Rôle : Gestionnaire de la bibliothèque
- Hérite de : User
- Actions possibles : Gérer le catalogue, gérer les utilisateurs, consulter les statistiques, modérer le forum

**AI System (Système d'IA)**
- Rôle : Service externe (Google Gemini)
- Actions : Fournir des recommandations personnalisées

### 4.1.2 Cas d'Utilisation Principaux

**Pour les Visiteurs :**
- UC1 : Créer un compte
- UC2 : Se connecter
- UC3 : Parcourir le catalogue

**Pour les Utilisateurs :**
- UC4 : Rechercher des livres
- UC5 : Emprunter un livre
- UC6 : Retourner un livre
- UC7 : Gérer ses bibliothèques personnelles
- UC8 : Consulter l'historique
- UC9 : Recevoir des recommandations IA
- UC10 : Participer au forum

**Pour les Administrateurs :**
- UC11 : Gérer le catalogue (CRUD livres)
- UC12 : Gérer les catégories
- UC13 : Gérer les utilisateurs
- UC14 : Consulter les statistiques
- UC15 : Gérer les emprunts et retards

**Relations :**
- UC5 (Emprunter) <<include>> UC4 (Rechercher)
- UC9 (Recommandations) <<extend>> UC4 (Rechercher)
- UC7 (Bibliothèques) <<include>> UC8 (Historique)

📄 **Fichier UML** : `/docs/uml/01_global_usecase.puml`

## 4.2 Diagramme de Cas d'Utilisation Détaillé : Gestion des Emprunts

Ce diagramme se concentre sur le processus d'emprunt et de retour des livres.

### 4.2.1 Acteurs

- **User** : Emprunte et retourne des livres
- **Admin** : Supervise les emprunts et gère les retards
- **OpenLibrary API** : Fournit des données enrichies sur les livres

### 4.2.2 Cas d'Utilisation

**UC_E1 : Rechercher des livres**
- Acteur : User
- Description : Rechercher par titre, auteur, catégorie

**UC_E2 : Consulter les détails d'un livre**
- Acteur : User
- Include : UC_E1
- Description : Voir informations complètes et disponibilité

**UC_E3 : Emprunter un livre**
- Acteur : User
- Include : UC_E4 (Vérifier disponibilité)
- Description : Créer un nouvel emprunt

**UC_E4 : Vérifier la disponibilité**
- Description : Contrôler qu'il reste des exemplaires

**UC_E5 : Retourner un livre**
- Acteur : User
- Description : Enregistrer le retour et libérer l'exemplaire

**UC_E6 : Prolonger un emprunt**
- Acteur : User
- Extend : UC_E3
- Description : Ajouter 7 jours à l'emprunt en cours

**UC_E7 : Gérer les retards**
- Acteur : Admin
- Description : Identifier et relancer les emprunts en retard

**Règles métier :**
- Un utilisateur ne peut emprunter que si aucun retard en cours
- Maximum 5 emprunts simultanés
- Durée standard : 14 jours
- Prolongation unique de 7 jours

📄 **Fichier UML** : `/docs/uml/02_borrowing_usecase.puml`

## 4.3 Diagramme de Classes

Le diagramme de classes présente la structure complète du système avec 11 classes principales.

### 4.3.1 Classes Principales

**User**
```
Attributs:
- id: int (PK)
- name: string
- email: string (unique)
- password_hash: string
- role: enum(user, admin)
- is_active: boolean
- created_at: datetime

Méthodes:
+ login(): boolean
+ register(): boolean
+ updateProfile(): boolean
+ getBorrowingHistory(): array
```

**Book**
```
Attributs:
- id: int (PK)
- title: string
- author: string
- isbn: string
- category_id: int (FK)
- description: text
- cover_image: string
- total_copies: int
- available_copies: int
- publication_year: int

Méthodes:
+ isAvailable(): boolean
+ borrowBook(): boolean
+ returnBook(): boolean
+ searchBooks(query): array
```

**Borrowing**
```
Attributs:
- id: int (PK)
- user_id: int (FK)
- book_id: int (FK)
- borrow_date: datetime
- due_date: datetime
- return_date: datetime (nullable)
- status: enum(active, returned, overdue)

Méthodes:
+ borrowBook(): boolean
+ returnBook(): boolean
+ extendDueDate(): boolean
+ isOverdue(): boolean
```

**PersonalShelf**
```
Attributs:
- id: int (PK)
- user_id: int (FK)
- name: string
- description: text
- is_public: boolean

Méthodes:
+ createShelf(): boolean
+ addBook(book_id): boolean
+ getBooks(): array
```

### 4.3.2 Relations

- User **1** --- **0..*** Borrowing (Un utilisateur a plusieurs emprunts)
- Book **1** --- **0..*** Borrowing (Un livre a plusieurs emprunts)
- User **1** --- **0..*** PersonalShelf (Un utilisateur a plusieurs étagères)
- Book ***** --- **1** Category (Plusieurs livres par catégorie)
- PersonalShelf **1** --- **0..*** ShelfBook (Une étagère contient plusieurs livres)

📄 **Fichier UML** : `/docs/uml/05_class_diagram.puml`

## 4.4 Diagrammes de Séquence

### 4.4.1 Séquence Globale : Authentification et Emprunt

Ce diagramme montre le flux complet depuis la connexion jusqu'à l'emprunt d'un livre.

**Participants :**
- User (Utilisateur)
- Browser (Navigateur)
- LoginController
- Database
- BookController
- BorrowingController

**Flux :**

1. **Authentification**
   - User saisit email/password → Browser
   - Browser → LoginController : POST /login.php
   - LoginController → Database : SELECT user WHERE email=?
   - Database → LoginController : User data
   - LoginController : Vérification password_verify()
   - LoginController : Démarrage de session
   - LoginController → Browser : Redirect dashboard

2. **Consultation Dashboard**
   - Browser → DashboardController : GET /user/dashboard.php
   - DashboardController → Database : Get statistics
   - Database → DashboardController : Borrowed books, overdue count
   - DashboardController → Browser : Display dashboard

3. **Recherche et Emprunt**
   - User → Browser : Search book
   - Browser → BookController : GET /browse_books.php?search=
   - BookController → Database : SELECT books WHERE title LIKE ?
   - Database → BookController : Book list
   - User → Browser : Click "Borrow"
   - Browser → BorrowingController : POST /borrow_book.php
   - BorrowingController → Database : Check availability
   - BorrowingController → Database : Check user eligibility
   - BorrowingController → Database : INSERT borrowing
   - BorrowingController → Database : UPDATE available_copies
   - BorrowingController → Browser : Success message

📄 **Fichier UML** : `/docs/uml/03_global_sequence.puml`

### 4.4.2 Séquence : Recommandations IA

**Participants :**
- User
- Browser  
- RecommendationController
- Database
- AI_Service
- Gemini_API

**Flux :**

1. **Collecte des Données**
   - User → Browser : Visit recommendations page
   - Browser → RecommendationController
   - RecommendationController → Database : Get borrowing history
   - RecommendationController → Database : Get favorite categories
   - RecommendationController → Database : Get personal shelves

2. **Génération IA**
   - RecommendationController → AI_Service : Generate recommendations
   - AI_Service : Build context from user data
   - AI_Service → Gemini_API : POST with prompt
   - Gemini_API → AI_Service : AI-generated recommendations
   - AI_Service : Parse and validate response

3. **Filtrage et Affichage**
   - AI_Service → Database : Search recommended books
   - Database → AI_Service : Available books
   - AI_Service → RecommendationController : Filtered recommendations
   - RecommendationController → Browser : Display with explanations

📄 **Fichier UML** : `/docs/uml/04_ai_recommendation_sequence.puml`

## 4.5 Diagramme d'Activité : Processus d'Emprunt

Ce diagramme décrit le workflow complet d'emprunt d'un livre avec toutes les règles de validation.

**Étapes :**

1. **Début** : User logs in

2. **Navigation** : User → Browse Books → Enter search

3. **Recherche** : System displays available books

4. **Décision** : Book found?
   - **Non** → Display "No books found" → End
   - **Oui** → Continue

5. **Action** : User clicks "Borrow Book"

6. **Validation 1** : Check book availability
   - **Copies = 0** → Display error "Not available" → End
   - **Copies > 0** → Continue

7. **Validation 2** : Check user eligibility
   - **User has overdue books** → Display error "Return overdue books first" → End
   - **No overdue** → Continue

8. **Validation 3** : Check borrowing limit
   - **≥ 5 active borrowings** → Display error "Limit reached" → End
   - **< 5** → Continue

9. **Transaction** :
   - Calculate due date (borrow_date + 14 days)
   - Create borrowing record
   - Decrease available_copies
   - Update user statistics

10. **Notification** : Send confirmation email

11. **Affichage** : Display success message with due date

12. **Fin** : Update user dashboard

**Règles Métier Appliquées :**
- Disponibilité : available_copies > 0
- Pas de retard en cours : status != 'overdue'
- Limite : COUNT(active borrowings) < 5
- Durée : 14 jours
- Email automatique : Confirmation + rappel J-2

📄 **Fichier UML** : `/docs/uml/06_activity_borrowing.puml`

---
