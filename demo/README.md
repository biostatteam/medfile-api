# Medfile API - DEMO Application

Aplikacja demo ma na celu szybką demonstrację sposobu autentykacji i używania API Medfile. 
Program tylko w celu demonstracyjnym używa otwartych paczek do obsługi tokenów JWT.

__Uwaga:__ Pamiętaj aby na środowisku testowym używać tylko fikcyjnych danych.

## 1. Konfiguracja klucza prywatnego 

Wgraj klucz prywatny do głównego katalogu aplikacji DEMO na swoim urządzeniu. 
Zwróć uwagę, aby nazwa i ścieżka pliku zgadzały się z parametrem `PEM_FILE` w pliku `.env` (zazwyczaj pod nazwą `private.pem`).

## 2. Zaktualizuj plik .env

Zastąp wszystkie wystąpienia `{{UUID}}` w pliku .env identyfikatorem UUID swojego konta w aplikacji Medfile na środowisku testowym.

__Uwaga:__ ID konta/UUID konta głównego - to synonimy.

## 3. Uruchom aplikację

Aby uruchomić aplikację, upewnij się, że posiadasz zainstalowane środowisko PHP (wersja 8.0+), a następnie wykonaj polecenie w konsoli z katalogu aplikacji:

```php
php index.php
```

__Uwaga:__ Uruchomienie skryptu na środowisku testowym spowoduje utworzenie testowego pacjenta na koncie klienta.
