# Spis treści

- [Spis treści](#spis-treści)
- [Pacjenci](#pacjenci)
  - [Tworzenie i aktualizacja pacjenta](#tworzenie-i-aktualizacja-pacjenta)
    - [Dobre praktyki](#dobre-praktyki)
  - [Usuwanie pacjenta](#usuwanie-pacjenta)
  - [Wyszukiwanie pacjentów](#wyszukiwanie-pacjentów)
      - [Obsługiwane parametry wyszukiwania](#obsługiwane-parametry-wyszukiwania)
- [Wizyty](#wizyty)
  - [Dodawanie wizyty do kalendarza](#dodawanie-wizyty-do-kalendarza)
    - [Dobre praktyki](#dobre-praktyki-1)
  - [Anulowanie wizyty](#anulowanie-wizyty)
  - [Zmiany danych wizyty](#zmiany-danych-wizyty)
  - [Zmiana usługi na wizycie](#zmiana-usługi-na-wizycie)
  - [Wyszukiwanie wizyt](#wyszukiwanie-wizyt)
      - [Obsługiwane parametry wyszukiwania](#obsługiwane-parametry-wyszukiwania-1)
  - [Pobieranie danych wizyty](#pobieranie-danych-wizyty)
  - [Pobieranie podsumowania wizyty w formacie PDF](#pobieranie-podsumowania-wizyty-w-formacie-pdf)
    - [Parametry wydruku PDF](#parametry-wydruku-pdf)
      - [Domyślne sekcje wydruku](#domyślne-sekcje-wydruku)
      - [Parametry opcjonalne](#parametry-opcjonalne)
        - [Parametr specjalny EDM](#parametr-specjalny-edm)

# Pacjenci

> ![FHIR logo](https://fhir.org/assets/images/HL7_FHIR-stacked_1_0_0.png)
> __Dokumentacja zasobów FHIR__
> - Patient: [https://www.hl7.org/fhir/patient.html](https://www.hl7.org/fhir/patient.html)

## Tworzenie i aktualizacja pacjenta

1. Sprawdź, czy zasób już istnieje, np. szukając po PESEL.
2. Jeżeli nie istnieje, utwórz zasób przez POST.
3. Jeżeli istnieje, zaktualizuj go przez PATCH lub PUT.

Przykład żądania XML:
```
POST /fhir/Patient
Content-Type: application/fhir+xml;fhirVersion=5.0
Accept: application/fhir+xml
Prefer: return=representation
```
```xml
<?xml version="1.0" encoding="UTF-8"?>
<Patient xmlns="http://hl7.org/fhir">
  <text>
    <div>This is a description</div>
  </text>
  <identifier>
    <system value="urn:oid:2.16.840.1.113883.3.4424.1.1.616"/>
    <value value="60032223600"/>
  </identifier>
  <identifier>
    <system value="urn:oid:2.16.840.1.113883.4.330.616"/>
    <value value="123456789"/>
  </identifier>
  <name>
    <family value="Senior"/>
    <given value="Jan"/>
    <given value="Sylwester"/>
  </name>
  <telecom>
    <system value="phone"/>
    <value value="+48131231230"/>
    <use value="mobile"/>
  </telecom>
  <telecom>
    <system value="phone"/>
      <value value="+48131231230"/>
    <use value="home"/>
  </telecom>
  <telecom>
    <system value="email"/>
      <value value="sylwester.senior@test.rstat.pl"/>
    <use value="home"/>
  </telecom>
  <gender value="male"/>
  <birthDate value="1940-01-01"/>
  <address>
    <use value="home"/>
    <type value="physical"/>
    <line value="Wrocławska"/>
    <line value="11A"/>
    <line value="3"/>
    <city value="Zielona Góra"/>
    <district value="lubuskie"/>
    <postalCode value="00-184"/>
    <country value="pl"/>
  </address>
  <address>
    <use value="home"/>
    <type value="postal"/>
    <line value="(street)"/>
    <line value="(house)"/>
    <line value="(flat)"/>
    <city value=""/>
    <postalCode value=""/>
    <country value=""/>
  </address>
  <address>
    <use value="billing"/>
    <type value="physical"/>
    <text value="(vat number)"/>
    <line value="(street)"/>
    <line value="(house)"/>
    <line value="(flat)"/>
    <city value=""/>
    <postalCode value=""/>
    <country value=""/>
  </address>
  <address>
    <use value="billing"/>
    <type value="both"/>
    <text value="(company name)"/>
  </address>
  <communication>
    <language>
      <coding>
        <system value="http://hl7.org/fhir/ValueSet/all-languages"/>
        <code value="pl"/>
      </coding>
    </language>
  </communication>
</Patient>
```
Przykład żądania JSON:
```
POST /fhir/Patient
Content-Type: application/fhir+json;fhirVersion=5.0
Accept: application/fhir+json
Prefer: return=representation
```
```json
{
    "resourceType": "Patient",
    "text": {
        "div": "<div>This is a description<\/div>"
    },
    "identifier": [
        {
            "system": "urn:oid:2.16.840.1.113883.3.4424.1.1.616",
            "value": "600322236"
        },
        {
            "system": "urn:oid:2.16.840.1.113883.4.330.616",
            "value": "123456789"
        }
    ],
    "name": [
        {
            "family": "Senior",
            "given": [
                "Jan",
                "Sylwester"
            ]
        }
    ],
    "telecom": [
        {
            "system": "phone",
            "value": "+48131231230",
            "use": "mobile"
        },
        {
            "system": "phone",
            "value": "+48131231230",
            "use": "home"
        },
        {
            "system": "email",
            "value": "sylwester.senior@test.rstat.pl",
            "use": "home"
        }
    ],
    "gender": "male",
    "birthDate": "2025-04-16",
    "address": [
        {
            "use": "home",
            "type": "physical",
            "line": [
                "Wrocławska",
                "11A",
                "3"
            ],
            "city": "Zielona Góra",
            "district": "lubuskie",
            "postalCode": "00-184",
            "country": "pl"
        },
        {
            "use": "home",
            "type": "postal",
            "line": [
                "PostalStreet",
                "PostalHouse",
                "PostalFlat"
            ],
            "city": "PostalCity",
            "postalCode": "PostalPostal",
            "country": "pl"
        },
        {
            "use": "billing",
            "type": "physical",
            "text": "vat_no",
            "line": [
                "BillingStreet",
                "BillingHouse",
                "BillingFlat"
            ],
            "city": "BillingCity",
            "postalCode": "BillingPostal",
            "country": "pl"
        },
        {
            "use": "billing",
            "type": "both",
            "text": "CompanyName"
        }
    ],
    "communication": [
        {
            "language": {
                "coding": [
                    {
                        "system": "http:\/\/hl7.org\/fhir\/ValueSet\/all-languages",
                        "code": "pl"
                    }
                ]
            }
        }
    ]
}
```

Przykład żądania PATCH XML:
```
PATCH /fhir/Patient/{id}
Content-Type: application/fhir+xml;fhirVersion=5.0
Accept: application/fhir+xml
Prefer: return=representation
```
```xml
<?xml version="1.0" encoding="UTF-8"?>
<Parameters xmlns="http://hl7.org/fhir">
  <parameter>
    <part>
      <name value="operation"/>
      <valueCode value="replace"/>
    </part>
    <part>
      <name value="path"/>
      <valueString value="Patient"/>
    </part>
    <part>
      <name value="name"/>
      <valueString value="given"/>
    </part>
    <part>
      <name value="value"/>
      <valueString value="Noweimię"/>
    </part>
  </parameter>
  <parameter>
    <part>
      <name value="operation"/>
      <valueCode value="replace"/>
    </part>
    <part>
      <name value="path"/>
      <valueString value="Patient"/>
    </part>
    <part>
      <name value="name"/>
      <valueString value="family"/>
    </part>
    <part>
      <name value="value"/>
      <valueString value="Nowenazwisko"/>
    </part>
  </parameter>
  <parameter>
    <part>
      <name value="operation"/>
      <valueCode value="replace"/>
    </part>
    <part>
      <name value="path"/>
      <valueString value="Patient"/>
    </part>
    <part>
      <name value="name"/>
      <valueString value="email"/>
    </part>
    <part>
      <name value="value"/>
      <valueString value="nowy.email@test.rstat.pl"/>
    </part>
  </parameter>
  <parameter>
    <part>
      <name value="operation"/>
      <valueCode value="replace"/>
    </part>
    <part>
      <name value="path"/>
      <valueString value="Patient"/>
    </part>
    <part>
      <name value="name"/>
      <valueString value="phone"/>
    </part>
    <part>
      <name value="value"/>
      <valueString value="+48131231230"/>
    </part>
  </parameter>
  <parameter>
    <part>
      <name value="operation"/>
      <valueCode value="replace"/>
    </part>
    <part>
      <name value="path"/>
      <valueString value="Patient"/>
    </part>
    <part>
      <name value="name"/>
      <valueString value="address"/>
    </part>
    <part>
      <name value="value"/>
      <valueString value="Nowyadres"/>
    </part>
  </parameter>
  <parameter>
    <part>
      <name value="operation"/>
      <valueCode value="replace"/>
    </part>
    <part>
      <name value="path"/>
      <valueString value="Patient"/>
    </part>
    <part>
      <name value="name"/>
      <valueString value="address-city"/>
    </part>
    <part>
      <name value="value"/>
      <valueString value="Nowemiasto"/>
    </part>
  </parameter>
  <parameter>
    <part>
      <name value="operation"/>
      <valueCode value="replace"/>
    </part>
    <part>
      <name value="path"/>
      <valueString value="Patient"/>
    </part>
    <part>
      <name value="name"/>
      <valueString value="address-postalcode"/>
    </part>
    <part>
      <name value="value"/>
      <valueString value="00-111"/>
    </part>
  </parameter>
</Parameters>
```

Przykład żądania PATCH JSON:
```
PATCH /fhir/Patient/{id}
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
                    "valueString": "Patient"
                },
                {
                    "name": "name",
                    "valueString": "given"
                },
                {
                    "name": "value",
                    "valueString": "testname"
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
                    "valueString": "Patient"
                },
                {
                    "name": "name",
                    "valueString": "family"
                },
                {
                    "name": "value",
                    "valueString": "testlastname"
                }
            ]
        }
    ]
}
```

Przykład żądania PUT XML:
```
PUT /fhir/Patient
Content-Type: application/fhir+xml;fhirVersion=5.0
Accept: application/fhir+xml
Prefer: return=representation
```
```xml
<?xml version="1.0" encoding="UTF-8"?>
<Patient xmlns="http://hl7.org/fhir">
  <text>
    <div>This is a description</div>
  </text>
  <identifier>
    <system value="urn:oid:2.16.840.1.113883.3.4424.1.1.616"/>
    <value value="600322236"/>
  </identifier>
  <identifier>
    <system value="urn:oid:2.16.840.1.113883.4.330.616"/>
    <value value="123456789"/>
  </identifier>
  <name>
    <family value="Senior"/>
    <given value="Jan"/>
    <given value="Sylwester"/>
  </name>
  <telecom>
    <system value="phone"/>
    <value value="+48131231230"/>
    <use value="mobile"/>
  </telecom>
  <telecom>
    <system value="phone"/>
    <value value="+48131231230"/>
    <use value="home"/>
  </telecom>
  <telecom>
    <system value="email"/>
    <value value="sylwester.senior@test.rstat.pl"/>
    <use value="home"/>
  </telecom>
  <gender value="male"/>
  <birthDate value="1940-01-01"/>
  <address>
    <use value="home"/>
    <type value="physical"/>
    <line value="Wrocławska"/>
    <line value="11A"/>
    <line value="3"/>
    <city value="Zielona Góra"/>
    <district value="lubuskie"/>
    <postalCode value="00-184"/>
    <country value="pl"/>
  </address>
  <address>
    <use value="home"/>
    <type value="postal"/>
    <line value="(street)"/>
    <line value="(house)"/>
    <line value="(flat)"/>
    <city value=""/>
    <postalCode value=""/>
    <country value=""/>
  </address>
  <address>
    <use value="billing"/>
    <type value="physical"/>
    <text value="(vat number)"/>
    <line value="(street)"/>
    <line value="(house)"/>
    <line value="(flat)"/>
    <city value=""/>
    <postalCode value=""/>
    <country value=""/>
  </address>
  <address>
    <use value="billing"/>
    <type value="both"/>
    <text value="(company name)"/>
  </address>
  <communication>
    <language>
      <coding>
        <system value="http://hl7.org/fhir/ValueSet/all-languages"/>
        <code value="pl"/>
      </coding>
    </language>
  </communication>
</Patient>
```

### Dobre praktyki

1. Warto po stronie integratora przechowywać UUID pacjenta, żeby pominąć krok 1 i zoptymalizować ilość żądań do API.

## Usuwanie pacjenta

1. Sprawdź, czy pacjent istnieje, np. szukając po PESEL.
2. Jeżeli nie istnieje, to zaniechaj usuwania.
3. Jeżeli istnieje, to usuń przez DELETE.

Przykład DELETE:

```
DELETE /fhir/Patient/{id}
Accept: application/fhir+xml
Prefer: return=representation
```

## Wyszukiwanie pacjentów

Operacja pozwala na wyszukiwanie pacjentów według parametrów.

1. Określ parametry kwerendy (query).
2. Umieść nagłówek HTTP Prefer z preferencją `representation`. Przykład: `Prefer: return=representation`.
3. Skieruj żądanie metodą POST na zasób Appointment.

```
POST /fhir/Patient/_search?{query}
Content-Type: application/fhir+json;fhirVersion=5.0
Accept: application/fhir+json
Prefer: return=representation
```
#### Obsługiwane parametry wyszukiwania
Poniższa tabela obejmuje aktualnie obsługiwane parametry wyszukiwania.

| Nazwa      | Opis                                                                                                                    | Komparatory |
|------------|-------------------------------------------------------------------------------------------------------------------------|-------------|
| _page      | numer strony wyników wyszukiwania                                                                                       | -           |
| _count     | ilość wyników wyszukiwania na stronę                                                                                    | -           |
| name       | imię pacjenta                                                                                                           | eq, ap      |
| family     | nazwisko pacjenta                                                                                                       | eq, ap      |
| identifier | identyfikator pacjenta, id lub system\|id, np: `urn:oid:2.16.840.1.113883.3.4424.1.1.616\|79011111663` dla numeru PESEL | -           |




# Wizyty

> ![FHIR logo](https://fhir.org/assets/images/HL7_FHIR-stacked_1_0_0.png)
> __Dokumentacja zasobów FHIR__
> - Appointment: [https://www.hl7.org/fhir/appointment.html](https://www.hl7.org/fhir/appointment.html)

## Dodawanie wizyty do kalendarza

1. Utwórz lub wyszukaj pacjenta.
2. Ustal UUID usługi, na którą pacjent zostanie umówiony przez Widżet API.
3. Ustal wolny termin wizyty, w którym przyjmiesz pacjenta przez Widżet API.
4. Ustal ID gabinetu, w którym wizyta ma się odbyć przez Widżet API. 
5. Ustal UUID specjalisty, który ma wizytę zrealizować przez Widżet API.
6. Dodaj wizytę do kalendarza przez POST.

Przykład żądania XML:
```
POST /fhir/Appointment
Content-Type: application/fhir+xml;fhirVersion=5.0
Accept: application/fhir+xml
Prefer: return=representation
```
```xml
<?xml version="1.0" encoding="UTF-8"?>
<Appointment xmlns="http://hl7.org/fhir">
  <serviceType>
    <reference>
      <reference value="HealthcareService/7fe05049-8176-2437-86be-01f2f3c28404"/>
    </reference>
  </serviceType>
  <serviceType>
    <reference>
      <reference value="HealthcareService/5c61b668-80b9-7411-57c2-909b70956715"/>
    </reference>
  </serviceType>
  <description value=""/>
  <start value=""/>
  <end value=""/>
  <created value=""/>
  <subject>
    <reference value="Patient/d28d4890-f63c-369d-362e-25dfb4fc8d79>"/>
    <type value="Patient"/>
  </subject>
  <participant>
    <actor>
      <reference value="Practitioner/ee2ea499-5f6f-4e4d-8a9a-5f0fb1382c3b"/>
      <type value="Practitioner"/>
    </actor>
    <required value="true"/>
  </participant>
  <participant>
    <actor>
      <reference value="Location/81"/>
      <type value="Location"/>
    </actor>
    <required value="true"/>
  </participant>
</Appointment>
```
Przykład żadania JSON:
```
POST /fhir/Appointment
Content-Type: application/fhir+json;fhirVersion=5.0
Accept: application/fhir+json
Prefer: return=representation
```
```json

{
    "resourceType": "Appointment",
    "serviceType": [
        {
            "reference": {
                "reference": "HealthcareService\/4076e899-e9e0-3f7c-b988-7a8ff4efd7bf"
            }
        },
        {
            "reference": {
                "reference": "HealthcareService\/c67b017d-969d-583a-6a89-4592c1b5ce5f"
            }
        }
    ],
    "description": "This is visit description",
    "start": "2025-04-16T10:31:57+02:00",
    "end": "2025-04-16T10:31:57+02:00",
    "created": "2025-04-16T10:31:57+02:00",
    "subject": {
        "reference": "Patient\/48f68127-3437-dbd5-9e04-6d7b9acc7ffb",
        "type": "Patient"
    },
    "participant": [
        {
            "actor": {
                "reference": "Practitioner\/ee2ea499-5f6f-4e4d-8a9a-5f0fb1382c3b",
                "type": "Practitioner"
            },
            "required": true
        },
        {
            "actor": {
                "reference": "Location\/64",
                "type": "Location"
            },
            "required": true
        }
    ]
}
```

### Dobre praktyki

1. Warto zachować UUID nowej wizyty w celu późniejszej identyfikacji np. przy anulowaniu lub przenoszeniu wizyty. 

## Anulowanie wizyty

1. Wyszukaj wizytę lub użyj zapamiętanego UUID.
2. Anuluj wizytę przez DELETE.

Przykład DELETE:

```
DELETE /fhir/Appointment/{id}
Accept: application/fhir+xml
Prefer: return=representation
```

## Zmiany danych wizyty

1. Wyszukaj wizytę lub użyj zapamiętanego UUID.
2. Określ jeden lub więcej parametrów do zmiany poprzez utworzenie elementu `parameter` dla odpowiedniego `name`.
Aktualnie, obsługiwane są parametry wymienione w przykładzie poniżej.
3. Aktualizuj jeden lub więcej parametrów wizyty wysyłajac żądanie PATCH.

Przykład żądania PATCH XML:
```
PATCH /fhir/Appointment/{id}
Content-Type: application/fhir+xml;fhirVersion=5.0
Accept: application/fhir+xml
Prefer: return=representation
```
```xml
<?xml version="1.0" encoding="UTF-8"?>
<Parameters xmlns="http://hl7.org/fhir">
  <parameter>
    <part>
      <name value="operation"/>
      <valueCode value="replace"/>
    </part>
    <part>
      <name value="path"/>
      <valueString value="Appointment"/>
    </part>
    <part>
      <name value="name"/>
      <valueString value="date"/>
    </part>
    <part>
      <name value="value"/>
      <valueDateTime value="2025-06-11T08:07:54+02:00"/>
    </part>
  </parameter>
  <parameter>
    <part>
      <name value="operation"/>
      <valueCode value="replace"/>
    </part>
    <part>
      <name value="path"/>
      <valueString value="Appointment"/>
    </part>
    <part>
      <name value="name"/>
      <valueString value="location"/>
    </part>
    <part>
      <name value="value"/>
      <valueId value="81"/>
    </part>
  </parameter>
  <parameter>
    <part>
      <name value="operation"/>
      <valueCode value="replace"/>
    </part>
    <part>
      <name value="path"/>
      <valueString value="Appointment"/>
    </part>
    <part>
      <name value="name"/>
      <valueString value="practitioner"/>
    </part>
    <part>
      <name value="value"/>
      <valueId value="148d23f4-70d2-47d7-8e81-2b77d1db3aae"/>
    </part>
  </parameter>
  <parameter>
    <part>
      <name value="operation"/>
      <valueCode value="replace"/>
    </part>
    <part>
      <name value="path"/>
      <valueString value="Appointment"/>
    </part>
    <part>
      <name value="name"/>
      <valueString value="status"/>
    </part>
    <part>
      <name value="value"/>
      <valueString value="fulfilled"/>
    </part>
  </parameter>
  <parameter>
    <part>
      <name value="operation"/>
      <valueCode value="replace"/>
    </part>
    <part>
      <name value="path"/>
      <valueString value="Appointment"/>
    </part>
    <part>
      <name value="name"/>
      <valueString value="end"/>
    </part>
    <part>
      <name value="value"/>
      <valueDateTime value="2025-06-11T08:37:54+02:00"/>
    </part>
  </parameter>
</Parameters>
```
Przykład żądania PATCH JSON:
```
PATCH /fhir/Appointment/{id}
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
          "valueString": "Appointment"
        },
        {
          "name": "name",
          "valueString": "date"
        },
        {
          "name": "value",
          "valueDateTime": "2025-06-11T08:09:22+02:00"
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
          "valueString": "Appointment"
        },
        {
          "name": "name",
          "valueString": "location"
        },
        {
          "name": "value",
          "valueId": "81"
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
          "valueString": "Appointment"
        },
        {
          "name": "name",
          "valueString": "practitioner"
        },
        {
          "name": "value",
          "valueId": "148d23f4-70d2-47d7-8e81-2b77d1db3aae"
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
          "valueString": "Appointment"
        },
        {
          "name": "name",
          "valueString": "status"
        },
        {
          "name": "value",
          "valueString": "fulfilled"
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
          "valueString": "Appointment"
        },
        {
          "name": "name",
          "valueString": "end"
        },
        {
          "name": "value",
          "valueDateTime": "2025-06-11T08:39:22+02:00"
        }
      ]
    }
  ]
}

```

## Zmiana usługi na wizycie

1. Wyszukaj wizytę lub użyj zapamiętanego UUID.
2. Aktualizuj usługę w wizycie przez PATCH.

## Wyszukiwanie wizyt

Operacja pozwala na wyszukiwanie wizyty według parametrów.

1. Określ parametry kwerendy (query).
2. Umieść nagłówek HTTP Prefer z preferencją `representation`. Przykład: `Prefer: return=representation`.
3. Skieruj żądanie metodą POST na zasób Appointment.

```
POST /fhir/Appointment/_search?{query}
Content-Type: application/fhir+json;fhirVersion=5.0
Accept: application/fhir+json
Prefer: return=representation
```
#### Obsługiwane parametry wyszukiwania
Poniższa tabela obejmuje aktualnie obsługiwane parametry wyszukiwania.

| Nazwa           | Opis                                 | Komparatory                    |
|-----------------|--------------------------------------|--------------------------------|
| _page           | numer strony wyników wyszukiwania    | -                              |
| _count          | ilość wyników wyszukiwania na stronę | -                              |
| date            | data wizyty                          | eq, ne, lt, le, gt, ge, sa, sb |
| practitioner.id | identyfikator lekarza                | -                              |
| patient.id      | identyfikator pacjenta               | -                              |
| location.id     | identyfikator gabinetu               | -                              |

## Pobieranie danych wizyty

Operacja pozwala na pobranie szczegółowych danych wizyty. m.in rozpoznań, diagnoz, opisow i leków.
Użyte oznaczenia kodowe zwracane są wraz z odniesieniami do systemów kodowych z których pochodzą.

1. Wyszukaj wizytę lub użyj zapamiętanego UUID zasobu Appointment/Encounter.
2. Umieść nagłówek HTTP Prefer z preferencją `representation`. Przykład: `Prefer: return=representation`.
3. Skieruj żądanie metodą GET na zasób Encounter.

```
GET /fhir/Encounter/{id}
Content-Type: application/fhir+json;fhirVersion=5.0
Accept: application/fhir+json
Prefer: return=representation
```

W odpowiedzi zwrócone zostaną pola zasobu Encounter zgodnie z dokumentacją ([FHIR Encounter](https://www.hl7.org/fhir/encounter.html))


## Pobieranie podsumowania wizyty w formacie PDF

Istnieje możliwość pobrania podsumowania wizyty w formie dokumentu PDF.
Dokument generowany jest każdorazowo w momencie otrzymania żądania i zawiera bieżące dane wizyty.
Jeśli nagłówek Prefer zawiera odpowiednią preferencję,
dane pliku dołączane są do zwracanego zasobu DocumentReference ([specyfikacja FHIR](https://www.hl7.org/fhir/documentreference.html))
jako strumień danych zakodowany Base64 i oznaczony typem MIME `application/pdf+base64`

1. Wyszukaj wizytę lub użyj zapamiętanego UUID zasobu Appointment/Encounter.
2. Umieść nagłówek HTTP Prefer z preferencją DocumentReference. Przykład: `Prefer: return=DocumentReference`.
3. Skieruj żądanie metodą GET na zasób Encounter.

```
GET /fhir/Encounter/{id}
Content-Type: application/fhir+json;fhirVersion=5.0
Accept: application/fhir+json
Prefer: return=DocumentReference
```

Przykład odpowiedzi XML:
```xml
<?xml version="1.0" encoding="UTF-8"?>
<DocumentReference xmlns="http://hl7.org/fhir">
  <status value="current"/>
  <subject>
    <reference value="Encounter/{id}"/>
  </subject>
  <content>
    <attachment>
      <contentType value="application/pdf+base64"/>
      <data value="JVBERi0xLjQKJeLjz9MKMyAwIG...{dane pliku}"/>
      <size value="41213"/>
      <hash value="35JmtFKuXD24LOHJrdJw6ISx9hY="/>
      <title value="{nazwa pliku}.pdf"/>
    </attachment>
  </content>
</DocumentReference>
```

### Parametry wydruku PDF

Operacja obsługuje parametr HTTP query o nazwie `print`,
który pozwala określić sekcje wydruku zawarte w zwracanym dokumencie PDF.
Nazwy poszczególnych sekcji należy podać jako wartość parametru, oddzielając je przecinkami.
Jeśli parametr `print` nie zostanie użyty lub jego wartość będzie pusta, załączone zostaną wszystkie dostępne sekcje.
Jeśli podana nazwa jest nieobsługiwana, operacja zostanie przerwana a system zwróci wyjątek. 


Przykład:
```
GET /fhir/Encounter/{id}?print=documents,organization,services
```
#### Domyślne sekcje wydruku
Poniższe sekcja będą zawsze zawarte w wydruku.

> UWAGA: Użycie ich nazw jako wartości `print` nie jest dozwolone i skutkuje błędem HTTP 400 'Invalid query parameter value '.

| wartość      | opis |
|-|-|
| patient      | dane pacjenta |
| date         | data |
| time         | czas |
| company_data | dane klienta |
| doctor       | dane lekarza |
| office       | dane gabinetu |

#### Parametry opcjonalne
Poniższe sekcje mogą być zamieszczane opcjonalnie.
Użycie ich nazw jako wartości `print` powoduje dołączenie odpowiednich sekcji  do sekcji domyślnych, na z góry ustalonej pozycji.

| wartość                     | opis                                                                                                              |
|-----------------------------|-------------------------------------------------------------------------------------------------------------------|
| documents                   | dokumenty wizyty                                                                                                  |
| organization                | dane organizacji                                                                                                  |
| services                    | usługi zrealizowane w ramach wizyty                                                                               |
| description                 | opis wizyty                                                                                                       |
| interview                   | wywiad lekarski                                                                                                   |
| symptoms                    | objawy                                                                                                            |
| physical_examination        | badanie fizykalne                                                                                                 |
| procedure                   | procedury                                                                                                         |
| diagnose                    | diagnozy                                                                                                          |
| drug                        | leki                                                                                                              |
| recommendations             | zalecenia                                                                                                         |
| comments                    | komentarze                                                                                                        |
| documents,referrals_cda     | wydruki dokumentów e-skierowań na oddzielnych stronach  <br/> (konieczne jednoczesne dołączenie sekcji documents) |
| documents,prescriptions_cda | wydruki dokumentów e-recept na oddzielnych stronach  <br/> (konieczne jednoczesne dołączenie sekcji documents)    |

##### Parametr specjalny EDM

Opcjonalny parametr specjalny `edm` zastępuje zbiór indywidujalnch parametrów opcjonalnych.
Pozwala na jednoczesne pobranie poniższych sekcji dokumentacji medycznej za pomocą jednej komendy `?print=edm`.

| wartość                     | opis              |
|-----------------------------|-------------------|
| description                 | opis wizyty       |
| interview                   | wywiad lekarski   |
| symptoms                    | objawy            |
| physical_examination        | badanie fizykalne |
| procedure                   | procedury         |
| diagnose                    | diagnozy          |
| drug                        | leki              |
| recommendations             | zalecenia         |
| comments                    | komentarze        |