# CHAPITRE 3 : MÉTHODOLOGIE DE GESTION DE PROJET

## 3.1 Pourquoi la Méthodologie Agile Scrum

Pour la réalisation de ce projet, nous avons opté pour la méthodologie Agile, et plus spécifiquement le framework Scrum. Ce choix s'est imposé pour plusieurs raisons fondamentales.

### 3.1.1 Adéquation avec la Nature du Projet

Le développement d'un système de gestion de bibliothèque présente des caractéristiques qui se prêtent particulièrement bien à l'approche Agile :

**Évolution des besoins**
Les retours d'expérience des futurs utilisateurs (bibliothécaires et lecteurs) ont nécessité des ajustements réguliers des fonctionnalités. L'approche itérative de Scrum permet ces adaptations sans remettre en cause l'ensemble du projet.

**Livraisons progressives**
Plutôt que d'attendre la fin du développement pour avoir un produit fonctionnel, Scrum permet de livrer des incréments utilisables à la fin de chaque sprint, permettant une validation continue.

**Complexité technique**
L'intégration d'APIs externes (OpenLibrary, Google Gemini) et le développement de fonctionnalités avancées (recommandations IA) nécessitent une approche flexible pour gérer les risques techniques.

### 3.1.2 Avantages de Scrum pour notre Contexte

**Feedback rapide**
Les démonstrations en fin de sprint permettent de recueillir rapidement les retours et d'ajuster le tir avant d'aller trop loin dans une mauvaise direction.

**Priorisation continue**
Le Product Backlog permet de réorganiser les priorités en fonction de la valeur métier et des contraintes découvertes en cours de route.

**Transparence et visibilité**
Les cérémonies Scrum (daily, planning, review, retrospective) assurent une communication constante avec les parties prenantes.

**Gestion des risques**
L'approche itérative permet de détecter et corriger rapidement les problèmes avant qu'ils ne deviennent critiques.

### 3.1.3 Adaptations au Contexte Académique

Bien que Scrum soit conçu pour des équipes de 5-9 personnes, nous l'avons adapté à notre contexte de projet académique :

- **Durée des sprints** : 2 semaines (au lieu de 1-4 semaines standard)
- **Cérémonies simplifiées** : Daily stand-up 2 fois par semaine avec l'encadrant
- **Product Owner** : Rôle joué par l'encadrant académique
- **Scrum Master** : Rôle partagé au sein de l'équipe de développement

## 3.2 Rôles Scrum

Dans notre projet, les rôles Scrum ont été distribués de la manière suivante :

### 3.2.1 Product Owner (PO)

