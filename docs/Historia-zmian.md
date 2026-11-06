# Historia zmian

Zmiany w Medfile FHIR API i w dokumentacji, od najnowszych.

# Spis treści

- [Historia zmian](#historia-zmian)
- [Spis treści](#spis-treści)
- [Wersja 1.4 (2026-11-06)](#wersja-14-2026-11-06)
- [Wersja 1.3 (2026-10-26)](#wersja-13-2026-10-26)
- [Wersja 1.2 (2026-10-03)](#wersja-12-2026-10-03)
- [Wersja 1.1 (2026-09-23)](#wersja-11-2026-09-23)
- [Wersja 1.0 (2026-03-22)](#wersja-10-2026-03-22)

# Wersja 1.4 (2026-11-06)

### Nowe funkcje

- Wiadomości SMS: odczyt i wyszukiwanie wiadomości SMS wysłanych do pacjenta i odebranych od pacjenta (zasób Communication) po pacjencie albo numerze telefonu oraz dacie wysłania. Numer telefonu dopasowywany jest niezależnie od zapisu (z prefiksem `+48`, `48` lub bez). Wiadomości z kodami logowania i hasłami do eksportu raportów nie są udostępniane. Patrz [Wiadomości SMS](./Przypadki-użycia-sms.md#wiadomości-sms).

### Dokumentacja

- Nowa strona [Wiadomości SMS](./Przypadki-użycia-sms.md#wiadomości-sms): pola zasobu, statusy, typy wiadomości, parametry wyszukiwania `patient`, `phone` i `sent`.

# Wersja 1.3 (2026-10-26)

### Nowe funkcje

- Recepty: odczyt i wyszukiwanie pozycji recept (zasób MedicationRequest) po pacjencie, wizycie i recepcie, wydruk recepty w formacie PDF (operacja `$print`) zgodny z tym, jak recepta została zamknięta - e-recepta z P1, recepta papierowa jako papierowa. Lek, dawkowanie i ubezpieczenie NFZ są częścią pozycji recepty. Patrz [Recepty](./Przypadki-użycia-recepty.md#recepty).
- Subskrypcje: powiadomienie o zamknięciu recepty - jeden raz na receptę, przy pierwszym wydruku recepty papierowej albo przy wystawieniu e-recepty w P1 (zdarzenie `fhir.subscription.topic.medicationrequest.closed`). Droga zamknięcia (`paper` | `p1`) w nagłówku `X-Medfile-Prescription-Closure` i w rozszerzeniu zdarzenia; filtr `closure` zawęża subskrypcję do jednej drogi. Patrz [Powiadomienia o zamknięciu recepty](./Przypadki-użycia-recepty.md#powiadomienia-o-zamknięciu-recepty).

### Dokumentacja

- Nowa strona [Recepty](./Przypadki-użycia-recepty.md#recepty): identyfikator pozycji recepty, pola zasobu, rozszerzenia, lek, ubezpieczenie NFZ, dawkowanie, wyszukiwanie, wydruk i powiadomienia.
- Subskrypcje: nowe zdarzenie zamknięcia recepty na liście obsługiwanych zdarzeń.

# Wersja 1.2 (2026-10-03)

### Nowe funkcje

- Usługi: dodawanie, odczyt, aktualizacja (PUT i PATCH), usuwanie i wyszukiwanie usług z cennika konta (zasób HealthcareService). Usługi Medfile są tylko do odczytu. Patrz [Usługi](./Przypadki-użycia-usługi.md#usługi).

### Dokumentacja

- Nowa strona [Usługi](./Przypadki-użycia-usługi.md#usługi): pola zasobu, rozszerzenia `healthcareservice-*`, usługi Medfile, parametry wyszukiwania.
- Wizyty: odnośnik do wyszukiwania usług przy ustalaniu UUID usługi wizyty.
- Słowniki: opis kategorii typu `services` jako kategorii usług.
- Swagger UI: przykłady żądań HealthcareService zgodne z obsługiwanym formatem (rozszerzenia `healthcareservice-*`, kategoria w polu `category`, XML w składni FHIR, osobne przykłady aktualizacji z polem `id`) oraz opis parametru wyszukiwania `name`.

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
