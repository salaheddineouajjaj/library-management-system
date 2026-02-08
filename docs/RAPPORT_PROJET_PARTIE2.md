# CHAPITRE 1 : CONTEXTE ET OBJECTIFS DU PROJET

## 1.1 Problématique Métier

La gestion traditionnelle des bibliothèques fait face à de nombreux défis dans un environnement de plus en plus numérique et exigeant. Les bibliothèques, qu'elles soient universitaires, publiques ou spécialisées, doivent gérer des milliers, voire des dizaines de milliers d'ouvrages, tout en assurant un service de qualité à leurs usagers.

### 1.1.1 Défis de la Gestion Manuelle

La gestion manuelle ou semi-automatisée des bibliothèques présente plusieurs problèmes majeurs :

**Gestion des emprunts et retours :**
- Processus chronophage nécessitant la présence physique d'un bibliothécaire
- Risques d'erreurs dans l'enregistrement des dates de retour
- Difficulté à suivre les livres en retard et à relancer les emprunteurs
- Impossibilité de consulter l'historique complet des emprunts

**Recherche et catalogage :**
- Système de classification rigide (Dewey, CDU) difficile à naviguer pour les usagers
- Temps de recherche important pour trouver un ouvrage spécifique
- Absence de filtres avancés (par auteur, catégorie, année, disponibilité)
- Impossibilité de réserver un livre actuellement emprunté

**Expérience utilisateur limitée :**
- Aucune personnalisation de l'expérience de lecture
- Absence de recommandations basées sur les préférences de l'utilisateur
- Impossibilité de créer des listes de lecture ou des bibliothèques personnelles
- Manque d'interaction entre les lecteurs (forums, discussions)

**Gestion administrative :**
- Rapports et statistiques difficiles à générer
- Suivi manuel des stocks et des disponibilités
- Gestion complexe des catégories et des nouveaux ouvrages
- Absence d'indicateurs de performance (livres les plus empruntés, taux de rotation)

### 1.1.2 Impact sur les Parties Prenantes

Ces problèmes affectent directement trois types d'acteurs :

**Les bibliothécaires :**
- Surcharge de travail administratif
- Temps limité pour les activités à valeur ajoutée (conseil, animation culturelle)
- Difficulté à maintenir à jour les fichiers d'emprunt

**Les usagers :**
- Expérience de recherche frustrante
- Temps d'attente pour emprunter ou retourner des livres
- Manque d'accessibilité (horaires d'ouverture limités)
- Difficulté à découvrir de nouveaux livres correspondant à leurs goûts

**Les administrateurs :**
- Manque de visibilité sur l'activité de la bibliothèque
- Difficulté à prendre des décisions éclairées sur les acquisitions
- Impossibilité d'optimiser les ressources

## 1.2 Solution Proposée

Pour répondre à ces défis, nous avons conçu et développé un **Système de Gestion de Bibliothèque Intelligent** (SGBI), une application web moderne qui digitalise et optimise l'ensemble des processus de gestion d'une bibliothèque.

### 1.2.1 Vue d'Ensemble de la Solution

Notre système est une plateforme web complète accessible 24h/24 et 7j/7, offrant :

**Pour les usagers :**
- Un portail intuitif pour rechercher et emprunter des livres en ligne
- Un tableau de bord personnalisé affichant les emprunts en cours, l'historique et les dates de retour
- Un système de recommandations intelligent basé sur l'IA (Google Gemini)
- Des bibliothèques personnelles pour organiser ses livres favoris
- Un forum de discussion pour échanger avec d'autres lecteurs
- Un système de messagerie interne

**Pour les bibliothécaires/administrateurs :**
- Un tableau de bord complet avec statistiques en temps réel
- Une gestion complète du catalogue (ajout, modification, suppression de livres)
- Un suivi automatisé des emprunts et des retours
- Une détection automatique des retards avec notifications
- Des rapports détaillés sur l'activité de la bibliothèque
- Une intégration avec l'API OpenLibrary pour enrichir le catalogue

