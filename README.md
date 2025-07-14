# Wereld Restaurants

**Deze applicatie is gebaseerd op een idee uit De Grote Podcastlas en toont een interactieve kaart met restaurants in Nederland, gefilterd op het land van herkomst van de keuken.**

De backend is gebouwd met PHP en Symfony, en de frontend gebruikt React. De initiële lijst met restaurants is afkomstig uit de OpenStreetMap API. Met behulp van AI is bij zoveel mogelijk restaurants het land van de keuken automatisch bepaald op basis van de naam.

# Bijdragen aan dit project

Bedankt voor je interesse om bij te dragen!

## Workflow

- Fork deze repository en werk op een **eigen feature branch**.
- Open altijd een **pull request naar de `develop` branch**.
- De `master` branch is beschermd en wordt alleen door de beheerder bijgewerkt.
- Zorg voor duidelijke commitberichten en goed gestructureerde code.

## Opmerkingen

- Houd de code schoon en consistent met het bestaande project.
- Test je wijzigingen waar mogelijk lokaal.

## Vereisten

- Node.js (.nvmrc)
- PHP 8.2+
- Composer
- MySQL of MariaDB
- Symfony CLI (optioneel, handig voor lokale server)
- Npm

## Backend API

De restaurants worden geladen via `/api/restaurants`, gefilterd op landcodes en kaartgrenzen.

Voorbeeld:

```
GET /api/restaurants?countries[]=NL&bounds={"south":51.5,"north":53.2,"west":4.4,"east":6.1}
```
