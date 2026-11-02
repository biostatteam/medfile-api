# Recepty

Zasób MedicationRequest reprezentuje jedną pozycję recepty, czyli jeden przepisany lek. Recepta (pakiet) z kilkoma lekami to kilka zasobów MedicationRequest o wspólnym polu `groupIdentifier`.

Recepty wystawia i wysyła do systemu P1 lekarz w aplikacji Medfile. API udostępnia je tylko do odczytu: odczyt pozycji, wyszukiwanie, wydruk recepty (PDF) oraz powiadomienia o zamknięciu recepty (wydruk recepty papierowej lub wystawienie e-recepty w P1). Przepisany lek jest częścią pozycji recepty (zasób osadzony Medication), a nie osobnym zasobem.

Wszystkie operacje obsługują formaty JSON i XML. Przykłady poniżej podano w formacie JSON, przykłady XML dostępne są w dokumentacji online Swagger UI ([https://medfile-api.uat.rstat.pl/fhirdoc](https://medfile-api.uat.rstat.pl/fhirdoc)).

> ![FHIR logo](https://fhir.org/assets/images/HL7_FHIR-stacked_1_0_0.png)
> __Dokumentacja zasobów FHIR__
>- MedicationRequest - [https://www.hl7.org/fhir/medicationrequest.html](https://www.hl7.org/fhir/medicationrequest.html)
>- Medication - [https://www.hl7.org/fhir/medication.html](https://www.hl7.org/fhir/medication.html)
>- Coverage - [https://www.hl7.org/fhir/coverage.html](https://www.hl7.org/fhir/coverage.html)
>- Dosage - [https://www.hl7.org/fhir/dosage.html](https://www.hl7.org/fhir/dosage.html)

# Spis treści

- [Recepty](#recepty)
- [Spis treści](#spis-treści)
- [Identyfikator pozycji recepty](#identyfikator-pozycji-recepty)
- [Pola zasobu](#pola-zasobu)
  - [Status](#status)
  - [Identyfikatory](#identyfikatory)
  - [Rozszerzenia](#rozszerzenia)
  - [Lek](#lek)
  - [Ubezpieczenie NFZ](#ubezpieczenie-nfz)
  - [Dawkowanie](#dawkowanie)
- [Pobieranie pozycji recepty](#pobieranie-pozycji-recepty)
- [Wyszukiwanie pozycji recept](#wyszukiwanie-pozycji-recept)
  - [Obsługiwane parametry wyszukiwania](#obsługiwane-parametry-wyszukiwania)
- [Wydruk recepty](#wydruk-recepty)
- [Powiadomienia o zamknięciu recepty](#powiadomienia-o-zamknięciu-recepty)
- [Operacje nieobsługiwane](#operacje-nieobsługiwane)

# Identyfikator pozycji recepty

Identyfikator (`id`) pozycji recepty ma postać `{uuid recepty}-{numer pozycji}`, np. `3f2b8c1e-5a47-4d9b-9e21-7c6d0a1b2c3d-1024`.

- Część przed ostatnim myślnikiem to UUID recepty - ta sama wartość co w polu `groupIdentifier`, wspólna dla wszystkich leków recepty.
- Identyfikator należy traktować jako całość i pobierać z wyników wyszukiwania lub z powiadomienia o zamknięciu recepty. Sam numer pozycji (bez UUID recepty) ani numer pozycji z UUID innej recepty nie pozwalają odczytać pozycji - serwer zwraca wtedy kod `400 Bad Request`.

# Pola zasobu

| Pole | Opis |
|---|---|
| `id` | identyfikator pozycji recepty, patrz [Identyfikator pozycji recepty](#identyfikator-pozycji-recepty) |
| `groupIdentifier` | UUID recepty (pakietu), `system` = `http://www.medfile.pl/fhir/NamingSystem/medicationrequest-group-identifier`. Wspólny dla wszystkich leków recepty. |
| `identifier` | identyfikatory recepty i pozycji, patrz [Identyfikatory](#identyfikatory) |
| `status` | status pozycji recepty, patrz [Status](#status) |
| `statusChanged` | data wysłania (status `active`) albo anulowania (status `cancelled`) recepty |
| `intent` | zawsze `original-order` |
| `category` | kategoria dostępności leku, `system` = `http://www.medfile.pl/fhir/ValueSet/medication-availability-category`: `Rp`, `Rpz`, `Lz`, `OTC`. Drugi wpis, `system` = `http://www.medfile.pl/fhir/ValueSet/medication-type`, podaje rodzaj: `PL` - produkt leczniczy, `WM` - wyrób medyczny, `SZ` - środek spożywczy specjalnego przeznaczenia. |
| `priority` | `urgent` - recepta „cito”, `routine` - pozostałe |
| `medication` | odwołanie `#medication` do leku osadzonego w zasobie, patrz [Lek](#lek) |
| `subject` | pacjent, `Patient/{uuid}` |
| `encounter` | wizyta, na której wystawiono receptę, `Encounter/{uuid}`. Recepta pozostaje dostępna także po usunięciu wizyty. |
| `authoredOn` | data i godzina wystawienia recepty |
| `recorder` | użytkownik, który zapisał receptę, `Practitioner/{uuid}` |
| `requester` | lekarz wystawiający receptę, `Practitioner/{uuid}`, wraz z placówką (rozszerzenie `requester-organization`, patrz [Rozszerzenia](#rozszerzenia)) |
| `performer` | asystent medyczny, tylko gdy recepta ma jego dane |
| `dosageInstruction` | dawkowanie, patrz [Dawkowanie](#dawkowanie) |
| `dispenseRequest.quantity` | ilość leku do wydania, np. `1 op.` |
| `dispenseRequest.validityPeriod` | okres ważności recepty |
| `dispenseRequest.numberOfRepeatsAllowed` | liczba dozwolonych powtórzeń realizacji |
| `substitution.allowedBoolean` | `false` - lekarz zaznaczył „nie zamieniać” |
| `insurance` | odwołanie `#nfz` do ubezpieczenia NFZ osadzonego w zasobie, patrz [Ubezpieczenie NFZ](#ubezpieczenie-nfz) |
| `note` | komentarz do pozycji recepty |

Pola bez wartości są pomijane - zasób nie zawiera pustych napisów ani pustych elementów.

## Status

| Status FHIR | Recepta w Medfile |
|---|---|
| `draft` | nowa, niewysłana do P1; także recepta papierowa (nie przechodzi przez P1, więc po wydruku nadal ma ten status) |
| `active` | wystawiona w P1 |
| `on-hold` | częściowo zrealizowana - wg statusu odczytanego z P1 |
| `completed` | zrealizowana - wg statusu odczytanego z P1 |
| `cancelled` | anulowana |
| `stopped` | zablokowana |
| `unknown` | częściowo anulowana |

Medfile nie śledzi realizacji recept na bieżąco. Statusy `on-hold` i `completed` pochodzą z P1 i są aktualne na chwilę ostatniego sprawdzenia statusu e-recepty w aplikacji Medfile - e-recepta zrealizowana w aptece może mieć nadal status `active`, dopóki ktoś nie sprawdzi jej statusu w Medfile. Dla recept papierowych Medfile nie ma informacji o realizacji.

## Identyfikatory

| `system` | Wartość |
|---|---|
| `http://www.medfile.pl/fhir/NamingSystem/medicationrequest-number` | numer recepty w Medfile |
| `http://www.medfile.pl/fhir/NamingSystem/medicationrequest-csioz-key` | klucz recepty w P1 (recepta wysłana) |
| `http://www.medfile.pl/fhir/NamingSystem/medicationrequest-csioz-code` | kod recepty w P1 (recepta wysłana) |
| `http://www.medfile.pl/fhir/NamingSystem/medication-csioz-key` | klucz pozycji recepty w P1 - w P1 każdy lek jest osobną e-receptą |
| `urn:oid:2.16.840.1.113883.3.4424.7.2.1` | identyfikator dokumentu recepty |
| `http://www.medfile.pl/fhir/NamingSystem/medicationrequest-practitioner-prescription-identifier`, `...-practitioner-prescription-number` | numer z puli numerów recept lekarza (recepta papierowa z lekiem refundowanym) |

## Rozszerzenia

| `url` | Element | Typ wartości | Opis |
|---|---|---|---|
| `urn:oid:2.16.840.1.113883.3.4424.13.11.7` | MedicationRequest | valueCode | tryb wystawienia recepty (P1): `Z` - zwykła, `P` - pielęgniarska, `PL` - pielęgniarska na zlecenie lekarza |
| `urn:oid:2.16.840.1.113883.3.4424.13.11.16` | MedicationRequest | valueCode | poziom odpłatności (P1): `100%`, `50%`, `30%`, `R` - ryczałt, `B` - bezpłatny |
| `urn:oid:2.16.840.1.113883.3.4424.13.11.85` | MedicationRequest | valueCode | rodzaj recepty (P1): `ZW` - zwykła, `PA` - pro auctore, `PF` - pro familiae |
| `http://www.medfile.pl/fhir/ValueSet/medicationrequest-extension#requester-organization` | `requester` | valueReference | placówka lekarza; `identifier` w postaci jak w P1 - patrz niżej |
| `http://www.medfile.pl/fhir/ValueSet/medicationrequest-extension#document-identifier` | `subject` | valueString | identyfikator dokumentu potwierdzającego uprawnienie pacjenta |
| `http://www.medfile.pl/fhir/ValueSet/medicationrequest-extension#medical-assistant-name`, `#medical-assistant-lastname`, `#medical-assistant-identifier` | `performer` | valueString | dane asystenta medycznego |

Identyfikator placówki w rozszerzeniu `requester-organization` ma postać jak w systemie P1: numer księgi rejestrowej (część I kodu resortowego, 12 cyfr) połączony z częścią V (jednostka organizacyjna) lub VII (komórka organizacyjna) kodu resortowego, a dla praktyki zawodowej z kodem miejsca udzielania świadczeń.

| `system` | Placówka | Przykład wartości |
|---|---|---|
| `urn:oid:2.16.840.1.113883.3.4424.2.3.2` | jednostka organizacyjna | `000000012345-01` |
| `urn:oid:2.16.840.1.113883.3.4424.2.3.3` | komórka organizacyjna | `000000012345-001` |
| `urn:oid:2.16.840.1.113883.3.4424.2.4.{kod izby}.1` | praktyka lekarska | `000000012345-01` |

Identyfikator jest pomijany, gdy dane placówki są niepełne (np. brak numeru księgi rejestrowej).

## Lek

Przepisany lek to zasób Medication osadzony w pozycji recepty (`contained`, `id` = `medication`), wskazywany przez pole `medication`.

| Pole | Opis |
|---|---|
| `code.text` | nazwa leku tak jak na wydruku recepty: nazwa handlowa, postać, opakowanie i dawka, np. `Apap Tabletki powlekane 6 tabl. (500 mg)`. Dla receptury własnej - jej opis. |
| `code.coding` | kody leku: `urn:oid:1.3.160` - kod EAN (GTIN), `urn:oid:2.16.840.1.113883.3.4424.6.1` - identyfikator produktu leczniczego z Rejestru Produktów Leczniczych (URPL), `urn:oid:2.16.840.1.113883.3.4424.11.1.25` - kategoria dostępności. Kody występują tylko, gdy lek je ma - leki recepturowe nie mają kodu EAN ani URPL, wyroby medyczne nie mają kodu URPL. |
| `doseForm.text` | postać leku, np. `Tabletki powlekane` |
| `marketingAuthorizationHolder.display` | producent |
| `ingredient` | substancje czynne i składniki leku recepturowego |

Kod EAN nie identyfikuje pozycji recepty: lek może go nie mieć, a ten sam kod EAN może wystąpić w kilku pozycjach jednej recepty.

## Ubezpieczenie NFZ

Gdy pacjent jest ubezpieczony w NFZ, a lek jest refundowany (poziom odpłatności inny niż `100%`), pozycja recepty zawiera osadzony zasób Coverage (`id` = `nfz`) wskazywany przez pole `insurance`. Dane pochodzą z recepty z chwili jej wystawienia.

| Pole | Opis |
|---|---|
| `status` | `active` |
| `kind` | `insurance` |
| `beneficiary` | pacjent, `Patient/{uuid}` |
| `insurer.identifier` | oddział wojewódzki NFZ pacjenta, `system` = `urn:oid:2.16.840.1.113883.3.4424.3.1`, `value` = kod oddziału, np. `12` |
| `class` | uprawnienie dodatkowe pacjenta, gdy dotyczy tego leku: `type.coding` = `urn:oid:2.16.840.1.113883.3.4424.13.5.1` / `RLUD`, `value` = kod uprawnienia (`system` = `urn:oid:2.16.840.1.113883.3.4424.11.3.1`), np. `S`, `DZ`, `C`, `IB` |

Uprawnienia zależne od leku (`S`, `DZ`, `C`) podawane są tylko wtedy, gdy lek je dopuszcza i zostały zastosowane na recepcie. Pozostałe uprawnienia (np. `IB`, `AZ`) podawane są zawsze.

## Dawkowanie

Pole `dosageInstruction` zawiera dawkowanie pozycji recepty.

Dawkowanie tekstowe to jeden element z polem `text` (dawka i okres stosowania), a dla wyrobu medycznego z polem `patientInstruction` i czasem stosowania w `timing.repeat.duration`.

Dawkowanie strukturalne to kilka elementów:

- każdy etap dawkowania jest osobnym elementem z kolejnym numerem `sequence`; elementy o różnych numerach stosuje się po kolei, w kolejności numerów,
- `timing.repeat` określa czas trwania etapu (`duration`, `durationUnit`), okres (`period`, `periodUnit`) i liczbę podań w okresie (`frequency`); jednostki: `h`, `d`, `wk`, `mo`,
- dawki w różnych porach dnia są osobnymi elementami o tym samym numerze `sequence`, z porą dnia w `timing.repeat.when` (`MORN` - rano, `NOON` - w południe, `AFT` - po południu, `EVE` - wieczorem) i własną dawką,
- dawka to `doseAndRate.doseQuantity` (ilość z jednostką) albo `doseAndRate.doseRange` (zakres),
- przerwa w stosowaniu leku to etap z dawką `0`,
- `asNeededFor` - stosowanie w razie potrzeby, `maxDosePerAdministration` - dawka maksymalna,
- `text` - instrukcja do etapu, `patientInstruction` - informacja dla pacjenta.

Przykład: przez 7 dni 3 razy dziennie po 1 tabletce, tydzień przerwy, następnie przez 14 dni 2 tabletki rano i pół tabletki wieczorem:

```json
"dosageInstruction": [
    {
        "sequence": 1,
        "text": "po posiłku",
        "timing": { "repeat": { "duration": 7, "durationUnit": "d", "frequency": 3, "period": 24, "periodUnit": "h" } },
        "doseAndRate": [ { "doseQuantity": { "value": 1, "unit": "tabl." } } ]
    },
    {
        "sequence": 2,
        "text": "przerwa",
        "timing": { "repeat": { "duration": 1, "durationUnit": "wk", "frequency": 1, "period": 1, "periodUnit": "wk" } },
        "doseAndRate": [ { "doseQuantity": { "value": 0, "unit": "tabl." } } ]
    },
    {
        "sequence": 3,
        "text": "na czczo",
        "timing": { "repeat": { "duration": 14, "durationUnit": "d", "frequency": 1, "period": 1, "periodUnit": "d", "when": [ "MORN" ] } },
        "doseAndRate": [ { "doseQuantity": { "value": 2, "unit": "tabl." } } ]
    },
    {
        "sequence": 3,
        "timing": { "repeat": { "duration": 14, "durationUnit": "d", "frequency": 1, "period": 1, "periodUnit": "d", "when": [ "EVE" ] } },
        "doseAndRate": [ { "doseQuantity": { "value": 0.5, "unit": "tabl." } } ]
    }
]
```

# Pobieranie pozycji recepty

1. Użyj identyfikatora pozycji recepty (patrz [Identyfikator pozycji recepty](#identyfikator-pozycji-recepty)).

```
GET /fhir/MedicationRequest/{id}
Accept: application/fhir+json
```

Przykład odpowiedzi JSON:
```json
{
    "resourceType": "MedicationRequest",
    "id": "3f2b8c1e-5a47-4d9b-9e21-7c6d0a1b2c3d-1024",
    "contained": [
        {
            "resourceType": "Medication",
            "id": "medication",
            "code": {
                "coding": [
                    { "system": "urn:oid:1.3.160", "code": "05909990296026" },
                    { "system": "urn:oid:2.16.840.1.113883.3.4424.6.1", "code": "100006494" },
                    { "system": "urn:oid:2.16.840.1.113883.3.4424.11.1.25", "code": "OTC" }
                ],
                "text": "Apap Tabletki powlekane 6 tabl. (500 mg)"
            },
            "marketingAuthorizationHolder": { "display": "US Pharmacia Sp. z o.o." },
            "doseForm": { "text": "Tabletki powlekane" },
            "ingredient": [
                {
                    "item": { "reference": { "type": "Substance", "display": "Paracetamolum" } },
                    "isActive": true,
                    "strengthQuantity": {
                        "extension": [
                            {
                                "url": "http://www.medfile.pl/fhir/ValueSet/medication-extension#amount_by_word",
                                "valueString": "pięćset miligramów"
                            }
                        ],
                        "value": 500,
                        "unit": "mg"
                    }
                }
            ]
        },
        {
            "resourceType": "Coverage",
            "id": "nfz",
            "status": "active",
            "kind": "insurance",
            "beneficiary": { "reference": "Patient/0b7a6c5d-4e3f-4a2b-8c1d-9e8f7a6b5c4d" },
            "insurer": {
                "identifier": { "system": "urn:oid:2.16.840.1.113883.3.4424.3.1", "value": "12" },
                "display": "Narodowy Fundusz Zdrowia"
            },
            "class": [
                {
                    "type": {
                        "coding": [ { "system": "urn:oid:2.16.840.1.113883.3.4424.13.5.1", "code": "RLUD" } ],
                        "text": "Uprawnienie dodatkowe"
                    },
                    "value": { "system": "urn:oid:2.16.840.1.113883.3.4424.11.3.1", "value": "S" }
                }
            ]
        }
    ],
    "extension": [
        { "url": "urn:oid:2.16.840.1.113883.3.4424.13.11.7", "valueCode": "Z" },
        { "url": "urn:oid:2.16.840.1.113883.3.4424.13.11.16", "valueCode": "50%" },
        { "url": "urn:oid:2.16.840.1.113883.3.4424.13.11.85", "valueCode": "ZW" }
    ],
    "identifier": [
        { "system": "http://www.medfile.pl/fhir/NamingSystem/medicationrequest-number", "value": "RC/2026/15" },
        { "system": "http://www.medfile.pl/fhir/NamingSystem/medicationrequest-csioz-key", "value": "1000000000000000000000000000000000000000001" },
        { "system": "http://www.medfile.pl/fhir/NamingSystem/medicationrequest-csioz-code", "value": "0000" },
        { "system": "http://www.medfile.pl/fhir/NamingSystem/medication-csioz-key", "value": "1000000000000000000000000000000000000000002" },
        { "system": "urn:oid:2.16.840.1.113883.3.4424.7.2.1", "value": "MEDFILE0000000001024PR" }
    ],
    "groupIdentifier": {
        "system": "http://www.medfile.pl/fhir/NamingSystem/medicationrequest-group-identifier",
        "value": "3f2b8c1e-5a47-4d9b-9e21-7c6d0a1b2c3d"
    },
    "status": "active",
    "statusChanged": "2026-10-20T10:15:42+02:00",
    "intent": "original-order",
    "category": [
        {
            "coding": [ { "system": "http://www.medfile.pl/fhir/ValueSet/medication-availability-category", "code": "Rp" } ]
        },
        {
            "coding": [ { "system": "http://www.medfile.pl/fhir/ValueSet/medication-type", "code": "PL" } ]
        }
    ],
    "priority": "routine",
    "medication": { "reference": { "reference": "#medication", "type": "Medication" } },
    "subject": { "reference": "Patient/0b7a6c5d-4e3f-4a2b-8c1d-9e8f7a6b5c4d" },
    "encounter": { "reference": "Encounter/1c2d3e4f-5a6b-4c7d-8e9f-0a1b2c3d4e5f" },
    "insurance": [ { "reference": "#nfz", "type": "Coverage" } ],
    "authoredOn": "2026-10-20T10:12:05+02:00",
    "requester": {
        "extension": [
            {
                "url": "http://www.medfile.pl/fhir/ValueSet/medicationrequest-extension#requester-organization",
                "valueReference": {
                    "reference": "Organization/1",
                    "identifier": { "system": "urn:oid:2.16.840.1.113883.3.4424.2.3.3", "value": "000000012345-001" }
                }
            }
        ],
        "reference": "Practitioner/7e6d5c4b-3a29-4180-9f7e-6d5c4b3a2918"
    },
    "recorder": { "reference": "Practitioner/7e6d5c4b-3a29-4180-9f7e-6d5c4b3a2918" },
    "dosageInstruction": [
        { "text": "1 tabl. 3 razy dziennie" }
    ],
    "dispenseRequest": {
        "validityPeriod": { "start": "2026-10-20T00:00:00+02:00" },
        "numberOfRepeatsAllowed": 0,
        "quantity": { "value": 1, "unit": "op." }
    },
    "substitution": { "allowedBoolean": true }
}
```

# Wyszukiwanie pozycji recept

Operacja pobiera listę pozycji recept zautoryzowanego konta, od najnowszych. Pozycje recept usuniętych nie są zwracane. Pozycje recept, których wizytę usunięto, są zwracane.

```
POST /fhir/MedicationRequest/_search?patient={uuid pacjenta}&_page=1&_count=20
Content-Type: application/fhir+json;fhirVersion=5.0
Accept: application/fhir+json
```

Aby pobrać wszystkie leki jednej recepty, wyszukaj po UUID recepty (`group-identifier`):

```
POST /fhir/MedicationRequest/_search?group-identifier={uuid recepty}
Content-Type: application/fhir+json;fhirVersion=5.0
Accept: application/fhir+json
```

#### Obsługiwane parametry wyszukiwania

Poniższa tabela obejmuje aktualnie obsługiwane parametry wyszukiwania. Parametry można łączyć. Parametr spoza listy zwraca kod `400 Bad Request`.

| Nazwa | Opis | Komparatory |
|---|---|---|
| _page | numer strony wyników wyszukiwania (domyślnie 1, maksymalnie 100) | - |
| _count | ilość wyników wyszukiwania na stronę (domyślnie 5, maksymalnie 200); `_count=0` zwraca tylko liczbę wyników (`total`) | - |
| patient | UUID pacjenta | eq |
| encounter | UUID wizyty | eq |
| group-identifier | UUID recepty (wartość pola `groupIdentifier`) | eq |

# Wydruk recepty

Operacja `$print` zwraca wydruk recepty w formacie PDF.

```
GET /fhir/MedicationRequest/{id}/$print
Accept: application/fhir+json
```

Rodzaj wydruku wynika z tego, jak recepta została zamknięta (tak jak w aplikacji Medfile) - operacja nie ma parametru wyboru formatu:

| Recepta | Wydruk |
|---|---|
| e-recepta (wysłana do P1, ma klucz P1) | wydruk e-recepty z systemu P1 (informacja dla pacjenta); e-recepta nigdy nie jest drukowana jako recepta papierowa |
| recepta papierowa (niewysłana do P1) | recepta papierowa |

Warunki wydruku:

- e-recepta: pozycja recepty ma status `active` lub `completed` (patrz [Status](#status)),
- recepta papierowa (bez klucza P1): została już wydrukowana w aplikacji Medfile; drukuje się także w statusie `draft`,
- recepta, która nie została ani wydrukowana w Medfile, ani wysłana do P1 (szkic), nie jest drukowana - serwer zwraca kod `400 Bad Request`; kod `400` zwracany jest też dla pozostałych statusów (`cancelled`, `stopped` itd.),
- recepta papierowa z lekiem refundowanym wymaga nadanego numeru z puli numerów recept lekarza.

Odpowiedzią jest zasób DocumentReference z wydrukiem w polu `content.attachment`:

| Pole | Opis |
|---|---|
| `subject` | pozycja recepty, `MedicationRequest/{id}` |
| `content.attachment.contentType` | `application/pdf+base64` |
| `content.attachment.data` | plik PDF zakodowany w Base64 |
| `content.attachment.title` | nazwa pliku |
| `content.attachment.size` | rozmiar pliku PDF w bajtach |
| `content.attachment.hash` | skrót SHA-1 pliku PDF zakodowany w Base64 |

Przykład odpowiedzi JSON (zawartość pola `data` skrócona):
```json
{
    "resourceType": "DocumentReference",
    "status": "current",
    "subject": { "reference": "MedicationRequest/3f2b8c1e-5a47-4d9b-9e21-7c6d0a1b2c3d-1024" },
    "content": [
        {
            "attachment": {
                "contentType": "application/pdf+base64",
                "data": "JVBERi0xLjQKJ...",
                "title": "recepta.pdf",
                "size": 48213,
                "hash": "2jmj7l5rSw0yVb/vlWAYkK/YBwk="
            }
        }
    ]
}
```

# Powiadomienia o zamknięciu recepty

Subskrypcja na zdarzenie `fhir.subscription.topic.medicationrequest.closed` (UUID `36dfc8be-8eaf-4310-b00b-928494d9be68`) informuje, że recepta została zamknięta - nie będzie już zmieniana przez wystawiającego i można ją pobrać. Dla każdej recepty powiadomienie wysyłane jest **jeden raz**:

| Droga zamknięcia | Kod | Moment zamknięcia |
|---|---|---|
| Recepta papierowa | `paper` | pierwszy wydruk recepty w Medfile (pojedynczy lub zbiorczy) |
| E-recepta | `p1` | wystawienie e-recepty w systemie P1 (P1 nadał klucz recepty) |

Powiadomienie **nie** jest wysyłane przy ponownym wydruku recepty ani przy zmianach statusu realizacji e-recepty w P1 (realizacja częściowa, całkowita) czy jej anulowaniu. Zamknięcie recepty następuje w aplikacji Medfile i nie można go wywołać przez API FHIR.

Bez filtra subskrypcja otrzymuje powiadomienia o zamknięciu obiema drogami. Filtr `closure` (`filterParameter` = `closure`, `value` = `paper` lub `p1`) zawęża je do jednej drogi. Przykład - subskrypcja tylko e-recept:

```json
{
  "resourceType": "Subscription",
  "status": "active",
  "topic": "SubscriptionTopic\/36dfc8be-8eaf-4310-b00b-928494d9be68",
  "filterBy": [
    {
      "filterParameter": "closure",
      "value": "p1"
    }
  ],
  "channelType": {
    "system": "http:\/\/hl7.org\/fhir\/ValueSet\/subscription-channel-type",
    "code": "rest-hook"
  },
  "endpoint": "https:\/\/client.url\/fhir\/notify",
  "content": "id-only"
}
```

Powiadomienie obejmuje wszystkie leki zamkniętej recepty. Droga zamknięcia (`paper` lub `p1`) przekazywana jest:

- w nagłówku HTTP `X-Medfile-Prescription-Closure` - przy każdej wartości `content`,
- w rozszerzeniu `http://www.medfile.pl/fhir/ValueSet/medicationrequest-extension#closure` (`valueCode`) każdego elementu `notificationEvent` - przy `content` = `full-resource`.

Zawartość powiadomienia:

- `content` = `id-only` - treść `text/plain` z odwołaniem do każdej pozycji recepty w osobnej linii, np.
  ```
  X-Medfile-Prescription-Closure: p1
  Content-Type: text/plain

  MedicationRequest/3f2b8c1e-5a47-4d9b-9e21-7c6d0a1b2c3d-1024
  MedicationRequest/3f2b8c1e-5a47-4d9b-9e21-7c6d0a1b2c3d-1025
  ```
- `content` = `full-resource` - zasób SubscriptionStatus z osobnym `notificationEvent` dla każdej pozycji recepty (`focus` = `MedicationRequest/{id}`), np.
  ```json
  {
    "resourceType": "SubscriptionStatus",
    "status": "active",
    "type": "event-notification",
    "notificationEvent": [
      {
        "extension": [
          {
            "url": "http:\/\/www.medfile.pl\/fhir\/ValueSet\/medicationrequest-extension#closure",
            "valueCode": "paper"
          }
        ],
        "eventNumber": 1793650617,
        "timestamp": "2026-11-02T21:16:57+01:00",
        "focus": {
          "reference": "MedicationRequest\/3f2b8c1e-5a47-4d9b-9e21-7c6d0a1b2c3d-1024",
          "type": "MedicationRequest"
        }
      }
    ],
    "subscription": {
      "reference": "Subscription\/ac581a4f-0cf3-46e0-b3e7-573d6d69da0a",
      "type": "Subscription"
    },
    "topic": "SubscriptionTopic\/36dfc8be-8eaf-4310-b00b-928494d9be68"
  }
  ```
- brak elementu `content` - pusta treść; droga zamknięcia tylko w nagłówku.

Powiadomienie nie zawiera danych recepty ani pacjenta - pozycje recepty pobierz operacją [Pobieranie pozycji recepty](#pobieranie-pozycji-recepty). Więcej o subskrypcjach: [Subskrypcje](./Przypadki-użycia-subskrypcje.md#subskrypcje).

# Operacje nieobsługiwane

Recepty są tylko do odczytu. Dodawanie (`POST /fhir/MedicationRequest`), aktualizacja (PUT, PATCH), usuwanie, historia zmian (`_history`) oraz pozostałe operacje zwracają kod `501 Not Implemented`.