**Pour le système :**
- Une architecture moderne et évolutive
- Une sécurité renforcée (authentification, gestion des rôles)
- Une interface utilisateur élégante inspirée des bibliothèques classiques
- Une performance optimisée pour gérer des milliers de livres

### 1.2.2 Innovation : Recommandations par IA

L'un des points forts de notre système est l'intégration de l'intelligence artificielle pour générer des recommandations personnalisées. En analysant :
- L'historique de lecture de l'utilisateur
- Ses catégories préférées
- Les livres dans ses bibliothèques personnelles
- Les tendances générales de la bibliothèque

Le système utilise l'API Google Gemini pour suggérer des ouvrages pertinents et adaptés aux goûts de chaque lecteur, créant ainsi une expérience véritablement personnalisée.

## 1.3 Objectifs du Projet

Le développement de ce système poursuit des objectifs à la fois fonctionnels et techniques, visant à créer une solution complète, performante et évolutive.

### 1.3.1 Objectifs Fonctionnels

**OF1 : Digitaliser la gestion des emprunts**
- Permettre aux usagers d'emprunter des livres en ligne
- Automatiser l'enregistrement des emprunts et des retours
- Gérer automatiquement les dates d'échéance et les prolongations
- Notifier les utilisateurs avant les dates de retour

**OF2 : Optimiser la recherche de livres**
- Offrir une recherche avancée avec filtres multiples
- Afficher la disponibilité en temps réel
- Permettre la réservation de livres déjà empruntés
- Intégrer des données enrichies depuis OpenLibrary API

**OF3 : Personnaliser l'expérience utilisateur**
- Créer des bibliothèques personnelles organisables
- Générer des recommandations basées sur l'IA
- Afficher un historique complet de lecture
- Proposer un forum de discussion entre lecteurs

**OF4 : Faciliter l'administration**
- Fournir un tableau de bord avec indicateurs clés
- Automatiser la gestion du catalogue
- Générer des rapports d'activité
- Gérer les utilisateurs et leurs droits d'accès

**OF5 : Améliorer l'accessibilité**
- Rendre le système accessible 24h/24 via le web
- Proposer une interface responsive (mobile, tablette, desktop)
- Offrir une expérience utilisateur intuitive
- Garantir un temps de réponse rapide

### 1.3.2 Objectifs Techniques

**OT1 : Architecture moderne et évolutive**
- Adopter une architecture MVC (Modèle-Vue-Contrôleur)
- Séparer clairement la logique métier de la présentation
- Faciliter la maintenance et l'évolution du code
- Permettre l'ajout de nouvelles fonctionnalités sans régression

**OT2 : Sécurité renforcée**
- Implémenter un système d'authentification sécurisé (bcrypt)
- Gérer les rôles et permissions (RBAC)
- Protéger contre les injections SQL (PDO préparé)
- Sécuriser les sessions utilisateurs

**OT3 : Performance et scalabilité**
- Optimiser les requêtes de base de données
- Mettre en cache les données fréquemment consultées
- Gérer efficacement plusieurs utilisateurs simultanés
- Assurer un temps de chargement inférieur à 2 secondes

**OT4 : Intégration d'API externes**
- Intégrer l'API OpenLibrary pour enrichir le catalogue
- Utiliser Google Gemini AI pour les recommandations
- Gérer les erreurs et les timeouts des API
- Mettre en place un système de fallback

**OT5 : Déploiement et maintenance**
- Mettre en place un système de versioning (Git/GitHub)
- Configurer le déploiement automatique (CI/CD)
- Documenter le code et l'architecture
- Faciliter la prise en main par de nouveaux développeurs

**OT6 : Expérience utilisateur (UX/UI)**
- Créer une interface élégante inspirée des bibliothèques classiques
- Utiliser des typographies lisibles (Crimson Text, Lora)
- Implémenter un design responsive
- Assurer l'accessibilité (WCAG 2.1)

