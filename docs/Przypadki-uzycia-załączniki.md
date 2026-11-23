# Załączniki

Zasób załącznika (DocumentReference) reprezentuje dowolny obiekt binarny, na przykład dokument PDF, notatkę kliniczną lub zdjęcie,
Oprócz sekwencji bajtów, załącznik ustanawia własny kontekst metadanych (tytuł, pacjent, wizyta, kategoria.)
i jest unikatowo identyfikowany przez UUID, analogicznie do pozostałych zasobów FHIR.
Treść binarna jest przekazywana jako strumień danych (string) przypisany do pola Attachment.Data.
Przekazywane dane muszą być zakodowane Base64.

> ![FHIR logo](https://fhir.org/assets/images/HL7_FHIR-stacked_1_0_0.png)
> __Dokumentacja zasobów FHIR__
>- DocumentReference [https://www.hl7.org/fhir/documentreference.html](https://www.hl7.org/fhir/documentreference.html)

# Spis treści

- [Załączniki](#załączniki)
- [Spis treści](#spis-treści)
  - [Kategoria załącznika](#kategoria-załącznika)
  - [Dodawanie załącznika](#dodawanie-załącznika)
  - [Odczytywanie załącznika](#odczytywanie-załącznika)
  - [Wyszukiwanie załączników](#wyszukiwanie-załączników)
  - [Aktualizowanie załącznika](#aktualizowanie-załącznika)
  - [Zmiana poszczególnych pól załącznika](#zmiana-poszczególnych-pól-załącznika)
  - [Usuwanie załącznika](#usuwanie-załącznika)

## Kategoria załącznika

Załącznik może mieć jedną kategorię. Kategorie załączników to pozycje typu `patient_attachment` ze słownika konta `entity-category`
(patrz [Pobieranie słownika konta (ValueSet)](./Przypadki-użycia-słowniki.md#pobieranie-słownika-konta-valueset)).

Kategoria jest przekazywana w polu `category`:

| Pole | Opis |
|---|---|
| `coding.system` | `2.16.840.1.113883.3.4424.2.7.biostat.17.1.13` |
| `coding.code` | UUID kategorii |
| `coding.display` | nazwa kategorii, tylko przy odczycie |

Przy dodawaniu i aktualizowaniu załącznika:

- pole `category` jest opcjonalne; jeśli go nie ma, przy aktualizacji kategoria załącznika się nie zmienia,
- pusty `code` usuwa kategorię załącznika,
- kodowania z innym `system` są pomijane,
- więcej niż jedna kategoria, brak kodowania z powyższym `system` albo UUID, który nie jest kategorią załączników konta, zwracają kod `400 Bad Request`.

W odpowiedzi załącznik bez kategorii nie ma pola `category`. Załączniki danej kategorii można wyszukać parametrem `category`
(patrz [Wyszukiwanie załączników](#wyszukiwanie-załączników)).

Przykład pola `category` w odpowiedzi JSON (fragment):
```json
{
    "resourceType": "DocumentReference",
    "category": [
        {
            "coding": [
                {
                    "system": "2.16.840.1.113883.3.4424.2.7.biostat.17.1.13",
                    "code": "{uuid kategorii}",
                    "display": "Wyniki badań"
                }
            ]
        }
    ]
}
```

## Dodawanie załącznika

1. Ustal UUID pacjenta (FHIR Patient)
2. Ustal UUID wizyty (FHIR Appointment)
3. Opcjonalnie ustal UUID kategorii załącznika (patrz [Kategoria załącznika](#kategoria-załącznika))
4. Ustal nazwę, rozszerzenie i rozmiar pliku
5. Przygotuj procedurę kodowania zawartości pliku przed wysłaniem algorytmem Base 64.

Przykład żądania XML:
```
POST /fhir/DocumentReference
Content-Type: application/fhir+xml;fhirVersion=5.0
Accept: application/fhir+xml
Prefer: return=representation
```
```xml
<?xml version="1.0" encoding="UTF-8"?>
<DocumentReference xmlns="http://hl7.org/fhir">
  <category>
    <coding>
      <system value="2.16.840.1.113883.3.4424.2.7.biostat.17.1.13"/>
      <code value="{uuid kategorii}"/>
    </coding>
  </category>
  <subject>
    <reference value="Patient/{uuid}"/>
    <type value="Patient"/>
  </subject>
  <context>
    <reference value="Encounter/{uuid}"/>
    <type value="Encounter"/>
  </context>
  <description value="description"/>
  <content>
    <attachment>
      <data value="{base64 encoded file contents}"/>
      <size value="12345"/>
      <title value="testfile.ext"/>
    </attachment>
  </content>
</DocumentReference>
```

Przykład żądania JSON:
```
POST /fhir/DocumentReference
Content-Type: application/fhir+json;fhirVersion=5.0
Accept: application/fhir+json
Prefer: return=representation
```
```json
{
    "resourceType": "DocumentReference",
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
    "subject": {
      "reference": "Patient\/{uuid}",
      "type": "Patient"
    },
    "context": [
      {
        "reference": "Encounter\/{uuid}",
        "type": "Encounter"
      }
    ],
    "description": "description",
  "content": [
    {
      "attachment": {
        "data": "{base64 encoded file contents}",
        "size": 12345,
        "title": "testfile.ext"
      }
    }
  ]
}
```

## Odczytywanie załącznika

1. Ustal UUID załącznika (FHIR DocumentReference)
2. Ustaw nagłówek Prefer na `return=representation` aby otrzymać całą reprezentacje zasobu.

Przykład żądania :
```
GET /fhir/DocumentReference/{id}
Content-Type: application/fhir+xml;fhirVersion=5.0
Accept: application/fhir+xml
Prefer: return=representation
```

## Wyszukiwanie załączników

1. Użyj filtrów wyszukiwania dołączając odpowiednie parametry kwerendy URL.

Przykład żądania:
```
POST /fhir/DocumentReference/_search?{query}
Content-Type: application/fhir+xml;fhirVersion=5.0
Accept: application/fhir+xml
```

Poniższa tabela obejmuje aktualnie obsługiwane parametry wyszukiwania.

| Nazwa | Opis | Komparatory |
|---|---|---|
| _page | numer strony wyników wyszukiwania | - |
| _count | ilość wyników wyszukiwania na stronę | - |
| original_filename | oryginalna nazwa pliku | eq, ap |
| patient | UUID pacjenta (FHIR Patient) | - |
| encounter | UUID wizyty (FHIR Encounter) | - |
| context | referencja kontekstu w składni `{zasób}/{uuid}`; obsługiwane zasoby: Appointment, Encounter, Patient | - |
| category | UUID kategorii załącznika (patrz [Kategoria załącznika](#kategoria-załącznika)) | - |

Przykład: `POST /fhir/DocumentReference/_search?category={uuid kategorii}`.

## Aktualizowanie załącznika

1. Ustal UUID załącznika (FHIR DocumentReference)
2. Ustal UUID pacjenta (FHIR Patient)
3. Ustal UUID wizyty (FHIR Appointment)
4. Opcjonalnie ustal UUID kategorii załącznika (patrz [Kategoria załącznika](#kategoria-załącznika))
5. Ustal nazwę, rozszerzenie i rozmiar pliku
6. Przygotuj procedurę kodowania zawartości pliku przed wysłaniem algorytmem Base 64.

Przykład żądania XML:
```
PUT /fhir/DocumentReference/{id}
Content-Type: application/fhir+xml;fhirVersion=5.0
Accept: application/fhir+xml
Prefer: return=representation
```
```xml
<?xml version="1.0" encoding="UTF-8"?>
<DocumentReference xmlns="http://hl7.org/fhir">
  <id value="{uuid}"/>
  <category>
    <coding>
      <system value="2.16.840.1.113883.3.4424.2.7.biostat.17.1.13"/>
      <code value="{uuid kategorii}"/>
    </coding>
  </category>
  <subject>
    <reference value="Patient/{uuid}"/>
    <type value="Patient"/>
  </subject>
  <context>
    <reference value="Encounter/{uuid}"/>
    <type value="Encounter"/>
  </context>
  <description value="description"/>
  <content>
    <attachment>
      <data value="{base64 encoded file contents}"/>
      <size value="12345"/>
      <title value="testfile.ext"/>
    </attachment>
  </content>
</DocumentReference>
```
Przykład żadania JSON:
```
PUT /fhir/DocumentReference/{id}
Content-Type: application/fhir+json;fhirVersion=5.0
Accept: application/fhir+json
Prefer: return=representation
```
```json
{
  "id":"{uuid}",
  "resourceType": "DocumentReference",
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
  "subject": {
    "reference": "Patient\/{uuid}",
    "type": "Patient"
  },
  "context": [
    {
      "reference": "Encounter\/{uuid}",
      "type": "Encounter"
    }
  ],
  "description": "description",
  "content": [
    {
      "attachment": {
        "data": "{base64 encoded file contents}",
        "size": 12345,
        "title": "testfile.ext"
      }
    }
  ]
}
```

## Zmiana poszczególnych pól załącznika

1. Ustal UUID załącznika (FHIR DocumentReference)
2. Ustal nową wartość wybranego parametru

Przykład żadania XML:
```
PATCH /fhir/DocumentReference/{id}
Content-Type: application/fhir+xml;fhirVersion=5.0
Accept: application/fhir+xml
Prefer: return=representation
```
```xml
<Parameters xmlns="http://hl7.org/fhir">
  <parameter>
    <part>
      <name value="operation"/>
      <valueCode value="replace"/>
    </part>
    <part>
      <name value="path"/>
      <valueString value="DocumentReference"/>
    </part>
    <part>
      <name value="name"/>
      <valueString value="description"/>
    </part>
    <part>
      <name value="value"/>
      <valueString value="patchdescription"/>
    </part>
  </parameter>
</Parameters>
```

Przykład żądania JSON:
```
PATCH /fhir/DocumentReference/{id}
Content-Type: application/fhir+json;fhirVersion=5.0
Accept: application/fhir+json
Prefer: return=representation
```
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
                    "valueString": "DocumentReference"
                },
                {
                    "name": "name",
                    "valueString": "description"
                },
                {
                    "name": "value",
                    "valueString": "patchdescription"
                }
            ]
        }
    ]
}
```

## Usuwanie załącznika

1. Ustal UUID załącznika (FHIR DocumentReference)

```
DELETE /fhir/DocumentReference/{id}
Content-Type: application/fhir+xml;fhirVersion=5.0
Accept: application/fhir+xml
Prefer: return=representation
```