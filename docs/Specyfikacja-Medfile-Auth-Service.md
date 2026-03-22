# Integracja Auth Service + Medfile/FHIR (krok po kroku)

W tej instrukcji znajdziesz kroki potrzebne do integracji autoryzacji z Auth Service oraz FHIR API.

# Architektura

W procesie uzyskiwania dostępu do systemów Medfile biorą udział następujący aktorzy:

- Klient, czyli podmiot żądający dostępu do wybranego systemu/usługi (np: Medfile FHIR API)
- AuthService, czyli centralny serwer autoryzacyjny weryfikujący tożsamość klienta na podstawie MasterToken oraz emitujący ChildToken, pozwalający na dostęp do systemu docelowego
- Aplikacja Medfile, przechowująca klucz publiczny w usłudze 'Własna autoryzacja'
- System docelowy (np. Medfile FHIR API) do którego klient chce uzyskać dostęp

Proces przebiega następująco:

1. Klient generuje klucze RSA256, którymi będzie podpisywał żądania autoryzacji
2. Klient rejestruje klucz publiczny na swoim koncie w aplikacji Medfile
3. Klient zachowuje klucz prywatny w celu podpisywania żądań autoryzacji
4. Klient przygotowuje żądanie do serwera autoryzacji (AuthService)
5. Klient tworzy token MasterToken JWT
6. Klient wykonuje żądanie do AuthService, autoryzując je MasterToken
7. AuthService zwraca ChildToken JWT
8. Klient odczytuje ChildToken i wykorzystuje go w celu autoryzacji żądania do docelowego systemu/usługi
9. Jeśli ChildToken straci swoją ważność, konieczne jest ponowne żądanie do AuthService w celu pobrania nowego

W dalszej części instrukcji, poszczególne kroki procesu zostały szczegółowo opisane.

## Co będzie potrzebne

- konto w aplikacji Medfile (np: testowe w środowisku UAT),
- aktywna usługa 'Własna autoryzacja' w Medfile
- Unikatowy identyfikator UUID konta, na które wgrywany będzie klucz publiczny (issuer) — widoczny w Medfile w linku do otwartego zasobu
- Unikatowy identyfikator UUID użytkownika lub ID konta, które ma być zautoryzowane w systemie docelowym — widoczny w Medfile w linku do otwartego zasobu
- para kluczy RSA256 (prywatny + publiczny),
- możliwość wykonania żądania HTTP do serwera autoryzacji (np: `auth-service.uat.rstat.pl` w środowisku UAT).

## Krok 1: Wygeneruj parę kluczy RS256

Poniższy przykład pozwala na wygenerowanie pary kluczy w systemie Linux:

```bash
openssl genpkey -algorithm RSA -out private_key.pem -pkeyopt rsa_keygen_bits:2048
openssl rsa -pubout -in private_key.pem -out public_key.pem
```

Uwaga: można użyć 4096 bitów.

## Krok 2: Wgraj klucz publiczny do Medfile

1. Zaloguj się do konta w aplikacji Medfile.
2. Upewnij się, że usługa 'Własna autoryzacja' jest aktywna. Jeśli nie, aktywuj ją.
3. Przejdź do konfiguracji usługi 'Własna autoryzacja' w Menu \> Moje Konto. (przykład linku w środowisku UAT `https://medfile2.uat.rstat.pl/authorization_jwt_manage`)
4. Wgraj plik w okienku wgrywania certyfikatu `public_key.pem`.

Ten krok jest wymagany, aby system mógł zweryfikować podpis MasterToken JWT.

## Krok 3: Zbuduj i wyślij Master Token (JWT RS256)

Zbudowany Master Token należy umieścić w nagłówku HTTP do Auth Service w nagłówku:

`Authorization: Bearer <master_token>`

Żądanie podpisane MasterToken w powyższy sposób należy skierować na poniższy endpoint AuthService, w celu pobrania ChildToken:

`GET https://{host}/api/auth/v1/token`

w przypadku środowiska UAT będzie to: `GET https://auth-service.uat.rstat.pl/api/auth/v1/token`

### Nagłówek MasterToken JWT

```json
{
  "typ": "JWT",
  "alg": "RS256"
}
```

### Payload MasterToken JWT