### 1.3.3 Indicateurs de Succès

Pour mesurer l'atteinte de ces objectifs, nous avons défini les indicateurs suivants :

| Indicateur | Objectif | Mesure |
|------------|----------|--------|
| Temps de recherche | < 2 secondes | Performance |
| Taux de disponibilité | > 99% | Infrastructure |
| Satisfaction utilisateur | > 4/5 | Questionnaire |
| Taux d'adoption | > 80% des usagers | Usage |
| Nombre de bugs critiques | 0 en production | Qualité |
| Couverture de tests | > 70% | Tests |

---

# CHAPITRE 2 : ANALYSE DE L'EXISTANT

## 2.1 Système Actuel

Avant le développement de notre solution, la bibliothèque utilisait un système hybride combinant des processus manuels et des outils informatiques basiques, principalement des feuilles de calcul Excel et un registre papier.

### 2.1.1 Processus de Catalogage

Le catalogage des livres était effectué dans un fichier Excel contenant :
- Le titre, l'auteur et l'ISBN
- La catégorie (classification Dewey simplifiée)
- Le nombre d'exemplaires disponibles
- La date d'acquisition

Ce fichier était maintenu manuellement par le bibliothécaire principal, sans système de sauvegarde automatique ni historique des modifications.

### 2.1.2 Gestion des Emprunts

Les emprunts étaient enregistrés dans un cahier papier avec :
- Le nom de l'emprunteur
- Le titre du livre
- La date d'emprunt
- La date de retour prévue

Les retours étaient notés en barrant la ligne correspondante. Aucun système automatique n'existait pour détecter les retards ou relancer les emprunteurs.

### 2.1.3 Recherche de Livres

Les usagers devaient :
1. Consulter un catalogue papier organisé par auteur ou catégorie
2. Noter la cote du livre
3. Se rendre dans les rayons pour localiser l'ouvrage
4. Vérifier physiquement la disponibilité

### 2.1.4 Rapports et Statistiques

Les statistiques étaient calculées manuellement en fin de mois :
- Nombre total d'emprunts
- Livres les plus empruntés
- Taux d'occupation

Ce processus prenait plusieurs heures et comportait des risques d'erreur.

## 2.2 Limites du Système Actuel

L'analyse approfondie du système existant a révélé de nombreuses limites critiques :

### 2.2.1 Limites Fonctionnelles

**L1 : Absence de traçabilité**
- Historique des emprunts incomplet
- Impossibilité de retrouver qui a emprunté un livre perdu
- Pas de suivi des prolongations

**L2 : Gestion des retards inefficace**
- Détection manuelle des retards (chronophage)
- Absence de relances automatiques
- Pas de système de pénalités cohérent

**L3 : Recherche limitée**
- Impossible de rechercher par mots-clés
- Pas de filtres par disponibilité, auteur, année
- Consultation obligatoire sur place

**L4 : Expérience utilisateur pauvre**
- Aucune personnalisation
- Impossibilité de connaître ses emprunts en cours à distance
- Pas de système de réservation

**L5 : Rapports inexacts**
- Calculs manuels sujets à erreurs
- Statistiques incomplètes
- Temps de génération important

### 2.2.2 Limites Techniques

**L6 : Absence de sécurité**
- Fichiers Excel non protégés
- Pas d'authentification
- Risque de perte de données (pas de sauvegarde)

**L7 : Scalabilité impossible**
- Système inadapté pour plus de 1000 livres
- Ralentissement avec l'augmentation des emprunts
- Pas de support multi-utilisateurs simultanés

**L8 : Accessibilité limitée**
- Consultation uniquement sur place
- Horaires d'ouverture contraignants
- Pas d'accès mobile

**L9 : Maintenance complexe**
- Pas de documentation
- Dépendance à une seule personne
- Risque de corruption de fichiers

### 2.2.3 Impact Business

