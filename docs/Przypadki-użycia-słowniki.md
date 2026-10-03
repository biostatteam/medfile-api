# Słowniki

Słowniki Medfile udostępniane są przez zasoby CodeSystem i ValueSet.

- CodeSystem - słowniki systemowe, wspólne dla wszystkich kont, dostępne tylko do odczytu.
- ValueSet - słowniki konta (kategorie i etykiety), które można odczytywać i modyfikować.

Etykiety ze słownika `entity-tag` przypisywane są do wizyt operacjami opisanymi w sekcji [Etykiety (tagi) wizyty](./Przypadki-uzycia.md#etykiety-tagi-wizyty).

> ![FHIR logo](https://fhir.org/assets/images/HL7_FHIR-stacked_1_0_0.png)
> __Dokumentacja zasobów FHIR__
>- CodeSystem - [https://www.hl7.org/fhir/codesystem.html](https://www.hl7.org/fhir/codesystem.html)
>- ValueSet - [https://www.hl7.org/fhir/valueset.html](https://www.hl7.org/fhir/valueset.html)

# Spis treści

- [Słowniki](#słowniki)
- [Spis treści](#spis-treści)
- [Dostępne słowniki](#dostępne-słowniki)
- [Pobieranie słownika systemowego (CodeSystem)](#pobieranie-słownika-systemowego-codesystem)
- [Pobieranie słownika konta (ValueSet)](#pobieranie-słownika-konta-valueset)
- [Dodawanie pozycji słownika](#dodawanie-pozycji-słownika)
  - [Dodawanie kategorii](#dodawanie-kategorii)
  - [Dodawanie etykiety](#dodawanie-etykiety)
- [Zmiana pozycji słownika](#zmiana-pozycji-słownika)
- [Usuwanie pozycji słownika](#usuwanie-pozycji-słownika)

# Dostępne słowniki

| Zasób | Identyfikator (id) | Opis | Kod pozycji |
|---|---|---|---|
| CodeSystem | `entity-category-type` | typy kategorii, np. `services`, `tags`, `patient_attachment` | nazwa typu |
| CodeSystem | `entity-category` | kategorie domyślne Medfile, wspólne dla wszystkich kont | UUID |
| ValueSet | `entity-category` | kategorie konta | UUID |
| ValueSet | `entity-tag` | aktywne etykiety konta | identyfikator liczbowy |

Adres kanoniczny (`url`) słownika ma postać `http://www.medfile.pl/fhir/{zasób}/{id}`, np. `http://www.medfile.pl/fhir/ValueSet/entity-tag`.
Ten sam adres podawany jest jako `system` pozycji słownika, np. w polu `meta.tag` wizyty.

Każda kategoria ma dokładnie jeden typ ze słownika `entity-category-type`, podany w polu `designation.use`.
Dla etykiet przeznaczone są kategorie typu `tags`, a dla usług kategorie typu `services` (patrz [Usługi](./Przypadki-użycia-usługi.md#usługi)).

# Pobieranie słownika systemowego (CodeSystem)

Operacja zwraca zasób CodeSystem z pełną listą pozycji słownika w polu `concept`.

```
GET /fhir/CodeSystem/{id}
Accept: application/fhir+json
```

Przykład: `GET /fhir/CodeSystem/entity-category-type`.

Słowniki systemowe są tylko do odczytu. Pozostałe interakcje (create, update, delete, wyszukiwanie) zwracają kod `501 Not Implemented`.
Nieznany identyfikator słownika zwraca kod `400 Bad Request`.

# Pobieranie słownika konta (ValueSet)

Operacja zwraca zasób ValueSet z pozycjami słownika konta w polu `compose.include[].concept`.

```
GET /fhir/ValueSet/{id}
Accept: application/fhir+json
```

Przykład: `GET /fhir/ValueSet/entity-tag`.

Słownik `entity-tag` zawiera wyłącznie aktywne etykiety. Etykieta dezaktywowana (`#active` = `false`) nie jest zwracana.

Zasób ValueSet nie obsługuje operacji create, update (PUT), delete ani wyszukiwania - zwracają one kod `501 Not Implemented`.
Pozycje słownika zmienia się operacjami `$add`, `$remove` oraz PATCH, opisanymi poniżej.
Nieznany identyfikator słownika zwraca kod `400 Bad Request`.

# Dodawanie pozycji słownika

Operacja `$add` dodaje do słownika jedną lub więcej pozycji.
Treścią żądania jest zasób ValueSet, którego `id` musi być zgodne z identyfikatorem w adresie URL.
Pozycje dodawane są bez pola `code` - identyfikator nadaje serwer.
W odpowiedzi zwracany jest cały słownik, w którym nowe pozycje można odnaleźć po nazwie (`display`).

```
POST /fhir/ValueSet/{id}/$add
Content-Type: application/fhir+json;fhirVersion=5.0
Accept: application/fhir+json
```

Jeżeli którakolwiek z pozycji jest niepoprawna, żądanie zwraca kod `400 Bad Request` i żadna pozycja nie jest zapisywana.

## Dodawanie kategorii

Wymagane pola kategorii:

- `display` - nazwa kategorii,
- `designation.use` - typ kategorii ze słownika `entity-category-type` (typ `product` nie jest obsługiwany).

Pole opcjonalne: rozszerzenie `http://www.medfile.pl/fhir/ValueSet/entity-category#pos` - pozycja na liście (domyślnie `0`).

```json
{
    "resourceType": "ValueSet",
    "id": "entity-category",
    "compose": {
        "inactive": false,
        "include": [
            {
                "system": "http:\/\/www.medfile.pl\/fhir\/ValueSet\/entity-category",
                "concept": [
                    {
                        "display": "Kategoria etykiet",
                        "designation": [
                            {
                                "use": {
                                    "system": "http:\/\/www.medfile.pl\/fhir\/CodeSystem\/entity-category-type",
                                    "code": "tags"
                                }
                            }
                        ],
                        "extension": [
                            {
                                "url": "http:\/\/www.medfile.pl\/fhir\/ValueSet\/entity-category#pos",
                                "valueInteger": 1
                            }
                        ]
                    }
                ]
            }
        ]
    }
}
```

Kategorię nadrzędną ustawia się po dodaniu kategorii, operacją PATCH właściwości `#parentid` (patrz [Zmiana pozycji słownika](#zmiana-pozycji-słownika)).

Przy odczycie słownika kategoria nadrzędna zwracana jest jako rozszerzenie bez pola `url`, w którym `valueCoding.system` ma wartość `http://www.medfile.pl/fhir/ValueSet/entity-category#parentid`, a `valueCoding.code` zawiera UUID kategorii nadrzędnej.

## Dodawanie etykiety

Wymagane pole etykiety: `display` - nazwa etykiety.
Nowa etykieta jest domyślnie aktywna.

Opcjonalne rozszerzenia (`url` = `http://www.medfile.pl/fhir/ValueSet/entity-tag#{właściwość}`):

| Właściwość | Typ wartości | Opis |
|---|---|---|
| `#active` | valueBoolean | etykieta aktywna |
| `#shortcut` | valueString | skrót |
| `#icon` | valueString | ikona |
| `#color` | valueString | kolor, np. `#ff0000` |
| `#categoryid` | valueCoding | kategoria typu `tags` ze słownika `entity-category` (`system` = `http://www.medfile.pl/fhir/ValueSet/entity-category`, `code` = UUID kategorii) |

```json
{
    "resourceType": "ValueSet",
    "id": "entity-tag",
    "compose": {
        "inactive": false,
        "include": [
            {
                "system": "http:\/\/www.medfile.pl\/fhir\/ValueSet\/entity-tag",
                "concept": [
                    {
                        "display": "Pilne",
                        "extension": [
                            {
                                "url": "http:\/\/www.medfile.pl\/fhir\/ValueSet\/entity-tag#color",
                                "valueString": "#ff0000"
                            },
                            {
                                "url": "http:\/\/www.medfile.pl\/fhir\/ValueSet\/entity-tag#categoryid",
                                "valueCoding": {
                                    "system": "http:\/\/www.medfile.pl\/fhir\/ValueSet\/entity-category",
                                    "code": "{uuid kategorii}"
                                }
                            }
                        ]
                    }
                ]
            }
        ]
    }
}
```

Identyfikator etykiety (liczba) zwrócony w polu `code` jest kodem używanym w polu `meta.tag` wizyty.

# Zmiana pozycji słownika

Operacja zmienia właściwości istniejących pozycji słownika metodą FHIR Path Patch.
Każdy element `parameter` zmienia jedną właściwość jednej pozycji:

- `operation` - `replace`,
- `path` - kod pozycji (UUID kategorii lub identyfikator etykiety),
- `name` - adres właściwości, np. `http://www.medfile.pl/fhir/ValueSet/entity-category#name`,
- `value` - nowa wartość (`valueString`, dla `#active` `valueBoolean`).

```
PATCH /fhir/ValueSet/{id}
Content-Type: application/fhir+json;fhirVersion=5.0
Accept: application/fhir+json
```

| Słownik | Właściwości |
|---|---|
| `entity-category` | `#name` (nazwa), `#pos` (pozycja), `#parentid` (UUID kategorii nadrzędnej) |
| `entity-tag` | `#tag` (nazwa), `#active`, `#shortcut`, `#icon`, `#color`, `#categoryid` (UUID kategorii typu `tags`) |

Typu kategorii nie można zmienić. Nie można też ustawić kategorii jako nadrzędnej dla samej siebie ani utworzyć cyklu.
Pusta nazwa, nieznana właściwość lub kod pozycji spoza konta zwracają kod `400 Bad Request`.

Etykietę, której nie chcesz już używać, możesz dezaktywować, ustawiając `#active` na `false`. Ustawienie `#active` na `true` przywraca ją do słownika.

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
                    "valueString": "{uuid kategorii}"
                },
                {
                    "name": "name",
                    "valueString": "http:\/\/www.medfile.pl\/fhir\/ValueSet\/entity-category#name"
                },
                {
                    "name": "value",
                    "valueString": "Nowa nazwa"
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
                    "valueString": "{uuid kategorii}"
                },
                {
                    "name": "name",
                    "valueString": "http:\/\/www.medfile.pl\/fhir\/ValueSet\/entity-category#parentid"
                },
                {
                    "name": "value",
                    "valueString": "{uuid kategorii nadrzędnej}"
                }
            ]
        }
    ]
}
```

# Usuwanie pozycji słownika

Operacja `$remove` usuwa ze słownika wskazane pozycje.
Treścią żądania jest zasób ValueSet z listą kodów usuwanych pozycji.

```
POST /fhir/ValueSet/{id}/$remove
Content-Type: application/fhir+json;fhirVersion=5.0
Accept: application/fhir+json
```

```json
{
    "resourceType": "ValueSet",
    "id": "entity-category",
    "compose": {
        "inactive": false,
        "include": [
            {
                "system": "http:\/\/www.medfile.pl\/fhir\/ValueSet\/entity-category",
                "concept": [
                    {
                        "code": "{uuid kategorii}"
                    }
                ]
            }
        ]
    }
}
```

Operacja jest niepodzielna - jeżeli którejkolwiek pozycji nie można usunąć (nieznany kod), żądanie zwraca kod `400 Bad Request` i nic nie jest usuwane.
Nie można usunąć kategorii, która ma kategorie podrzędne lub do której należą etykiety - należy najpierw usunąć etykiety, następnie kategorie podrzędne.
