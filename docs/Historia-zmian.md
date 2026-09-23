# Historia zmian

Zmiany w Medfile FHIR API i w dokumentacji, od najnowszych.

# Spis treści

- [Historia zmian](#historia-zmian)
- [Spis treści](#spis-treści)
- [Wersja 1.1 (2026-09-23)](#wersja-11-2026-09-23)
- [Wersja 1.0 (2026-03-22)](#wersja-10-2026-03-22)

# Wersja 1.1 (2026-09-23)

### Nowe funkcje

- Słowniki: odczyt słowników systemowych (CodeSystem `entity-category-type`, `entity-category`) oraz zarządzanie słownikami konta (ValueSet `entity-category`, `entity-tag`) operacjami `$add`, `$remove` i PATCH. Patrz [Słowniki](./Przypadki-użycia-słowniki.md#słowniki).
- Wizyty: odczyt i zmiana etykiet wizyty operacjami `$meta`, `$meta-add` i `$meta-delete`. Etykiety zwracane są również w polu `meta.tag` zasobu Appointment. Patrz [Etykiety (tagi) wizyty](./Przypadki-uzycia.md#etykiety-tagi-wizyty).
- Pacjenci: wyszukiwanie pacjentów po numerze telefonu (parametr `phone`).

### Zmiany wymagające uwagi integratorów

- Autoryzacja: ChildToken musi zawierać identyfikatory w formacie UUID (identyfikatory liczbowe są odrzucane) oraz datę wygaśnięcia `exp`. Patrz [Specyfikacja Medfile Auth Service](./Specyfikacja-Medfile-Auth-Service.md).
- Wyszukiwanie: parametry wyszukiwania przekazywane są wyłącznie w query string. Parametry w treści żądania (`application/x-www-form-urlencoded`) nie są odczytywane. Patrz [Wyszukiwanie zasobów](./Specyfikacja-protokolu.md#wyszukiwanie-zasobów).

### Dokumentacja

- Opis typów zdarzeń subskrypcji.
- Nowa sekcja [Kody odpowiedzi HTTP](./Specyfikacja-protokolu.md#kody-odpowiedzi-http).
- Uzupełnione opisy: wyszukiwanie załączników (DocumentReference), odczyt danych lekarza, parametry wyszukiwania subskrypcji, limity parametrów `_page` i `_count`.
- Poprawione przykłady: metoda i adres aktualizacji pacjenta (`PUT /fhir/Patient/{id}`) i lekarza (`PUT /fhir/Practitioner/{id}`), zasób w wyszukiwaniu pacjentów, format kodów kwalifikacji lekarza, nagłówki i placeholdery w przykładach załączników i subskrypcji.
- Dane w przykładach zastąpione danymi fikcyjnymi.
- Aplikacja demonstracyjna: wymagane PHP 8.2 - 8.4 z rozszerzeniem `sodium`, poprawiony format dat w tokenie JWT oraz nagłówek `Content-Type` w żądaniach wyszukiwania.

# Wersja 1.0 (2026-03-22)

Pierwsza publiczna wersja dokumentacji: pacjenci, wizyty, lekarze, załączniki, subskrypcje i lokalizacje.
