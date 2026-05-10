# Site web Helpdesk

Mini-application web de support et de gestion de tickets pour un contexte
etudiant/tuteur. Le site permet aux etudiants de creer des demandes
d'assistance et aux tuteurs de suivre, filtrer, commenter et faire evoluer le
statut des tickets.

## Fonctionnalites

- Connexion et deconnexion avec sessions PHP.
- Roles distincts : `student` et `tutor`.
- Tableau de bord avec statistiques de tickets.
- Creation de tickets par les etudiants.
- Consultation des tickets personnels pour les etudiants.
- Consultation globale pour les tuteurs.
- Filtres par statut, categorie, priorite et recherche textuelle.
- Detail d'un ticket avec commentaires.
- Changement de statut par les tuteurs.
- Stockage local dans des fichiers JSON.

## Comptes de demonstration

Mot de passe commun : `password`

| Identifiant | Role | Nom affiche |
| --- | --- | --- |
| `alice` | Etudiant | Alice Martin |
| `bob` | Etudiant | Bob Dupont |
| `tuteur` | Tuteur | Prof. Leclerc |

Les mots de passe de demonstration sont stockes sous forme de hash bcrypt dans
`data/users.json`.

## Architecture

```text
.
|-- assets/
|   |-- style.css          # Styles de l'interface
|   `-- script.js          # Interactions front-end
|-- data/
|   |-- users.json         # Utilisateurs de demonstration
|   |-- tickets.json       # Tickets
|   `-- comments.json      # Commentaires
|-- includes/
|   |-- config.php         # Constantes, chemins, session
|   |-- auth.php           # Authentification et roles
|   |-- data.php           # Lecture/ecriture JSON
|   |-- functions.php      # Helpers de securite et d'affichage
|   |-- header.php         # Entete commun
|   `-- footer.php         # Pied de page commun
|-- create_ticket.php      # Creation d'un ticket
|-- index.php              # Tableau de bord
|-- login.php              # Connexion
|-- logout.php             # Deconnexion
|-- ticket.php             # Detail d'un ticket
`-- tickets.php            # Liste et filtres
```

## Choix techniques

- PHP natif, sans framework.
- HTML5, CSS3 et JavaScript.
- Donnees persistantes dans des fichiers JSON.
- Sessions PHP pour l'authentification.
- `password_verify()` pour verifier les mots de passe hashes.
- `htmlspecialchars()` via la fonction `e()` pour limiter les risques XSS.
- `LOCK_EX` lors de l'ecriture JSON pour limiter les conflits d'ecriture.

## Installation

Prerequis :

- PHP 8.0 ou plus recent.

Lancer le serveur de developpement depuis la racine du projet :

```bash
php -S localhost:8080
```

Ouvrir ensuite :

```text
http://localhost:8080/login.php
```

## Pages principales

- `login.php` : connexion utilisateur.
- `index.php` : tableau de bord adapte au role connecte.
- `tickets.php` : liste des tickets et filtres.
- `ticket.php` : detail, commentaires et changement de statut.
- `create_ticket.php` : formulaire de creation d'un ticket etudiant.

## Securite

Le projet applique plusieurs protections importantes :

- echappement HTML des sorties avec `htmlspecialchars()`;
- regeneration de l'identifiant de session apres connexion;
- controle d'acces selon le role connecte;
- validation des champs POST et des identifiants GET;
- cookies de session en `httponly` et `samesite=Strict`;
- mots de passe hashes avec bcrypt.

Pour un deploiement public en production, il faudrait aussi activer HTTPS,
placer les fichiers JSON hors de la racine web ou les proteger par configuration
serveur, et remplacer les donnees de demonstration.

## Donnees

Le dossier `data/` contient les fichiers JSON de demonstration. Ils sont utiles
pour tester directement le site apres lancement local.