Ces limites entraînent :
- **Perte de temps** : 3-4 heures/jour en tâches administratives
- **Insatisfaction** : 60% des usagers se plaignent du système
- **Coûts cachés** : Livres perdus non retracés (≈ 5% par an)
- **Opportunités manquées** : Impossibilité d'optimiser les acquisitions

## 2.3 Besoins Identifiés

Sur la base de cette analyse et après consultation des parties prenantes (bibliothécaires, usagers, administrateurs), nous avons établi une liste exhaustive des besoins.

### 2.3.1 Besoins Fonctionnels

#### BF1 : Gestion des Utilisateurs

**BF1.1 : Inscription et Authentification**
- Permettre l'inscription en ligne des nouveaux utilisateurs
- Vérifier l'unicité de l'email
- Hasher les mots de passe (bcrypt)
- Gérer la connexion/déconnexion sécurisée

**BF1.2 : Gestion des Profils**
- Modifier les informations personnelles
- Changer le mot de passe
- Consulter son historique de lecture
- Gérer ses préférences

**BF1.3 : Gestion des Rôles**
- Différencier utilisateur standard et administrateur
- Contrôler l'accès aux fonctionnalités selon les rôles
- Permettre la désactivation de comptes

#### BF2 : Gestion du Catalogue

**BF2.1 : CRUD des Livres (Admin)**
- Ajouter de nouveaux livres manuellement
- Importer des données depuis OpenLibrary API
- Modifier les informations des livres
- Supprimer des livres (soft delete)

**BF2.2 : Recherche Avancée**
- Rechercher par titre, auteur, ISBN
- Filtrer par catégorie, année, disponibilité
- Trier les résultats (pertinence, date, popularité)
- Afficher les informations détaillées

**BF2.3 : Gestion des Catégories**
- Créer/modifier/supprimer des catégories
- Organiser hiérarchiquement
- Associer des livres aux catégories

#### BF3 : Gestion des Emprunts

**BF3.1 : Emprunter un Livre**
- Vérifier la disponibilité en temps réel
- Vérifier l'éligibilité de l'utilisateur (pas de retard)
- Enregistrer l'emprunt avec date de retour (14 jours)
- Décrémenter le nombre d'exemplaires disponibles

**BF3.2 : Retourner un Livre**
- Enregistrer la date de retour effective
- Incrémenter le nombre d'exemplaires disponibles
- Calculer d'éventuelles pénalités de retard

**BF3.3 : Prolonger un Emprunt**
- Permettre une prolongation unique de 7 jours
- Vérifier qu'aucune réservation n'est en attente
- Mettre à jour la date de retour

**BF3.4 : Gestion des Retards**
- Détecter automatiquement les livres en retard
- Envoyer des notifications de rappel
- Bloquer les emprunts pour les utilisateurs en retard

#### BF4 : Bibliothèques Personnelles

**BF4.1 : Créer des Étagères**
- Créer des bibliothèques personnelles thématiques
- Nommer et décrire ses bibliothèques
- Définir la visibilité (publique/privée)

**BF4.2 : Gérer les Livres**
- Ajouter des livres à une bibliothèque
- Retirer des livres
- Ajouter des notes personnelles
- Réorganiser les livres

#### BF5 : Recommandations IA

**BF5.1 : Analyse du Profil**
- Analyser l'historique de lecture
- Identifier les catégories préférées
- Détecter les patterns de lecture

**BF5.2 : Génération de Recommandations**
- Utiliser Google Gemini AI
- Générer 5-10 recommandations personnalisées
- Expliquer le raisonnement derrière chaque recommandation
- Filtrer les livres disponibles dans la bibliothèque

#### BF6 : Communication

**BF6.1 : Forum**
- Créer des discussions thématiques
- Commenter les posts
- Épingler les discussions importantes
- Modérer le contenu (admin)

**BF6.2 : Messagerie**
- Envoyer des messages entre utilisateurs
- Consulter sa boîte de réception
- Marquer les messages comme lus
- Supprimer des messages

