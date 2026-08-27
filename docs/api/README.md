# World Connections API Documentation

This folder contains the OpenAPI specification and Swagger UI page for the World Connections REST API.

## Files

- `openapi.yaml` — OpenAPI 3.0.3 specification.
- `index.html` — Swagger UI documentation page.
- `swagger-initializer.js` — Configures Swagger UI to load `openapi.yaml`.

## Expected repository location

```text
translator_coordinator/
├── api/
│   ├── countries/
│   │   ├── index.php
│   │   └── .htaccess
│   └── regions/
│       ├── index.php
│       └── .htaccess
├── docs/
│   └── api/
│       ├── index.html
│       ├── openapi.yaml
│       ├── swagger-initializer.js
│       └── README.md
└── ...
```

## Open the documentation

With Apache/XAMPP running and the repository located under `htdocs`, browse to:

```text
http://localhost/translator_coordinator/docs/api/
```

Swagger UI loads the API specification from `openapi.yaml` and uses a relative API server URL of `../../api`. This allows the same documentation to work when the entire `translator_coordinator` project is moved to another Apache host or base path.

## Documented requests

```text
GET /api/countries
GET /api/countries/{countryId}
GET /api/countries?regionid=3
GET /api/countries?search=united
GET /api/countries?regionid=3&search=united
GET /api/regions
GET /api/regions/{regionId}
GET /api/regions?search=asia
```

## Important Apache requirement

For URLs such as:

```text
/api/countries/25
/api/regions/3
```

to work, the `api/countries/.htaccess` rewrite rule discussed in the project setup must be enabled and Apache must permit `AllowOverride` for the project directory.

## Current Swagger UI dependency

`index.html` pins Swagger UI Dist version `5.32.14` through unpkg. This keeps the repository small, but an Internet connection is required to load the Swagger UI JavaScript and CSS.

### Optional: make Swagger UI fully offline

Download these three files from the `swagger-ui-dist` distribution for version 5.32.14 and place them in this directory:

```text
swagger-ui.css
swagger-ui-bundle.js
swagger-ui-standalone-preset.js
```

Then replace the three `https://unpkg.com/...` references in `index.html` with:

```html
<link rel="stylesheet" href="swagger-ui.css">
<script src="swagger-ui-bundle.js"></script>
<script src="swagger-ui-standalone-preset.js"></script>
```

The OpenAPI file and application API are already served locally; only these Swagger UI display assets need to be copied for a completely offline setup.

## Suggested Definition of Done for API stories

When a student changes or adds an API endpoint, the associated OpenAPI documentation should be updated in the same pull request.
