# Wprowadzenie

## Słownik pojęć

1. **UUID konta** / **ID klienta** identyfikator klienta w formacie UUID. Można go pobrać z Moje konto / Własna autoryzacja. UUID konta to synonim UUID użytkownika, który jest właścicielem konta. Każdy dodatkowy pracownik na koncie posiada swoje unikalne UUID.
2. **Własna autoryzacja** nazwa modułu w aplikacji Medfile App odpowiedzialna za wgranie klucza publicznego integratora.
3. **Medfile Auth Service** usługa autoryzacji.
4. **Medfile API (FHIR)** usługa API w standardzie FHIR.

## Aktorzy

| Aktor                    | Opis                                                                               |
|--------------------------|------------------------------------------------------------------------------------|
| **Medfile Auth Service** | Usługa autoryzacji — wystawia tokeny JWT (child token) na podstawie master token.  |
| **Medfile API (FHIR)**   | Serwer zasobów FHIR — obsługuje żądania autoryzowane child tokenem.                |
| **Medfile App**          | Aplikacja Medfile — interfejs użytkownika końcowego.                               |
| **Integrator / Klient**  | Strona integrująca się z API — tworzy master token i wykonuje żądania do FHIR API. |

## Wymagania wstępne — rejestracja klucza publicznego

Przed rozpoczęciem autoryzacji klient musi zarejestrować swój klucz publiczny RSA256 w aplikacji Medfile App:

1. Wygeneruj parę kluczy RSA256 (procedura opisana w [Specyfikacji Auth Service](./Specyfikacja-Medfile-Auth-Service.md)).
2. Zaloguj się do Medfile App i przejdź do **Moje Konto > Własna autoryzacja**.
3. Wgraj plik klucza publicznego (`public_key.pem`).

Jeśli usługa **Własna autoryzacja** nie jest dostępna na koncie, należy ją zakupić w **Marketplace** w aplikacji Medfile lub poprosić o aktywację wysyłając wiadomość na adres integracja@medfile.pl.

Klucz publiczny jest niezbędny do weryfikacji podpisu master token przez Auth Service.

## Schemat autoryzacji

```
┌────────┐         ┌───────────────┐         ┌──────────┐
│ Klient │         │  Auth Service │         │ FHIR API │
└───┬────┘         └──────┬────────┘         └────┬─────┘
    │                     │                       │
    │  1. master token    │                       │
    │────────────────────>│                       │
    │                     │                       │
    │  2. child token     │                       │
    │<────────────────────│                       │
    │                     │                       │
    │  3. Authorization: Bearer <child token>     │
    │────────────────────────────────────────────>│
    │                     │                       │
    │  4. FHIR response   │                       │
    │<────────────────────────────────────────────│
```

## Autoryzacja żądań

Dostęp do FHIR API wymaga tokenu JWT (child token) wystawionego przez Medfile Auth Service. Bezpośrednie tworzenie tokenów JWT nie jest możliwe.

### 1. Master token

Klient tworzy master token wskazując URL FHIR API endpoint (testowy lub produkcyjny). Nieprawidłowy URL spowoduje odrzucenie żądań.

### 2. Child token

Master token przesyłany jest do Auth Service, który zwraca child token o ograniczonym czasie ważności. Token podpisany jest kluczem serwera — modyfikacja payload spowoduje błąd walidacji sygnatury.

### 3. Użycie w żądaniach

```
Authorization: Bearer <child token>
```

Child token umieszczany jest w nagłówku `Authorization` każdego żądania do FHIR API.
