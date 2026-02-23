# TMDB App

Aplikacja Laravel do pobierania i udostępniania danych o filmach, serialach i gatunkach z TMDB (The Movie Database).

## 📋 Spis treści

- [Funkcjonalności](#funkcjonalności)
- [Wymagania](#wymagania)
- [Instalacja](#instalacja)
- [Konfiguracja](#konfiguracja)
- [Endpointy API](#endpointy-api)
- [Wielojęzyczność](#wielojęzyczność)
- [Testy](#testy)
- [Technologie](#technologie)

## ✨ Funkcjonalności

### Interfejs Webowy (Livewire)
- **Lista filmów z paginacją** - `/movies`
  - Wyświetla filmy w responsywnej siatce (3 kolumny na dużych ekranach)
  - Paginacja (12 filmów na stronę)
  - Wielojęzyczne tytuły i opisy (PL/EN/DE)
  - Automatyczne formatowanie dat premiery

### REST API
- Endpointy do pobierania filmów, seriali i gatunków
- Paginacja (10 rekordów na stronę)
- Obsługa wielojęzyczności przez nagłówek `Accept-Language`
- API Resources dla czystej transformacji danych

### Scrapowanie danych
- Pobieranie danych z TMDB API przez Queue Job
- Automatyczne tłumaczenia na 3 języki (PL, EN, DE)
- Retry logic z exponential backoff
- Szczegółowe logowanie błędów

## 🔧 Wymagania

### Dla instalacji Docker (zalecane):
- Docker 20.10+
- Docker Compose 2.0+

### Dla instalacji lokalnej:
- PHP 8.2 lub wyższy
- Composer
- Node.js 18+ i NPM
- MySQL 8.0+ / PostgreSQL / SQLite
- Redis (opcjonalnie, dla queue)

## 🚀 Instalacja

### Uruchomienie z Docker (zalecane)

1. **Klonuj repozytorium:**
   ```bash
   git clone <adres_repozytorium>
   cd tmdb-app
   ```

2. **Skopiuj i skonfiguruj plik środowiskowy:**
   ```bash
   cp .env.example .env
   ```

3. **Edytuj plik `.env` i dodaj swój token TMDB:**
   ```env
   TMDB_TOKEN=your_tmdb_bearer_token_here
   ```

4. **Uruchom kontenery Docker:**
   ```bash
   docker-compose up -d --build
   ```

5. **Pobierz dane z TMDB:**
   ```bash
   docker-compose exec app php artisan tmdb:fetch
   ```

6. **Aplikacja jest dostępna pod adresem:**
   - Frontend: http://localhost:8000
   - API: http://localhost:8000/api

#### Przydatne komendy Docker:

```bash
# Zatrzymanie kontenerów
docker-compose down

# Restart kontenerów
docker-compose restart

# Podgląd logów
docker-compose logs -f

# Wejście do kontenera aplikacji
docker-compose exec app bash

# Uruchomienie komend Artisan
docker-compose exec app php artisan migrate
docker-compose exec app php artisan test

# Czyszczenie (usuwa kontenery i wolumeny)
docker-compose down -v
```

### Uruchomienie lokalne

1. **Klonuj repozytorium:**
   ```bash
   git clone <adres_repozytorium>
   cd tmdb-app
   ```

2. **Zainstaluj zależności:**
   ```bash
   composer install
   npm install
   ```

3. **Skonfiguruj plik środowiskowy:**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Edytuj plik `.env` i dodaj konfigurację:**
   ```env
   TMDB_TOKEN=your_tmdb_bearer_token_here
   
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=tmdb_app
   DB_USERNAME=root
   DB_PASSWORD=
   
   QUEUE_CONNECTION=database
   ```

5. **Uruchom migracje:**
   ```bash
   php artisan migrate
   ```

6. **Zbuduj assety frontend:**
   ```bash
   npm run build
   ```

7. **Uruchom serwer:**
   ```bash
   php artisan serve
   ```

8. **Uruchom Queue Worker (w osobnym terminalu):**
   ```bash
   php artisan queue:work --tries=3
   ```

9. **Pobierz dane z TMDB:**
   ```bash
   php artisan tmdb:fetch
   ```

## ⚙️ Konfiguracja

### Uzyskanie tokenu TMDB

1. Załóż konto na [The Movie Database (TMDB)](https://www.themoviedb.org/)
2. Przejdź do [ustawień API](https://www.themoviedb.org/settings/api)
3. Wygeneruj **API Read Access Token (Bearer Token)**
4. Skopiuj token i dodaj do pliku `.env`:
   ```env
   TMDB_TOKEN=your_bearer_token_here
   ```

## 📡 Endpointy API

### GET /api/movies

Zwraca paginowaną listę filmów.

**Parametry zapytania:**
- `page` (opcjonalny) - numer strony (domyślnie: 1)

**Nagłówki:**
- `Accept-Language` (opcjonalny) - język odpowiedzi: `pl`, `en`, `de` (domyślnie: `en`)

**Przykład żądania:**
```bash
curl -H "Accept-Language: pl" http://localhost:8000/api/movies
```

**Przykład odpowiedzi:**
```json
{
  "data": [
    {
      "id": 1,
      "tmdb_id": 550,
      "title": "Podziemny krąg",
      "overview": "Znudzony życiem młody mężczyzna...",
      "release_date": "1999-10-15"
    }
  ],
  "links": {
    "first": "http://localhost:8000/api/movies?page=1",
    "last": "http://localhost:8000/api/movies?page=5",
    "prev": null,
    "next": "http://localhost:8000/api/movies?page=2"
  },
  "meta": {
    "current_page": 1,
    "per_page": 10,
    "total": 50
  }
}
```

### GET /api/series

Zwraca paginowaną listę seriali.

**Parametry i nagłówki:** identyczne jak `/api/movies`

**Przykład odpowiedzi:**
```json
{
  "data": [
    {
      "id": 1,
      "tmdb_id": 1396,
      "name": "Breaking Bad",
      "overview": "A high school chemistry teacher...",
      "first_air_date": "2008-01-20"
    }
  ]
}
```

### GET /api/genres

Zwraca paginowaną listę gatunków.

**Przykład odpowiedzi:**
```json
{
  "data": [
    {
      "id": 1,
      "tmdb_id": 28,
      "name": "Action"
    }
  ]
}
```

## 🌍 Wielojęzyczność

Aplikacja obsługuje 3 języki: **Polski (pl)**, **Angielski (en)**, **Niemiecki (de)**.

### Jak to działa?

1. **Przechowywanie:** Dane są przechowywane w bazie jako JSON z kluczami językowymi
2. **API:** Middleware parsuje nagłówek `Accept-Language` i ustawia odpowiedni język
3. **Fallback:** Jeśli tłumaczenie nie jest dostępne, API zwraca wersję angielską

### Przykłady użycia:

```bash
# Polski
curl -H "Accept-Language: pl" http://localhost:8000/api/movies

# Angielski
curl -H "Accept-Language: en" http://localhost:8000/api/movies

# Niemiecki
curl -H "Accept-Language: de" http://localhost:8000/api/movies
```

## 🧪 Testy

**Uruchomienie wszystkich testów:**
```bash
php artisan test
```

**Uruchomienie z Docker:**
```bash
docker-compose exec app php artisan test
```

**Uruchomienie konkretnego testu:**
```bash
php artisan test --filter MovieApiTest
```

### Dostępne testy:

- `MovieApiTest` - testy endpointu `/api/movies`
- `SerieApiTest` - testy endpointu `/api/series`
- `GenreApiTest` - testy endpointu `/api/genres`

## 📁 Struktura projektu

```
tmdb-app/
├── app/
│   ├── Console/Commands/
│   │   └── FetchTmdbData.php          # Komenda Artisan
│   ├── Http/
│   │   ├── Controllers/Api/           # Kontrolery API
│   │   ├── Middleware/                # Middleware wielojęzyczności
│   │   └── Resources/                 # API Resources
│   ├── Jobs/
│   │   └── FetchTmdbDataJob.php       # Job do pobierania danych
│   ├── Livewire/
│   │   └── MoviesList.php             # Komponent Livewire
│   ├── Models/                        # Modele Eloquent
│   └── Services/
│       └── TmdbService.php            # Serwis TMDB API
├── database/
│   ├── factories/                     # Factories dla testów
│   └── migrations/                    # Migracje bazy danych
├── docker/                            # Konfiguracja Docker
├── tests/Feature/Api/                 # Testy Feature
├── docker-compose.yml
├── Dockerfile
└── README.md
```

## 🛠️ Technologie

- **Backend:** Laravel 12
- **Frontend:** Livewire 4, TailwindCSS 4, Vite
- **Baza danych:** MySQL 8.0
- **Cache & Queue:** Redis
- **API:** TMDB (The Movie Database)
- **Konteneryzacja:** Docker, Docker Compose
- **Web Server:** Nginx
- **Testy:** PHPUnit

## 📝 Komenda Artisan

### php artisan tmdb:fetch

Komenda do pobierania danych z TMDB API.

**Użycie:**
```bash
php artisan tmdb:fetch
```

**Co robi:**
1. Dodaje `FetchTmdbDataJob` do kolejki
2. Job pobiera:
   - Wszystkie gatunki filmowe
   - 50 najpopularniejszych filmów
   - 10 najpopularniejszych seriali
3. Dla każdego elementu pobiera tłumaczenia na 3 języki
4. Zapisuje dane w bazie danych

**Retry logic:**
- 3 próby wykonania w przypadku błędu
- Exponential backoff: 1 min, 5 min, 15 min
- Szczegółowe logowanie błędów

## 🐛 Rozwiązywanie problemów

### Błąd: "TMDB token is not configured"

Upewnij się, że dodałeś token TMDB do pliku `.env`:
```env
TMDB_TOKEN=your_token_here
```

### Queue Job nie wykonuje się

Sprawdź czy Queue Worker jest uruchomiony:
```bash
# Lokalnie
php artisan queue:work

# Docker
docker-compose logs queue
```

### Vite manifest not found (strona /movies)

Zbuduj assety frontend:
```bash
# Docker
docker-compose exec app npm run build

# Lokalnie
npm run build
```

## 📄 Licencja

Ten projekt jest licencjonowany na zasadach MIT License.