```json
{
  "iss": "<tu umieść UUID konta na które wgrany został certyfikat>",
  "aud": "<endpoint do pobrania ChildToken, np: auth-service.uat.rstat.pl/api/auth/v1/token>",
  "iat": <timestamp utworzenia tokenu>,
  "exp": <timestamp ważności tokenu>,
  "sub": "<podmiot autoryzacji wg składni login::<userUuid>::<audience>, patrz scenariusze poniżej>"
}
```

Uwaga: System docelowy identyfikowany jest przez segment w elemencie 'sub'.

- `iat` i `exp` podawaj jako Unix timestamp (sekundy).
- `exp` ustaw na krótki czas liczony od `iat` (np. +30 do +60 minut).

## Krok 4: Ustaw `sub` zależnie od celu autoryzacji

Odpowiednie ustawienie elementu `sub` jest kluczowe dla rozróżnienia systemu docelowego autoryzacji. Wartość segmentu `audience` odróżnia logowanie do aplikacji Medfile od dostępu do Medfile FHIR API. Poniżej opisano przykładowe scenariusze.

### Scenariusz A: Logowanie użytkownika do Medfile na podstawie JWT

W tym scenariuszu możliwe jest zalogowanie użytkownika wyłącznie na podstawie tokenu JWT dołączonego do linku, z pominięciem okna logowania. W tym celu należy umieścić docelową domenę aplikacji Medfile w segmencie `audience`. W przypadku środowiska UAT i serwera numer 2 będzie to:

`sub = login::<userUuid>::medfile2.uat.rstat.pl`

Po wykonaniu żądania do AuthService otrzymasz ChildToken dający dostęp do tej domeny aplikacji Medfile, który należy umieścić w linku w następujący sposób:

`https://{domena}/login/jwt?token=<token>`

Przykład w środowisku UAT: `https://medfile2.uat.rstat.pl/login/jwt?token=<token>`

### Scenariusz B: Uzyskiwanie dostępu do FHIR API

W tym scenariuszu możliwe jest zautoryzowanie aplikacji klienckiej FHIR API na podstawie tokenu JWT. W tym celu należy umieścić docelową domenę Medfile FHIR API endpoint w segmencie `audience`. W przypadku środowiska UAT będzie to:

`sub = login::<userUuid>::medfile-api.uat.rstat.pl`

Po wykonaniu żądania do AuthService otrzymasz ChildToken dający dostęp do API, który należy umieścić w poniższym nagłówku HTTP, w każdym żądaniu:

`Authorization: Bearer <child_token>`

## Krok 5: Testowanie FHIR API przez SwaggerUI

Uzyskany ChildToken można wykorzystać do autoryzacji żądań testowych za pośrednictwem strony dokumentacji online SwaggerUI.

Dokumentacja dostępna jest w domenie Medfile FHIR API endpoint, pod adresem: `http://{host}/fhirdoc`

W środowisku UAT będzie to:

`http://medfile-api.uat.rstat.pl/fhirdoc`

Dostęp do strony dokumentacji zabezpieczony jest metodą HTTP Basic. W celu otwarcia strony należy podać następujące poświadczenia w okienku autoryzacji wyświetlonym przez przeglądarkę. Proszę przesłać wniosek na integracja@medfile.pl w celu uzyskania danych autoryzacyjnych.

## Szybka checklista

Poniżej zaprezentowana jest szybka lista kontrolna wymaganych kroków autoryzacji.

1. Wygenerowałeś klucze RSA.
2. Klucz publiczny jest wgrany w Medfile.
3. Master Token jest podpisany prywatnym kluczem RS256.
4. `aud` wskazuje na odpowiednią instancję AuthService np: (`auth-service.uat.rstat.pl/api/auth/v1/token`).
5. `sub` odpowiada właściwemu scenariuszowi (`medfile2...` vs `medfile-api...`).
6. Otrzymany ChildToken został prawidłowo użyty (np: parametr query 'token' lub `Authorization` Header w przypadku FHIR API).

## Najczęstsze błędy

- Pomylenie `sub` dla loginu i FHIR API.
- Wysłanie niepodpisanego JWT lub podpisanego innym kluczem.
- Brak wgranego klucza publicznego w panelu Medfile.
- Niepoprawne `iat/exp` (np. token wygasły w momencie wysyłki).