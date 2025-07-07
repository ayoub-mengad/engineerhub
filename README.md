# EngineerHub

> **Projet de 2ᵉ année CI – Ingénierie des Systèmes Informatiques**  
> Réalisé par **M. Ayoub Mengad**
> Année universitaire 2024 – 2025

---

## Sommaire
1. [Présentation](#présentation)
2. [Fonctionnalités](#fonctionnalités)
3. [Architecture & choix techniques](#architecture--choix-techniques)
4. [Prérequis](#prérequis)
5. [Installation rapide](#installation-rapide)
6. [Scripts utiles](#scripts-utiles)
7. [Tests & qualité](#tests--qualité)
8. [Screenshots](#screenshots)
9. [Roadmap](#roadmap)
10. [Auteur & contact](#auteur--contact)
11. [Licence](#licence)

---

## Présentation

**EngineerHub** est un réseau social de niche destiné aux ingénieurs, développé en **Laravel 12**.  
Le projet illustre :

* l’implémentation d’un backend modulaire (pattern DAO/Repository + Service),
* une interface utilisateur inspirée de LinkedIn (Tailwind CSS),
* l’intégration d’une fonctionnalité d’IA (OpenAI / Gemini) pour générer des posts ou répondre à des questions d’ingénierie.

---

## Fonctionnalités

| Domaine                   | Détails                                                                      |
|---------------------------|------------------------------------------------------------------------------|
| Authentification          | Laravel Breeze (login, inscription, reset mdp)                               |
| Fil d’actualité           | Posts personnels + posts d’amis, filtres « My Posts », « Friends Posts ».     |
| Posts                     | CRUD basique (contenu, visibilité publique / amis).                          |
| Système d’amis            | Rechercher, inviter, accepter/refuser, liste d’amis, partage sélectif.       |
| AI Prompt & historique    | Génération de contenu via OpenAI/Gemini, loggé dans `prompt_logs`.           |
| UI LinkedIn-like          | Palette `#0A66C2`, cartes `rounded-lg shadow-sm`, layout 3 colonnes `lg:`.   |

---

## Architecture & choix techniques

| Couche        | Rôle principal                                    | Dossier                           |
|---------------|---------------------------------------------------|-----------------------------------|
| **Controllers** | Validation HTTP + appel des Services             | `app/Http/Controllers`            |
| **Services**     | Logique métier, testable, sans Eloquent direct  | `app/Services`                    |
| **DAO / Repo**   | Accès aux données (Eloquent encapsulé)          | `app/Repositories`                |
| **Models**       | Modèles Eloquent                                | `app/Models`                      |
| **Provider**     | Binding interface → implémentation              | `app/Providers/RepositoryServiceProvider.php` |

> **Stack** : PHP 8.3 • Laravel 12 • MySQL 8 • Tailwind CSS • Vite • Node 18  
> **Tests** : PHPUnit 10 + Pest (optionnel) • Mockery pour les interfaces

---

## Prérequis

| Outil         | Version minimum |
|---------------|-----------------|
| PHP           | 8.2.0           |
| Composer      | 2.x             |
| Node & npm    | 18.x / 9.x      |
| MySQL/MariaDB | 8.x / 10.6+     |

---

## Installation rapide

```bash
# 1. Cloner le dépôt
git clone https://github.com/<votre-org>/engineerhub.git
cd engineerhub

# 2. Back-end
composer install
cp .env.example .env            # renseigner DB_*, OPENAI_KEY…
php artisan key:generate
php artisan migrate --seed

# 3. Front-end
npm install
npm run build                   # ou `npm run dev` en mode watch

# 4. Lancer
php artisan serve
# → http://127.0.0.1:8000