**Identité** : [Nom de l'encadrant]  
**Responsabilités** :
- Définir la vision du produit
- Maintenir et prioriser le Product Backlog
- Valider les incréments produits à chaque sprint
- Définir les critères d'acceptance des User Stories
- Représenter les besoins des utilisateurs finaux

**Actions concrètes** :
- Organisation de sessions de travail hebdomadaires
- Validation des maquettes et interfaces
- Arbitrage sur les choix fonctionnels
- Validation des sprints lors des Sprint Reviews

### 3.2.2 Scrum Master (SM)

**Identité** : [Nom du chef de projet dans l'équipe]  
**Responsabilités** :
- Faciliter les cérémonies Scrum
- Éliminer les obstacles rencontrés par l'équipe
- Protéger l'équipe des perturbations externes
- Promouvoir les pratiques Agiles
- Animer les retrospectives

**Actions concrètes** :
- Organisation des sprint plannings
- Animation des retrospectives
- Suivi de la vélocité de l'équipe
- Gestion des risques et des blocages
- Communication avec le Product Owner

### 3.2.3 Équipe de Développement

**Composition** : [Noms des membres de l'équipe]  
**Responsabilités** :
- Développer les fonctionnalités du backlog
- Auto-organisation du travail
- Garantir la qualité du code (tests, revues)
- Estimer la complexité des User Stories
- Livrer un incrément potentiellement livrable

**Répartition des tâches** :
- **Frontend** : Développement des interfaces utilisateur (HTML/CSS/JavaScript)
- **Backend** : Développement de la logique métier (PHP)
- **Base de données** : Conception et optimisation du schéma
- **Tests** : Validation fonctionnelle et tests utilisateurs
- **DevOps** : Déploiement et intégration continue

## 3.3 Product Backlog

Le Product Backlog constitue la liste priorisée de toutes les fonctionnalités à développer. Il est organisé en **Epics** (grandes fonctionnalités) décomposés en **User Stories** (fonctionnalités utilisateur).

### Notation des User Stories

Chaque User Story suit le format standard :
```
En tant que [rôle],
Je veux [action/fonctionnalité],
Afin de [bénéfice/valeur métier].
```

Et inclut :
- **Critères d'acceptance** : Conditions de validation
- **Estimation** : Complexité en Story Points (échelle de Fibonacci : 1, 2, 3, 5, 8, 13)
- **Priorité** : Haute, Moyenne, Basse

---

### EPIC 1 : GESTION DES UTILISATEURS

#### US1.1 : Inscription d'un Nouvel Utilisateur
**En tant que** visiteur,  
**Je veux** créer un compte utilisateur,  
**Afin de** pouvoir accéder aux services de la bibliothèque.

**Critères d'acceptance** :
- Email unique dans la base
- Mot de passe d'au moins 6 caractères
- Hashage sécurisé du mot de passe (bcrypt)
- Message de confirmation affiché
- Redirection vers la page de connexion

**Estimation** : 5 points  
**Priorité** : Haute

---

#### US1.2 : Connexion au Système
**En tant qu'** utilisateur enregistré,  
**Je veux** me connecter avec mon email et mot de passe,  
**Afin d'** accéder à mon compte personnel.

**Critères d'acceptance** :
- Vérification des identifiants en base de données
- Démarrage de session sécurisée
- Redirection vers le dashboard selon le rôle (user/admin)
- Message d'erreur si identifiants incorrects
- Protection contre le brute-force

**Estimation** : 3 points  
**Priorité** : Haute

---

#### US1.3 : Gestion du Profil Utilisateur
**En tant qu'** utilisateur connecté,  
**Je veux** modifier mes informations personnelles,  
**Afin de** maintenir mon profil à jour.

**Critères d'acceptance** :
- Modification du nom
- Modification de l'email (avec vérification d'unicité)
- Changement de mot de passe (avec confirmation)
- Validation des données avant enregistrement
- Message de confirmation de mise à jour

**Estimation** : 5 points  
**Priorité** : Moyenne

---

#### US1.4 : Déconnexion Sécurisée
**En tant qu'** utilisateur connecté,  
**Je veux** me déconnecter du système,  
**Afin de** sécuriser mon compte.

**Critères d'acceptance** :
- Destruction de la session
- Redirection vers la page de connexion
- Impossibilité d'accéder aux pages protégées après déconnexion

**Estimation** : 2 points  
**Priorité** : Haute

---

### EPIC 2 : GESTION DU CATALOGUE

#### US2.1 : Ajouter un Livre Manuellement (Admin)
**En tant qu'** administrateur,  
**Je veux** ajouter un nouveau livre au catalogue,  
**Afin d'** enrichir l'offre de la bibliothèque.

**Critères d'acceptance** :
- Formulaire avec champs : titre, auteur, ISBN, catégorie, description, année de publication
- Upload de la couverture du livre
- Validation des champs obligatoires
- Enregistrement en base de données
- Affichage du livre dans le catalogue

**Estimation** : 8 points  
**Priorité** : Haute

---

#### US2.2 : Importer un Livre depuis OpenLibrary API (Admin)
**En tant qu'** administrateur,  
**Je veux** importer les données d'un livre depuis OpenLibrary via ISBN,  
**Afin de** gagner du temps lors de l'ajout de nouveaux ouvrages.

**Critères d'acceptance** :
- Saisie de l'ISBN
- Appel à l'API OpenLibrary
- Récupération automatique : titre, auteur, description, couverture
- Pré-remplissage du formulaire
- Possibilité de modifier avant enregistrement

**Estimation** : 13 points  
**Priorité** : Moyenne

---

#### US2.3 : Rechercher des Livres
**En tant qu'** utilisateur,  
**Je veux** rechercher des livres par titre, auteur ou catégorie,  
**Afin de** trouver rapidement l'ouvrage souhaité.

**Critères d'acceptance** :
- Barre de recherche accessible sur toutes les pages
- Recherche par mots-clés (titre, auteur)
- Filtres par catégorie, disponibilité
- Affichage des résultats avec couverture, titre, auteur, disponibilité
- Pagination des résultats (20 par page)

**Estimation** : 8 points  
**Priorité** : Haute

---

#### US2.4 : Consulter les Détails d'un Livre
**En tant qu'** utilisateur,  
**Je veux** voir toutes les informations d'un livre,  
**Afin de** décider si je veux l'emprunter.

**Critères d'acceptance** :
- Affichage de la couverture grand format
- Titre, auteur, ISBN, année de publication
- Description complète
- Catégorie
- Nombre d'exemplaires disponibles/total
- Bouton "Emprunter" si disponible

**Estimation** : 5 points  
**Priorité** : Haute

---

#### US2.5 : Modifier un Livre (Admin)
**En tant qu'** administrateur,  
**Je veux** modifier les informations d'un livre,  
**Afin de** corriger des erreurs ou mettre à jour les données.

**Critères d'acceptance** :
- Formulaire pré-rempli avec les données actuelles
- Modification de tous les champs
- Validation avant enregistrement
- Mise à jour en base de données
- Message de confirmation

**Estimation** : 5 points  
**Priorité** : Moyenne

---

#### US2.6 : Supprimer un Livre (Admin)
**En tant qu'** administrateur,  
**Je veux** supprimer un livre du catalogue,  
**Afin de** retirer les ouvrages obsolètes ou endommagés.

**Critères d'acceptance** :
- Vérification qu'aucun emprunt n'est en cours
- Demande de confirmation avant suppression
- Soft delete (marquage comme supprimé, pas de suppression physique)
- Livre non visible dans les recherches

**Estimation** : 5 points  
**Priorité** : Basse

---

### EPIC 3 : GESTION DES EMPRUNTS

#### US3.1 : Emprunter un Livre
**En tant qu'** utilisateur,  
**Je veux** emprunter un livre disponible,  
**Afin de** pouvoir le lire.

**Critères d'acceptance** :
- Vérification de la disponibilité (copies > 0)
- Vérification que l'utilisateur n'a pas de livres en retard
- Limite de 5 emprunts simultanés par utilisateur
- Création de l'emprunt avec date de retour (J+14)
- Décrémentation du nombre d'exemplaires disponibles
- Email de confirmation envoyé
- Affichage dans "Mes emprunts"

**Estimation** : 13 points  
**Priorité** : Haute

---

#### US3.2 : Consulter Mes Emprunts en Cours
**En tant qu'** utilisateur,  
**Je veux** voir la liste de mes emprunts actuels,  
**Afin de** connaître les dates de retour.

**Critères d'acceptance** :
- Liste des livres empruntés
- Date d'emprunt et date de retour pour chaque livre
- Indication visuelle si retard (texte rouge)
- Bouton "Prolonger" si éligible
- Nombre d'emprunts en cours affiché

**Estimation** : 5 points  
**Priorité** : Haute

---

#### US3.3 : Retourner un Livre
**En tant qu'** utilisateur,  
**Je veux** retourner un livre emprunté,  
**Afin de** libérer un emplacement dans ma bibliothèque.

**Critères d'acceptance** :
- Bouton "Retourner" sur chaque emprunt actif
- Enregistrement de la date de retour effective
- Incrémentation du nombre d'exemplaires disponibles
- Calcul d'éventuelles pénalités si retard
- Suppression de l'emprunt de la liste active
- Ajout à l'historique

**Estimation** : 8 points  
**Priorité** : Haute

---

#### US3.4 : Prolonger un Emprunt
**En tant qu'** utilisateur,  
**Je veux** prolonger la durée de mon emprunt,  
**Afin de** disposer de plus de temps pour lire.

**Critères d'acceptance** :
- Prolongation possible une seule fois
- Ajout de 7 jours à la date de retour
- Vérification qu'aucune réservation n'existe
- Mise à jour de la date en base
- Message de confirmation avec nouvelle date

**Estimation** : 5 points  
**Priorité** : Moyenne

---

#### US3.5 : Consulter l'Historique de Lecture
**En tant qu'** utilisateur,  
**Je veux** voir l'historique complet de mes emprunts,  
**Afin de** retrouver les livres que j'ai lus.

**Critères d'acceptance** :
- Liste chronologique des emprunts passés
- Pour chaque emprunt : livre, date d'emprunt, date de retour
- Filtrage par année ou catégorie
- Pagination des résultats
- Export possible en PDF

**Estimation** : 8 points  
**Priorité** : Basse

---

#### US3.6 : Gérer les Retards (Admin)
**En tant qu'** administrateur,  
**Je veux** voir tous les emprunts en retard,  
**Afin de** relancer les utilisateurs concernés.

**Critères d'acceptance** :
- Liste filtrée des emprunts avec date de retour dépassée
- Nom de l'utilisateur, livre, nombre de jours de retard
- Bouton "Envoyer rappel" pour chaque emprunt
- Possibilité d'envoyer des rappels groupés
- Historique des rappels envoyés

**Estimation** : 8 points  
**Priorité** : Moyenne

---

### EPIC 4 : BIBLIOTHÈQUES PERSONNELLES

#### US4.1 : Créer une Bibliothèque Personnelle
**En tant qu'** utilisateur,  
**Je veux** créer une bibliothèque thématique,  
**Afin d'** organiser mes livres favoris.

**Critères d'acceptance** :
- Formulaire avec nom et description
- Choix de visibilité (publique/privée)
- Validation du nom (non vide, unique)
- Création en base de données
- Affichage dans "Mes bibliothèques"

**Estimation** : 5 points  
**Priorité** : Moyenne

---

#### US4.2 : Ajouter un Livre à une Bibliothèque
**En tant qu'** utilisateur,  
**Je veux** ajouter un livre à l'une de mes bibliothèques,  
**Afin de** le retrouver facilement.

**Critères d'acceptance** :
- Bouton "Ajouter à bibliothèque" sur les fiches livres
- Sélection de la bibliothèque de destination
- Possibilité d'ajouter une note personnelle
- Vérification de non-duplication
- Message de confirmation

**Estimation** : 8 points  
**Priorité** : Moyenne

---

#### US4.3 : Consulter une Bibliothèque
**En tant qu'** utilisateur,  
**Je veux** voir le contenu d'une de mes bibliothèques,  
**Afin de** retrouver les livres que j'y ai placés.

**Critères d'acceptance** :
- Liste des livres présents
- Affichage de la couverture, titre, auteur
- Affichage de mes notes personnelles
- Tri possible (date d'ajout, titre, auteur)
- Bouton "Retirer de la bibliothèque"

**Estimation** : 5 points  
**Priorité** : Moyenne

---

### EPIC 5 : RECOMMANDATIONS IA

#### US5.1 : Générer des Recommandations Personnalisées
**En tant qu'** utilisateur,  
**Je veux** recevoir des suggestions de livres basées sur mes lectures,  
**Afin de** découvrir de nouveaux ouvrages adaptés à mes goûts.

**Critères d'acceptance** :
- Analyse de mon historique de lecture
- Analyse de mes bibliothèques personnelles
- Appel à l'API Google Gemini avec contexte utilisateur
- Affichage de 5-10 recommandations
- Pour chaque recommandation : titre, auteur, raison de la suggestion
- Bouton "Ajouter à ma bibliothèque"

**Estimation** : 21 points  
**Priorité** : Moyenne

---

#### US5.2 : Affiner les Recommandations
**En tant qu'** utilisateur,  
**Je veux** indiquer mes préférences de lecture,  
**Afin d'** améliorer la qualité des recommandations.

**Critères d'acceptance** :
- Sélection de catégories préférées
- Exclusion de catégories non souhaitées
- Sauvegarde des préférences
- Prise en compte dans la génération future

**Estimation** : 8 points  
**Priorité** : Basse

---

### EPIC 6 : COMMUNICATION ET FORUMS

#### US6.1 : Créer une Discussion sur le Forum
**En tant qu'** utilisateur,  
**Je veux** créer un sujet de discussion,  
**Afin de** échanger avec d'autres lecteurs.

**Critères d'acceptance** :
- Formulaire avec titre et contenu
- Sélection d'une catégorie
- Validation des champs
- Publication du post
- Notification aux abonnés de la catégorie

**Estimation** : 8 points  
**Priorité** : Basse

---

#### US6.2 : Commenter une Discussion
**En tant qu'** utilisateur,  
**Je veux** répondre à un post du forum,  
**Afin de** participer à la conversation.

**Critères d'acceptance** :
- Zone de texte sous chaque post
- Validation du contenu
- Ajout du commentaire avec horodatage
- Affichage du nom de l'auteur
- Notification à l'auteur du post

**Estimation** : 5 points  
**Priorité** : Basse

---

### EPIC 7 : ADMINISTRATION

#### US7.1 : Consulter le Dashboard Admin
**En tant qu'** administrateur,  
**Je veux** voir les statistiques globales de la bibliothèque,  
**Afin de** surveiller l'activité.

**Critères d'acceptance** :
- Nombre total de livres
- Nombre d'utilisateurs actifs
- Nombre d'emprunts en cours
- Nombre d'emprunts en retard
- Livres les plus empruntés (top 10)
- Graphiques d'activité

**Estimation** : 13 points  
**Priorité** : Haute

---

#### US7.2 : Gérer les Utilisateurs (Admin)
**En tant qu'** administrateur,  
**Je veux** activer/désactiver des comptes utilisateurs,  
**Afin de** contrôler l'accès au système.

**Critères d' acceptance** :
- Liste de tous les utilisateurs
- Bouton "Activer/Désactiver" pour chaque utilisateur
- Mise à jour du statut en base
- Blocage de connexion pour les utilisateurs désactivés
- Message de confirmation

**Estimation** : 5 points  
**Priorité** : Moyenne

---

#### US7.3 : Générer des Rapports d'Activité (Admin)
**En tant qu'** administrateur,  
**Je veux** générer des rapports mensuels,  
**Afin d'** analyser la performance de la bibliothèque.

**Critères d'acceptance** :
- Sélection de la période (mois/année)
- Génération automatique du rapport
- Données : emprunts totaux, taux de retour, livres populaires
- Export en PDF
- Envoi par email possible

**Estimation** : 13 points  
**Priorité** : Basse

---

## 3.4 Planification des Sprints

Le projet a été organisé en **6 sprints de 2 semaines**, soit une durée totale de développement de 12 semaines (3 mois).

### Sprint 0 : Initialisation et Préparation (Semaine -2 à 0)
**Objectifs** :
- Mise en place de l'environnement de développement
- Configuration Git/GitHub
- Installation de XAMPP, création de la base de données
- Setup du projet (architecture des dossiers)
- Rédaction de la documentation initiale

**Livrables** :
- Environnement de développement fonctionnel
- Repository Git initialisé
- Schéma de base de données initial

---

### Sprint 1 : Authentification et Base (Semaines 1-2)
**User Stories incluses** :
- US1.1 : Inscription
- US1.2 : Connexion
- US1.4 : Déconnexion
- US1.3 : Gestion du profil

**Objectif** : Avoir un système d'authentification fonctionnel

**Livrables** :
- Pages d'inscription et de connexion
- Système de session sécurisé
- Page de profil utilisateur
- Templates de base (header, footer, navigation)

**Vélocité** : 15 Story Points

---

### Sprint 2 : Catalogue et Recherche (Semaines 3-4)
**User Stories incluses** :
- US2.1 : Ajouter un livre (admin)
- US2.3 : Rechercher des livres
- US2.4 : Consulter les détails
- US2.5 : Modifier un livre (admin)

**Objectif** : Constituer et naviguer dans le catalogue

**Livrables** :
- Interface d'administration des livres
- Page de recherche avec filtres
- Page de détails d'un livre
- Upload de couvertures

**Vélocité** : 23 Story Points

---

### Sprint 3 : Gestion des Emprunts (Semaines 5-6)
**User Stories incluses** :
- US3.1 : Emprunter un livre
- US3.2 : Consulter mes emprunts
- US3.3 : Retourner un livre
- US3.4 : Prolonger un emprunt

**Objectif** : Fonctionnalité cœur de la bibliothèque opérationnelle

**Livrables** :
- Système de gestion des emprunts
- Dashboard utilisateur avec emprunts en cours
- Notifications de rappel
- Gestion des dates de retour

**Vélocité** : 31 Story Points

---

### Sprint 4 : Bibliothèques Personnelles et APIs (Semaines 7-8)
**User Stories incluses** :
- US4.1 : Créer une bibliothèque
- US4.2 : Ajouter un livre à une bibliothèque
- US4.3 : Consulter une bibliothèque
- US2.2 : Import depuis OpenLibrary

**Objectif** : Personnalisation de l'expérience utilisateur

**Livrables** :
- Système de bibliothèques personnelles
- Intégration OpenLibrary API
- Gestion des notes personnelles
- Organisation des collections

**Vélocité** : 26 Story Points

---

### Sprint 5 : Intelligence Artificielle et Recommandations (Semaines 9-10)
**User Stories incluses** :
- US5.1 : Recommandations IA
- US3.5 : Historique de lecture
- US7.1 : Dashboard admin

**Objectif** : Fonctionnalités avancées et intelligence

**Livrables** :
- Intégration Google Gemini AI
- Système de recommandations
- Tableaux de bord admin
- Statistiques et graphiques

**Vélocité** : 42 Story Points

---

### Sprint 6 : Communication et Finitions (Semaines 11-12)
**User Stories incluses** :
- US6.1 : Forum - création de posts
- US6.2 : Forum - commentaires
- US7.2 : Gestion des utilisateurs
- US3.6 : Gestion des retards
- Corrections de bugs et optimisations

**Objectif** : Finalisation et polissage

**Livrables** :
- Forum fonctionnel
- Messagerie interne
- Modération admin
- Application stable et testée

**Vélocité** : 26 Story Points

---

### Évolution de la Vélocité

| Sprint | Story Points Planifiés | Story Points Réalisés | Vélocité |
|--------|------------------------|----------------------|----------|
| Sprint 1 | 15 | 15 | 100% |
| Sprint 2 | 23 | 21 | 91% |
| Sprint 3 | 31 | 31 | 100% |
| Sprint 4 | 26 | 24 | 92% |
| Sprint 5 | 42 | 38 | 90% |
| Sprint 6 | 26 | 26 | 100% |
| **Total** | **163** | **155** | **95%** |

### Burndown Chart

Le burndown chart général du projet montre une progression régulière avec quelques ajustements mineurs entre les sprints 4 et 5 dus à la complexité de l'intégration de l'IA.

---

### Cérémonies Scrum

Pour chaque sprint, nous avons respecté les cérémonies suivantes :

**Sprint Planning** (début de sprint - 2h)
- Sélection des User Stories du backlog
- Décomposition en tâches techniques
- Estimation et engagement de l'équipe

**Daily Scrum** (2 fois par semaine - 15min)
- Ce qui a été fait
- Ce qui sera fait
- Les obstacles rencontrés

**Sprint Review** (fin de sprint - 1h)
- Démonstration des fonctionnalités développées
- Feedback du Product Owner
- Validation de l'incrément

**Sprint Retrospective** (fin de sprint - 45min)
- Ce qui s'est bien passé
- Ce qui peut être amélioré
- Actions d'amélioration pour le sprint suivant

---