#### BF7 : Tableaux de Bord

**BF7.1 : Dashboard Utilisateur**
- Afficher les emprunts en cours
- Afficher les livres en retard
- Montrer l'historique récent
- Suggérer des recommandations

**BF7.2 : Dashboard Admin**
- Statistiques globales (nombre de livres, utilisateurs, emprunts)
- Livres les plus empruntés
- Utilisateurs avec retards
- Emprunts récents et retours attendus

#### BF8 : Rapports et Statistiques

**BF8.1 : Rapports d'Activité**
- Générer des rapports mensuels/annuels
- Exporter au format PDF/Excel
- Analyser les tendances de lecture
- Évaluer les performances de la bibliothèque

### 2.3.2 Besoins Non Fonctionnels

#### BNF1 : Performance

**BNF1.1 : Temps de Réponse**
- Page d'accueil : < 1 seconde
- Recherche de livres : < 2 secondes
- Actions CRUD : < 1 seconde
- Génération de rapports : < 5 secondes

**BNF1.2 : Charge**
- Support de 100 utilisateurs simultanés minimum
- Gestion de 50 000 livres dans le catalogue
- 1 000 transactions/jour

#### BNF2 : Sécurité

**BNF2.1 : Authentification**
- Hashage des mots de passe (bcrypt, cost 12)
- Protection contre le brute-force (rate limiting)
- Sessions sécurisées (HttpOnly, Secure)
- Timeout de session après 30 minutes d'inactivité

**BNF2.2 : Autorisation**
- Contrôle d'accès basé sur les rôles (RBAC)
- Validation des permissions à chaque requête
- Isolation des données entre utilisateurs

**BNF2.3 : Protection des Données**
- Requêtes SQL préparées (protection injection SQL)
- Échappement des sorties (protection XSS)
- Protection CSRF pour les formulaires
- Validation stricte des entrées utilisateur

#### BNF3 : Disponibilité

**BNF3.1 : Uptime**
- Taux de disponibilité : > 99.5%
- Maintenance planifiée annoncée 48h à l'avance
- Temps de récupération : < 15 minutes

**BNF3.2 : Sauvegarde**
- Backup automatique quotidien de la base de données
- Rétention de 30 jours
- Plan de reprise après sinistre (DRP)

#### BNF4 : Utilisabilité

**BNF4.1 : Interface**
- Design intuitif et cohérent
- Temps d'apprentissage : < 30 minutes
- Chargement progressif des données
- Messages d'erreur clairs

**BNF4.2 : Accessibilité**
- Responsive design (mobile, tablette, desktop)
- Compatibilité navigateurs (Chrome, Firefox, Safari, Edge)
- Conformité WCAG 2.1 niveau AA
- Support des lecteurs d'écran

#### BNF5 : Maintenabilité

**BNF5.1 : Code**
- Structure MVC claire
- Commentaires et documentation du code
- Nommage cohérent des variables/fonctions
- Respect des standards PSR (PHP)

**BNF5.2 : Documentation**
- README complet avec guide d'installation
- Documentation API
- Diagrammes UML à jour
- Changelog des versions

#### BNF6 : Évolutivité

**BNF6.1 : Architecture**
- Séparation frontend/backend claire
- API RESTful pour futures intégrations
- Base de données normalisée
- Facilité d'ajout de nouvelles fonctionnalités

**BNF6.2 : Données**
- Support de croissance à 200 000 livres
- Scalabilité horizontale possible
- Optimisation des requêtes

#### BNF7 : Compatibilité

**BNF7.1 : Environnement**
- PHP 7.4+
- MySQL 5.7+ ou MariaDB 10.3+
- Serveur Apache ou Nginx
- Support HTTPS

**BNF7.2 : APIs Externes**
- OpenLibrary API pour enrichissement catalogue
- Google Gemini AI pour recommandations
- Gestion des erreurs et timeouts
- Système de fallback en cas d'indisponibilité

---
