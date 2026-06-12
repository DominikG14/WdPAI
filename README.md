# Zdasz.to

Zdasz.to to aplikacja webowa do nauki matematyki maturalnej. Użytkownik może wybrać dział matematyki, rozwiązać zestaw losowych zadań, sprawdzić wynik oraz zapisać postęp na swoim koncie. Aplikacja zawiera także panel administratora do zarządzania użytkownikami i zadaniami.

## Spis Treści

- [Funkcje](#funkcje)
- [Technologie](#technologie)
- [Uruchomienie](#uruchomienie)
- [Konta Testowe](#konta-testowe)
- [Flow Aplikacji](#flow-aplikacji)
- [Panel Administratora](#panel-administratora)
- [Bezpieczeństwo](#bezpieczenstwo)
- [Baza Danych](#baza-danych)
- [Screeny Do Dodania](#screeny-do-dodania)
- [Struktura Projektu](#struktura-projektu)

## Funkcje

- wybór działu matematyki i liczby zadań,
- losowanie zadań z różnych działów,
- rozwiązywanie zadań typu `ABCD` oraz `PF`,
- podsumowanie wyniku po zakończeniu testu,
- zapis postępów zalogowanego użytkownika,
- możliwość zalogowania lub rejestracji po rozwiązaniu testu i zapisania wyniku,
- dashboard użytkownika z historią podejść i podsumowaniem według działów,
- panel administratora z listą użytkowników, wyszukiwaniem i podglądem postępów,
- panel administratora do dodawania i przeglądania zadań,
- responsywny interfejs dla telefonów,
- całość aplikacji działa przez HTTPS w środowisku Docker.

## Technologie

- PHP 8.3
- PostgreSQL
- Nginx
- Docker Compose
- HTML, CSS, JavaScript
- Font Awesome
- pgAdmin

## Uruchomienie

1. Sklonuj lub rozpakuj projekt.
2. Upewnij się, że istnieje plik `.env` w katalogu głównym.
3. Uruchom kontenery:

```bash
docker compose up -d --build
```

4. Otwórz aplikację:

```text
https://localhost:8443/index
```

5. Panel pgAdmin:

```text
http://localhost:5050
```

Domyślne wartości środowiskowe znajdują się w pliku `.env`.

## Konta Testowe

Hasło dla przykładowych kont użytkowników:

```text
Password123
```

Przykładowe konto użytkownika:

```text
email: mistrz@example.com
hasło: Password123
```

Konto administratora:

```text
email: admin@example.com
hasło: haslo
```

> Jeśli potrzebujesz administratora na innym koncie, nadaj mu rolę `ADMIN` w bazie.

## Flow Aplikacji

### 1. Strona Główna

Użytkownik wchodzi na stronę główną, gdzie widzi listę działów matematycznych oraz przycisk do losowania zadań mieszanych.

Po kliknięciu działu aplikacja otwiera modal wyboru liczby zadań. Użytkownik może wpisać liczbę ręcznie albo wybrać jeden z szybkich przycisków.

### 2. Rozwiązywanie Zadań

Po rozpoczęciu testu użytkownik widzi jedno zadanie naraz. Po kliknięciu odpowiedzi aplikacja przechodzi do kolejnego zadania.

Użytkownik może zakończyć test wcześniej. Wtedy aplikacja pyta o potwierdzenie i podsumowuje tylko udzielone odpowiedzi.

### 3. Podsumowanie Wyniku

Po zakończeniu testu aplikacja pokazuje wynik punktowy. Dla zalogowanych użytkowników wynik jest zapisywany w bazie danych.

Jeśli użytkownik nie jest zalogowany, może przejść do logowania lub rejestracji. Wynik testu zostaje tymczasowo zapisany w `sessionStorage`, a po udanym logowaniu lub rejestracji aplikacja wraca do testu i zapisuje postęp.

### 4. Dashboard Użytkownika

Po zalogowaniu użytkownik może wejść do panelu postępów. Dashboard pokazuje:

- historię wszystkich podejść,
- wynik punktowy,
- skuteczność procentową,
- datę wykonania,
- podsumowanie według działów.

### 5. Panel Administratora

Administrator ma dostęp do:

- listy użytkowników,
- wyszukiwarki użytkowników,
- usuwania użytkowników,
- podglądu postępów wybranego użytkownika,
- listy zadań,
- dodawania nowych zadań,
- filtrowania zadań według działu,

## Panel Administratora

Panel administratora jest dostępny tylko dla użytkowników z rolą `ADMIN`.

Najważniejsze widoki:

- `/admin/users` - lista użytkowników,
- `/admin/users/progress/{id}` - postępy wybranego użytkownika,
- `/admin/exercises` - lista i dodawanie zadań.

Jeśli zwykły użytkownik spróbuje wejść do panelu admina, aplikacja zwróci kod `403` i przekieruje go na stronę główną.

## Bezpieczenstwo

W projekcie zaimplementowano:

- HTTPS dla całej aplikacji,
- przekierowanie HTTP na HTTPS,
- cookie sesyjne z flagami `Secure`, `HttpOnly`, `SameSite=Strict`,
- walidację długości pól formularzy,
- walidację złożoności hasła,
- hashowanie haseł przez `password_hash`,
- weryfikację hasła przez `password_verify`,
- ochronę przed open redirect przez walidację `returnUrl`,
- sensowniejsze kody HTTP, np. `400`, `401`, `403`, `404`, `405`, `409`,
- logowanie nieudanych prób logowania bez zapisywania haseł,
- escapowanie danych w widokach przez `htmlspecialchars`,
- pobieranie minimalnego zestawu danych o użytkownikach w panelu admina,
- singleton dla `UsersRepository`.

## Baza Danych

Główne tabele:

- `users` - konta użytkowników,
- `roles` - role w systemie,
- `user_roles` - relacja użytkownik-rola,
- `fields` - działy matematyki,
- `exercises` - zadania,
- `user_progress` - zapisane wyniki testów.

W `init.sql` dodano także widoki:

- `v_exercise_catalog` - katalog zadań z nazwą i numerem działu,
- `v_field_exercise_stats` - liczba zadań w działach z podziałem na typy,
- `v_user_progress_summary` - zbiorcze statystyki użytkowników,
- `v_user_field_progress_summary` - statystyki użytkowników per dział.

Plik inicjalizacji bazy:

```text
docker/db/init/init.sql
```

### 🏠 Strona Główna
![Strona główna](/docs/screens/home.png)

*Główny punkt wejściowy do aplikacji. Zawiera intuicyjne nawigowanie, szybki dostęp do rozpoczęcia testu oraz krótką prezentację możliwości systemu.*

### 📊 Dashboard Użytkownika
![Dashboard użytkownika](/docs/screens/dashboard.png)

*Spersonalizowany panel, w którym uczeń może śledzić swoją historię postępów, statystyki.*

### ⚙️ Konfiguracja Testu
![Modal wyboru liczby zadań](/docs/screens/exercise-count-modal.png)

*Pozwala użytkownikowi na szybkie określenie preferowanej liczby zadań do rozwiązania, z opcją wyboru wartości domyślnych (szybki wybór).*

### 📝 Rozwiązywanie Zadań
![Rozwiązywanie zadań](/docs/screens/quiz.png)

*Ekran właściwego testu. Interfejs skupiony na zadaniu, zapewniający czytelność treści matematycznych oraz łatwy sposób zaznaczania odpowiedzi.*

### 🏆 Podsumowanie Wyników
![Podsumowanie testu](/docs/screens/summary.png)

*Po zakończeniu testu użytkownik otrzymuje zestawienie swoich odpowiedzi wraz z wynikiem punktowym, co pozwala na błyskawiczną analizę błędów.*

### 🔐 Autoryzacja i Dostęp
#### Logowanie
![Logowanie](/docs/screens/login.png)

*Bezpieczny formularz logowania z przejrzystym układem pól, zaprojektowany z myślą o szybkiej autoryzacji użytkownika.*

#### Rejestracja
![Rejestracja](/docs/screens/register.png)

*Formularz pozwalający na dołączenie do platformy.*

### 🛡️ Panel Administratora
#### Zarządzanie Użytkownikami
![Panel administratora - użytkownicy](/docs/screens/admin-users.png)

*Narzędzie administracyjne umożliwiające podgląd zarejestrowanych użytkowników.*

#### Zarządzanie Bazą Zadań
![Panel administratora - zadania](/docs/screens/admin-exercises.png)

*Moduł zarządzania bazą zadań: dodawanie nowych treści, filtrowanie po działach oraz podgląd graficzny zdjęć zadań.*

### 📱 Responsywność (Mobile)
![Wersja mobilna](/docs/screens/mobile-home.png)

*Responsywny widok strony głównej, w pełni dostosowany do urządzeń mobilnych dla wygody nauki w dowolnym miejscu.*

## Struktura Projektu

```text
.
├── docker/
│   ├── db/
│   │   └── init/init.sql
│   ├── nginx/
│   │   └── nginx.conf
│   └── php/
├── public/
│   ├── scripts/
│   ├── styles/
│   ├── images/
│   └── views/
├── src/
│   ├── controllers/
│   ├── models/
│   └── repositories/
├── Database.php
├── Routing.php
├── config.php
├── docker-compose.yaml
└── index.php
```

## Najważniejsze Endpointy

```text
GET  /index
GET  /login
POST /login
GET  /register
POST /register
GET  /dashboard
GET  /exercises/field/{id}
GET  /exercises/random
POST /exercises/save
GET  /admin/users
POST /admin/users/search
GET  /admin/users/progress/{id}
GET  /admin/users/delete/{id}
GET  /admin/exercises
POST /admin/exercises/create
GET  /logout
```