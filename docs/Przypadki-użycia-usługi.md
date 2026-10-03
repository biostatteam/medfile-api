# Usługi

Zasób HealthcareService reprezentuje usługę z cennika konta Medfile, np. konsultację lub zabieg, wraz z ceną, czasem trwania i kategorią.
UUID usługi podaje się przy umawianiu wizyty w polu `serviceType.reference` zasobu Appointment (patrz [Dodawanie wizyty do kalendarza](./Przypadki-uzycia.md#dodawanie-wizyty-do-kalendarza)).

Wszystkie operacje obsługują formaty JSON i XML. Przykłady poniżej podano w formacie JSON, przykłady XML dostępne są w dokumentacji online Swagger UI ([https://medfile-api.uat.rstat.pl/fhirdoc](https://medfile-api.uat.rstat.pl/fhirdoc)).

> ![FHIR logo](https://fhir.org/assets/images/HL7_FHIR-stacked_1_0_0.png)
> __Dokumentacja zasobów FHIR__
>- HealthcareService - [https://www.hl7.org/fhir/healthcareservice.html](https://www.hl7.org/fhir/healthcareservice.html)

# Spis treści

- [Usługi](#usługi)
- [Spis treści](#spis-treści)
- [Pola zasobu](#pola-zasobu)
  - [Rozszerzenia](#rozszerzenia)
  - [Usługi Medfile](#usługi-medfile)
- [Dodawanie usługi](#dodawanie-usługi)
- [Pobieranie danych usługi](#pobieranie-danych-usługi)
- [Aktualizowanie usługi](#aktualizowanie-usługi)
- [Zmiana poszczególnych pól usługi (FHIR Path Patch)](#zmiana-poszczególnych-pól-usługi-fhir-path-patch)
- [Usuwanie usługi](#usuwanie-usługi)
- [Wyszukiwanie usług](#wyszukiwanie-usług)
  - [Obsługiwane parametry wyszukiwania](#obsługiwane-parametry-wyszukiwania)

# Pola zasobu

| Pole | Opis | Zapis |
|---|---|---|
| `id` | UUID usługi, nadawany przez serwer | wymagany w PUT, niedozwolony w POST |
| `name` | nazwa usługi | tak |
| `comment` | opis usługi | tak |
| `active` | `true` - usługa aktywna, ustawienie `false` usuwa usługę | tak |
| `category` | kategoria usługi: `system` = `2.16.840.1.113883.3.4424.2.7.biostat.17.1.13`, `code` = UUID kategorii typu `services` ze słownika konta `entity-category` (patrz [Słowniki](./Przypadki-użycia-słowniki.md#pobieranie-słownika-konta-valueset)). Przy odczycie `display` zawiera nazwę kategorii. | tak |
| `characteristic` | tryb realizacji usługi, `system` = `http://hl7.org/fhir/ValueSet/service-mode`: `in-person` - stacjonarnie, `videoconference` - zdalnie. Inne kody zapisywane są jako `in-person`. | tak |
| `communication` | język usługi, `system` = `http://hl7.org/fhir/ValueSet/all-languages`, np. `pl` | tak |
| `location` | gabinety, w których usługa jest realizowana, w postaci `Location/{id gabinetu}` | nie, tylko odczyt |
| `serviceProvisionCode` | odpłatność, `system` = `http://hl7.org/fhir/ValueSet/service-provision-conditions`: `free` - cena 0, `cost` - usługa płatna. `display` zawiera cenę. | nie, tylko odczyt |

Pola tylko do odczytu są ignorowane przy zapisie. Przypisanie usługi do gabinetów zmienia się w aplikacji Medfile.

## Rozszerzenia

Adres rozszerzenia (`url`) ma postać `http://www.medfile.pl/fhir/StructureDefinition/healthcareservice-{nazwa}`.

| Nazwa | Typ wartości | Opis |
|---|---|---|
| `price` | valueMoney | cena usługi |
| `taxRate` | valueDecimal | stawka VAT w procentach, `-1` - zwolniona (zw) |
| `duration` | valueDuration | czas trwania w minutach |
| `break` | valueDuration | czas przerwy w minutach |
| `code` | valueCode | kod usługi |
| `promoted` | valueBoolean | usługa Medfile, tylko do odczytu (patrz [Usługi Medfile](#usługi-medfile)) |

## Usługi Medfile

Usługi Medfile udostępniane są na koncie przez Medfile i ich edycja jest zablokowana.
Rozpoznasz je po rozszerzeniu `healthcareservice-promoted` z wartością `true`.
Usługę Medfile można odczytać i wyszukać, ale aktualizacja (PUT, PATCH) i usuwanie zwracają kod `400 Bad Request`.

# Dodawanie usługi

1. Ustal UUID kategorii usług - pozycji typu `services` ze słownika konta `entity-category` (patrz [Pobieranie słownika konta (ValueSet)](./Przypadki-użycia-słowniki.md#pobieranie-słownika-konta-valueset)).
2. Wyślij zasób HealthcareService bez pola `id` - identyfikator nadaje serwer. Żądanie z polem `id` zwraca kod `400 Bad Request`.
3. Po otrzymaniu odpowiedzi odczytaj i zapisz UUID nowej usługi z pola `id`.

```
POST /fhir/HealthcareService
Content-Type: application/fhir+json;fhirVersion=5.0
Accept: application/fhir+json
Prefer: return=representation
```
```json
{
    "resourceType": "HealthcareService",
    "extension": [
        {
            "url": "http:\/\/www.medfile.pl\/fhir\/StructureDefinition\/healthcareservice-duration",
            "valueDuration": {
                "value": 30
            }
        },
        {
            "url": "http:\/\/www.medfile.pl\/fhir\/StructureDefinition\/healthcareservice-price",
            "valueMoney": {
                "value": 150
            }
        },
        {
            "url": "http:\/\/www.medfile.pl\/fhir\/StructureDefinition\/healthcareservice-taxRate",
            "valueDecimal": -1
        },
        {
            "url": "http:\/\/www.medfile.pl\/fhir\/StructureDefinition\/healthcareservice-code",
            "valueCode": "KI01"
        },
        {
            "url": "http:\/\/www.medfile.pl\/fhir\/StructureDefinition\/healthcareservice-break",
            "valueDuration": {
                "value": 5
            }
        }
    ],
    "active": true,
    "category": [
        {
            "coding": [
                {
                    "system": "2.16.840.1.113883.3.4424.2.7.biostat.17.1.13",
                    "code": "{uuid kategorii}"
                }
            ]
        }
    ],
    "name": "Konsultacja internistyczna",
    "comment": "Wizyta u lekarza internisty",
    "characteristic": [
        {
            "coding": [
                {
                    "system": "http:\/\/hl7.org\/fhir\/ValueSet\/service-mode",
                    "code": "in-person"
                }
            ]
        }
    ],
    "communication": [
        {
            "coding": [
                {
                    "system": "http:\/\/hl7.org\/fhir\/ValueSet\/all-languages",
                    "code": "pl"
                }
            ]
        }
    ]
}
```

Kategoria spoza konta lub kategoria innego typu niż `services` zwraca kod `400 Bad Request`.

# Pobieranie danych usługi

Operacja zwraca zasób HealthcareService z polami opisanymi w sekcji [Pola zasobu](#pola-zasobu), w tym z polami tylko do odczytu.

1. Użyj UUID usługi dla identyfikacji docelowego zasobu. UUID możesz ustalić operacją [Wyszukiwanie usług](#wyszukiwanie-usług).

```
GET /fhir/HealthcareService/{id}
Accept: application/fhir+json
Prefer: return=representation
```

Fragment odpowiedzi z polami tylko do odczytu:

```json
{
    "location": [
        {
            "reference": "Location\/{id gabinetu}"
        }
    ],
    "serviceProvisionCode": [
        {
            "coding": [
                {
                    "system": "http:\/\/hl7.org\/fhir\/ValueSet\/service-provision-conditions",
                    "code": "cost",
                    "display": "{cena}"
                }
            ]
        }
    ]
}
```

UUID spoza zautoryzowanego konta zwraca kod `400 Bad Request`.
Historia wersji usługi (`_history`) nie jest dostępna - zwraca kod `501 Not Implemented`.

# Aktualizowanie usługi

1. Wyszukaj usługę lub użyj zapamiętanego UUID.
2. Wyślij zasób HealthcareService z polem `id` równym UUID z adresu URL. Brak pola `id` lub jego niezgodność z adresem zwraca kod `400 Bad Request`.

Treścią żądania jest pełny zasób HealthcareService. Do zmiany wybranych pól użyj operacji [Zmiana poszczególnych pól usługi (FHIR Path Patch)](#zmiana-poszczególnych-pól-usługi-fhir-path-patch).
Ustawienie `active` = `false` usuwa usługę, tak jak operacja [Usuwanie usługi](#usuwanie-usługi).

```
PUT /fhir/HealthcareService/{id}
Content-Type: application/fhir+json;fhirVersion=5.0
Accept: application/fhir+json
Prefer: return=representation
```
```json
{
    "resourceType": "HealthcareService",
    "id": "{id}",
    "extension": [
        {
            "url": "http:\/\/www.medfile.pl\/fhir\/StructureDefinition\/healthcareservice-duration",
            "valueDuration": {
                "value": 45
            }
        },
        {
            "url": "http:\/\/www.medfile.pl\/fhir\/StructureDefinition\/healthcareservice-price",
            "valueMoney": {
                "value": 180
            }
        },
        {
            "url": "http:\/\/www.medfile.pl\/fhir\/StructureDefinition\/healthcareservice-taxRate",
            "valueDecimal": -1
        },
        {
            "url": "http:\/\/www.medfile.pl\/fhir\/StructureDefinition\/healthcareservice-code",
            "valueCode": "KI01"
        },
        {
            "url": "http:\/\/www.medfile.pl\/fhir\/StructureDefinition\/healthcareservice-break",
            "valueDuration": {
                "value": 5
            }
        }
    ],
    "active": true,
    "category": [
        {
            "coding": [
                {
                    "system": "2.16.840.1.113883.3.4424.2.7.biostat.17.1.13",
                    "code": "{uuid kategorii}"
                }
            ]
        }
    ],
    "name": "Konsultacja internistyczna",
    "comment": "Wizyta u lekarza internisty, z oceną wyników badań",
    "characteristic": [
        {
            "coding": [
                {
                    "system": "http:\/\/hl7.org\/fhir\/ValueSet\/service-mode",
                    "code": "videoconference"
                }
            ]
        }
    ],
    "communication": [
        {
            "coding": [
                {
                    "system": "http:\/\/hl7.org\/fhir\/ValueSet\/all-languages",
                    "code": "pl"
                }
            ]
        }
    ]
}
```

# Zmiana poszczególnych pól usługi (FHIR Path Patch)

Operacja zmienia wybrane pola usługi. Każdy element `parameter` zmienia jedno pole:

- `operation` - `replace`,
- `path` - `HealthcareService`,
- `name` - nazwa pola z poniższej tabeli,
- `value` - nowa wartość w typie podanym w tabeli.

```
PATCH /fhir/HealthcareService/{id}
Content-Type: application/fhir+json;fhirVersion=5.0
Accept: application/fhir+json
Prefer: return=representation
```

| Nazwa (`name`) | Typ wartości | Opis |
|---|---|---|
| `name` | valueString | nazwa usługi |
| `description` | valueString | opis usługi (pole `comment`) |
| `code` | valueString | kod usługi |
| `price` | valueMoney | cena usługi |
| `tax_rate` | valueDecimal | stawka VAT w procentach, `-1` - zwolniona (zw) |
| `duration` | valueDuration | czas trwania w minutach |
| `break` | valueDuration | czas przerwy w minutach |
| `category` | valueId | UUID kategorii typu `services` |
| `remote` | valueBoolean | `true` - usługa zdalna (`videoconference`), `false` - stacjonarna (`in-person`) |

Operacja inna niż `replace`, ścieżka inna niż `HealthcareService` lub nieobsługiwana nazwa pola zwracają kod `400 Bad Request`.
Pola `active` nie można zmienić operacją PATCH - użyj PUT lub DELETE.

```json
{
    "resourceType": "Parameters",
    "parameter": [
        {
            "part": [
                {
                    "name": "operation",
                    "valueCode": "replace"
                },
                {
                    "name": "path",
                    "valueString": "HealthcareService"
                },
                {
                    "name": "name",
                    "valueString": "price"
                },
                {
                    "name": "value",
                    "valueMoney": {
                        "value": 180
                    }
                }
            ]
        },
        {
            "part": [
                {
                    "name": "operation",
                    "valueCode": "replace"
                },
                {
                    "name": "path",
                    "valueString": "HealthcareService"
                },
                {
                    "name": "name",
                    "valueString": "duration"
                },
                {
                    "name": "value",
                    "valueDuration": {
                        "value": 45
                    }
                }
            ]
        }
    ]
}
```

# Usuwanie usługi

1. Użyj UUID usługi dla identyfikacji docelowego zasobu.

```
DELETE /fhir/HealthcareService/{id}
Accept: application/fhir+json
Prefer: return=representation
```

Usuwanie wszystkich usług naraz (`DELETE /fhir/HealthcareService`) nie jest obsługiwane i zwraca kod `501 Not Implemented`.

# Wyszukiwanie usług

Operacja pobiera listę usług zautoryzowanego konta. Usługi usunięte nie są zwracane.

```
POST /fhir/HealthcareService/_search?_page=1&_count=5&name=apkonsultacja
Content-Type: application/fhir+json;fhirVersion=5.0
Accept: application/fhir+json
```

#### Obsługiwane parametry wyszukiwania

Poniższa tabela obejmuje aktualnie obsługiwane parametry wyszukiwania.
Jeśli parametr jest użyty bez prefixu komparatora, domyślnie stosuje się komparator eq (równość).

| Nazwa  | Opis                                                                | Komparatory |
|--------|---------------------------------------------------------------------|-------------|
| _page  | numer strony wyników wyszukiwania (domyślnie 1, maksymalnie 100)    | -           |
| _count | ilość wyników wyszukiwania na stronę (domyślnie 5, maksymalnie 200) | -           |
| name   | nazwa usługi                                                        | eq, ap      |

Przykład kwerendy z dokładnym dopasowaniem nazwy (brak komparatora lub komparator eq):
```
/fhir/HealthcareService/_search?name=Konsultacja%20internistyczna
/fhir/HealthcareService/_search?name=eqKonsultacja%20internistyczna
```

Przykład kwerendy z dopasowaniem fragmentu nazwy (komparator ap):
```
/fhir/HealthcareService/_search?name=apkonsultacja
```
