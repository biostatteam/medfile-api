# Załączniki

Zasób załącznika (DocumentReference) reprezentuje dowolny obiekt binarny, na przykład dokument PDF, notatkę kliniczną lub zdjęcie,
Oprócz sekwencji bajtów, załącznik ustanawia własny kontekst metadanych (tytuł, pacjent, wizyta.)
i jest unikatowo identyfikowany przez UUID, analogicznie do pozostałych zasobów FHIR.
Treść binarna jest przekazywana jako strumień danych (string) przypisany do pola Attachment.Data.
Przekazywane dane muszą być zakodowane Base64.

> ![FHIR logo](https://fhir.org/assets/images/HL7_FHIR-stacked_1_0_0.png)
> __Dokumentacja zasobów FHIR__
>- DocumentReference [https://www.hl7.org/fhir/documentreference.html](https://www.hl7.org/fhir/documentreference.html)

# Spis treści

- [Załączniki](#załączniki)
- [Spis treści](#spis-treści)
  - [Dodawanie załącznika](#dodawanie-załącznika)
  - [Odczytywanie załącznika](#odczytywanie-załącznika)
  - [Wyszukiwanie załączników](#wyszukiwanie-załączników)
  - [Aktualizowanie załącznika](#aktualizowanie-załącznika)
  - [Zmiana poszczególnych pól załącznika](#zmiana-poszczególnych-pól-załącznika)
  - [Usuwanie załącznika](#usuwanie-załącznika)

## Dodawanie załącznika

1. Ustal UUID pacjenta (FHIR Patient)
2. Ustal UUID wizyty (FHIR Appointment)
3. Ustal nazwę, rozszerzenie i rozmiar pliku
4. Przygotuj procedurę kodowania zawartości pliku przed wysłaniem algorytmem Base 64.

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

## Aktualizowanie załącznika

1. Ustal UUID załącznika (FHIR DocumentReference)
2. Ustal UUID pacjenta (FHIR Patient)
3. Ustal UUID wizyty (FHIR Appointment)
3. Ustal nazwę, rozszerzenie i rozmiar pliku
4. Przygotuj procedurę kodowania zawartości pliku przed wysłaniem algorytmem Base 64.

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