# LocImmo

Plateforme de publication d'offres immobilieres pour la Republique du Congo, ciblee sur Brazzaville, Dolisie, Pointe-Noire et Nkayi.

Stack reprise du projet de reference :
- Symfony 6.4 pour l'API REST
- Vue 3, Vite et Pinia pour la SPA
- MySQL 8
- Authentification JWT
- Socle RGPD : consentement, export, rectification et anonymisation
- Docker Compose et configuration Render

## Fonctionnalites

- consultation publique des offres immobilieres
- publication d'offres de location et de vente
- filtres par ville, type d'offre, type de bien et budget
- publication d'annonces par bailleurs/agences connectes
- types de biens : appartement, maison, studio, villa, bureau, terrain
- fiche detaillee : quartier, adresse/repere, prix, surface, chambres, salles d'eau
- demande de visite par utilisateur connecte
- espace personnel et administration conserves depuis le socle LocImmo

## Structure

```text
LocImmo/
├── backend/   # API Symfony 6.4 + Doctrine + JWT
├── frontend/  # SPA Vue 3 + Vite + Pinia
├── docker/    # reverse proxy nginx
└── docker-compose.yml
```

## Installation locale

Backend :

```bash
cd backend
composer install
cp .env.example .env
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
php -S 127.0.0.1:8000 -t public
```

Frontend :

```bash
cd frontend
npm install
cp .env.example .env
npm run dev -- --host 127.0.0.1 --port 5173
```

URLs :
- frontend : `http://127.0.0.1:5173`
- API : `http://127.0.0.1:8000/api`

## Docker

```bash
docker compose up --build
```

Application : `http://localhost`

## Endpoints principaux

| Methode | Endpoint | Acces | Description |
|---|---|---|---|
| GET | `/api/properties` | Public | Liste des offres publiees |
| GET | `/api/properties?city=Brazzaville&offerType=Location&propertyType=Appartement&maxRent=250000` | Public | Liste filtree |
| GET | `/api/properties/{id}` | Public | Detail d'une offre |
| POST | `/api/properties` | Bailleur/agence/admin | Creer une offre |
| PUT | `/api/properties/{id}` | Proprietaire/admin | Modifier une offre |
| PATCH | `/api/properties/{id}/publish` | Proprietaire/admin | Publier ou depublier |
| DELETE | `/api/properties/{id}` | Proprietaire/admin | Supprimer une annonce |
| POST | `/api/events/{id}/register` | Auth | Demander une visite |
| GET | `/api/registrations/my` | Auth | Mes demandes de visite |

Les anciens endpoints `/api/events` restent disponibles pour compatibilite avec le socle initial.

## Variables utiles

Backend :
- `DATABASE_URL`
- `JWT_SECRET_KEY`
- `JWT_PUBLIC_KEY`
- `JWT_PASSPHRASE`
- `JWT_TTL`
- `CORS_ALLOW_ORIGIN`

Frontend :
- `VITE_API_URL=http://localhost:8000/api`

## Notes

- Ne pas commiter les fichiers `.env`.
- Ne pas commiter les cles JWT privees.
- Les types d'offre autorises sont `Location` et `Vente`.
- Les villes autorisees par validation backend sont `Brazzaville`, `Dolisie`, `Pointe-Noire` et `Nkayi`.
