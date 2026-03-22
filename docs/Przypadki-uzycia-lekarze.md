# Lekarze

> ![FHIR logo](https://fhir.org/assets/images/HL7_FHIR-stacked_1_0_0.png)
> __Dokumentacja zasobów FHIR__
> - Practitioner: [https://www.hl7.org/fhir/practitioner.html](https://www.hl7.org/fhir/practitioner.html)

# Spis treści

- [Lekarze](#lekarze)
- [Spis treści](#spis-treści)
  - [Dodawanie lekarza](#dodawanie-lekarza)
  - [Aktualizowanie danych lekarza](#aktualizowanie-danych-lekarza)
  - [Zmiana poszczególnych pól danych lekarza (FHIR Path Patch)](#zmiana-poszczególnych-pól-danych-lekarza-fhir-path-patch)
  - [Usuwanie lekarza](#usuwanie-lekarza)
  - [Wyszukiwanie lekarzy](#wyszukiwanie-lekarzy)
    - [Parametry wyszukiwania](#parametry-wyszukiwania)

## Dodawanie lekarza

Operacja tworzy nowego użytkownika w ramach zautoryzowanego konta głównego klienta.
Każdy nowy użytkownik uzyskuje domyślny zestaw uprawnień pozwalający na wykonywanie podstawowych działań w aplikacji Medfile.
Uprawnienie te mogą być następnie modyfikowane z poziomu GUI aplikacji.

1. Ustal UUID specjalizacji medycznej wg specyfikacji P! dla OID `2.16.840.1.113883.3.4424.11.1.80`
2. Ustal UUID roli użytkownika, które zostało uprzenio zdefiniowane w ramach konta klienta Medfile. UUID powinno mieć składnię GUID v4.
3. Ustal identyfikator education, wg stałych Medfile; system OID `2.16.840.1.113883.3.4424.2.7.biostat.17.1.11`
4. W celu zadeklarowania poszczególnych identyfikatorów, użyj identyfikatorów OID według wskazanych w poniższym przykładzie
5. Po otrzymaniu odpowiedzi, odczytaj i zapisz identyfikator GUID nowo utworzonego rekordu.

```
POST /fhir/Practitioner
Content-Type: application/fhir+xml;fhirVersion=5.0
Accept: application/fhir+xml
Prefer: return=representation
```
```xml
<?xml version="1.0" encoding="UTF-8"?>
<Practitioner xmlns="http://hl7.org/fhir">
  <identifier>
    <system value="urn:oid:2.16.840.1.113883.3.4424.2.7.biostat.17.1.4"/>
    <value value="password"/>
  </identifier>
  <identifier>
    <system value="urn:oid:2.16.840.1.113883.3.4424.1.6"/>
    <value value="pwz"/>
  </identifier>
    <identifier>
    <system value="urn:oid:2.16.840.1.113883.3.4424.11.1.80"/>
    <value value="medical_profession"/>
  </identifier>
  <identifier>
    <system value="urn:oid:2.16.840.1.113883.3.4424.2.1"/>
    <value value="nip"/>
  </identifier>
  <identifier>
    <system value="urn:oid:2.16.840.1.113883.3.4424.2.2"/>
    <value value="regon"/>
  </identifier>
  <identifier>
    <system value="urn:oid:2.16.840.1.113883.3.4424.2.7.biostat.17.1.8"/>
    <value value="iban"/>
  </identifier>
  <identifier>
    <system value="urn:oid:2.16.840.1.113883.3.4424.2.7.biostat.17.1.9"/>
    <value value="bankName"/>
  </identifier>
  <identifier>
    <system value="urn:oid:2.16.840.1.113883.3.4424.1.1.616"/>
    <value value="pesel"/>
  </identifier>
  <active value="true"/>
  <name>
    <family value="Family"/>
    <given value="Given"/>
    <given value="Middle"/>
    <prefix value="Prefix"/>
  </name>
  <telecom>
    <system value="phone"/>
    <value value="+48131231230"/>
    <use value="mobile"/>
  </telecom>
  <telecom>
    <system value="phone"/>
    <value value="+48131231230"/>
    <use value="work"/>
  </telecom>
  <telecom>
    <system value="email"/>
    <value value="sylwester.senior@test.rstat.pl"/>
    <use value="home"/>
  </telecom>
  <telecom>
    <system value="email"/>
    <value value="test email signature"/>
    <use value="temp"/>
  </telecom>
  <telecom>
    <system value="email"/>
    <value value="sylwester.senior@test.rstat.pl"/>
    <use value="work"/>
  </telecom>
  <address>
    <use value="work"/>
    <type value="physical"/>
    <line value="street"/>
    <line value="house"/>
    <line value="flat"/>
    <city value="city"/>
    <postalCode value="postal_cod"/>
    <country value="pl"/>
  </address>
  <qualification>
    <code>
      <coding>
        <system value="urn:oid:2.16.840.1.113883.3.4424.2.7.biostat.17.1.11"/>
        <code value="3"/>
      </coding>
    </code>
  </qualification>
  <qualification>
    <code>
      <coding>
        <system value="urn:oid:2.16.840.1.113883.3.4424.2.7.biostat.17.1.12"/>
        <code value="0828c110ceb04ed78d529a347d64181a"/>
      </coding>
    </code>
  </qualification>
  <communication>
    <language>
      <coding>
        <system value="http://hl7.org/fhir/ValueSet/all-languages"/>
        <code value="pl"/>
      </coding>
    </language>
  </communication>
</Practitioner>

```
```
POST /fhir/Practitioner
Content-Type: application/fhir+json;fhirVersion=5.0
Accept: application/fhir+json
Prefer: return=representation
```
```json
{
    "resourceType": "Practitioner",
    "identifier": [
        {
            "system": "urn:oid:2.16.840.1.113883.3.4424.2.7.biostat.17.1.4",
            "value": "password"
        },
        {
            "system": "urn:oid:2.16.840.1.113883.3.4424.1.6",
            "value": "npwz"
        },
        {
            "system": "urn:oid:2.16.840.1.113883.3.4424.11.1.80",
            "value": "11"
        },
        {
            "system": "urn:oid:2.16.840.1.113883.3.4424.2.1",
            "value": "nip"
        },
        {
            "system": "urn:oid:2.16.840.1.113883.3.4424.2.2",
            "value": "regon"
        },
        {
            "system": "urn:oid:2.16.840.1.113883.3.4424.2.7.biostat.17.1.8",
            "value": "iban"
        },
        {
            "system": "urn:oid:2.16.840.1.113883.3.4424.2.7.biostat.17.1.9",
            "value": "bank_name"
        },
        {
            "system": "urn:oid:2.16.840.1.113883.3.4424.1.1.616",
            "value": "pesel"
        }
    ],
    "active": true,
    "name": [
        {
            "family": "Family",
            "given": [
                "Given",
                "Middle"
            ],
           "prefix": [
              "Prefix"
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
            "use": "work"
        },
        {
            "system": "email",
            "value": "sylwester.senior@test.rstat.pl",
            "use": "home"
        },
        {
            "system": "email",
            "value": "test email signature",
            "use": "temp"
        },
        {
            "system": "email",
            "value": "sylwester.senior@test.rstat.pl",
            "use": "work"
        }
    ],
    "address": [
        {
            "use": "work",
            "type": "physical",
            "line": [
                "street",
                "house",
                "flat"
            ],
            "city": "city",
            "postalCode": "postal",
            "country": "pl"
        }
    ],
    "qualification": [
        {
            "code": {
                "coding": [
                    {
                        "system": "2.16.840.1.113883.3.4424.2.7.biostat.17.1.11",
                        "code": "3"
                    }
                ]
            }
        },
        {
            "code": {
                "coding": [
                    {
                        "system": "2.16.840.1.113883.3.4424.2.7.biostat.17.1.12",
                        "code": "0828c110ceb04ed78d529a347d64181a"
                    }
                ]
            }
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

## Aktualizowanie danych lekarza

1. Ustal UUID specjalizacji medycznej wg specyfikacji P! dla OID `2.16.840.1.113883.3.4424.11.1.80`
2. Ustal UUID roli użytkownika, które zostało uprzenio zdefiniowane w ramach konta klienta Medfile. UUID powinno mieć składnię GUID v4.
3. Ustal identyfikator education, wg stałych Medfile; system OID `2.16.840.1.113883.3.4424.2.7.biostat.17.1.11`
4. W celu zadeklarowania poszczególnych identyfikatorów, użyj identyfikatorów OID według wskazanych w poniższym przykładzie

```
PUT /fhir/Practitioner/{id}
Content-Type: application/fhir+xml;fhirVersion=5.0
Accept: application/fhir+xml
Prefer: return=representation
```
```xml
<?xml version="1.0" encoding="UTF-8"?>
<Practitioner xmlns="http://hl7.org/fhir">
  <identifier>
    <system value="urn:oid:2.16.840.1.113883.3.4424.2.7.biostat.17.1.4"/>
    <value value="password"/>
  </identifier>
  <identifier>
    <system value="urn:oid:2.16.840.1.113883.3.4424.1.6"/>
    <value value="pwz"/>
  </identifier>
    <identifier>
    <system value="urn:oid:2.16.840.1.113883.3.4424.11.1.80"/>
    <value value="medical_profession"/>
  </identifier>
  <identifier>
    <system value="urn:oid:2.16.840.1.113883.3.4424.2.1"/>
    <value value="nip"/>
  </identifier>
  <identifier>
    <system value="urn:oid:2.16.840.1.113883.3.4424.2.2"/>
    <value value="regon"/>
  </identifier>
  <identifier>
    <system value="urn:oid:2.16.840.1.113883.3.4424.2.7.biostat.17.1.8"/>
    <value value="iban"/>
  </identifier>
  <identifier>
    <system value="urn:oid:2.16.840.1.113883.3.4424.2.7.biostat.17.1.9"/>
    <value value="bankName"/>
  </identifier>
  <identifier>
    <system value="urn:oid:2.16.840.1.113883.3.4424.1.1.616"/>
    <value value="pesel"/>
  </identifier>
  <active value="true"/>
  <name>
    <family value="Family"/>
    <given value="Given"/>
    <given value="Middle"/>
    <prefix value="Prefix"/>
  </name>
  <telecom>
    <system value="phone"/>
    <value value="+48131231230"/>
    <use value="mobile"/>
  </telecom>
  <telecom>
    <system value="phone"/>
    <value value="+48131231230"/>
    <use value="work"/>
  </telecom>
  <telecom>
    <system value="email"/>
    <value value="sylwester.senior@test.rstat.pl"/>
    <use value="home"/>
  </telecom>
  <telecom>
    <system value="email"/>
    <value value="test email signature"/>
    <use value="temp"/>
  </telecom>
  <telecom>
    <system value="email"/>
    <value value="sylwester.senior@test.rstat.pl"/>
    <use value="work"/>
  </telecom>
  <address>
    <use value="work"/>
    <type value="physical"/>
    <line value="street"/>
    <line value="house"/>
    <line value="flat"/>
    <city value="city"/>
    <postalCode value="postal_cod"/>
    <country value="pl"/>
  </address>
  <qualification>
    <code>
      <coding>
        <system value="2.16.840.1.113883.3.4424.2.7.biostat.17.1.11"/>
        <code value="3"/>
      </coding>
    </code>
  </qualification>
  <qualification>
    <code>
      <coding>
        <system value="2.16.840.1.113883.3.4424.2.7.biostat.17.1.12"/>
        <code value="0828c110ceb04ed78d529a347d64181a"/>
      </coding>
    </code>
  </qualification>
  <communication>
    <language>
      <coding>
        <system value="http://hl7.org/fhir/ValueSet/all-languages"/>
        <code value="pl"/>
      </coding>
    </language>
  </communication>
</Practitioner>

```
```
POST /fhir/Practitioner
Content-Type: application/fhir+json;fhirVersion=5.0
Accept: application/fhir+json
Prefer: return=representation
```
```json
{
    "resourceType": "Practitioner",
    "identifier": [
        {
            "system": "urn:oid:2.16.840.1.113883.3.4424.2.7.biostat.17.1.4",
            "value": "password"
        },
        {
            "system": "urn:oid:2.16.840.1.113883.3.4424.1.6",
            "value": "npwz"
        },
        {
            "system": "urn:oid:2.16.840.1.113883.3.4424.11.1.80",
            "value": "11"
        },
        {
            "system": "urn:oid:2.16.840.1.113883.3.4424.2.1",
            "value": "nip"
        },
        {
            "system": "urn:oid:2.16.840.1.113883.3.4424.2.2",
            "value": "regon"
        },
        {
            "system": "urn:oid:2.16.840.1.113883.3.4424.2.7.biostat.17.1.8",
            "value": "iban"
        },
        {
            "system": "urn:oid:2.16.840.1.113883.3.4424.2.7.biostat.17.1.9",
            "value": "bank_name"
        },
        {
            "system": "urn:oid:2.16.840.1.113883.3.4424.1.1.616",
            "value": "pesel"
        }
    ],
    "active": true,
    "name": [
        {
            "family": "Family",
            "given": [
                "Given",
                "Middle"
            ],
           "prefix": [
              "Prefix"
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
            "use": "work"
        },
        {
            "system": "email",
            "value": "sylwester.senior@test.rstat.pl",
            "use": "home"
        },
        {
            "system": "email",
            "value": "test email signature",
            "use": "temp"
        },
        {
            "system": "email",
            "value": "sylwester.senior@test.rstat.pl",
            "use": "work"
        }
    ],
    "address": [
        {
            "use": "work",
            "type": "physical",
            "line": [
                "street",
                "house",
                "flat"
            ],
            "city": "city",
            "postalCode": "postal",
            "country": "pl"
        }
    ],
    "qualification": [
        {
            "code": {
                "coding": [
                    {
                        "system": "2.16.840.1.113883.3.4424.2.7.biostat.17.1.11",
                        "code": "3"
                    }
                ]
            }
        },
        {
            "code": {
                "coding": [
                    {
                        "system": "2.16.840.1.113883.3.4424.2.7.biostat.17.1.12",
                        "code": "0828c110ceb04ed78d529a347d64181a"
                    }
                ]
            }
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

## Zmiana poszczególnych pól danych lekarza (FHIR Path Patch)

1. Użyj UUID lekarza dla identyfikacji docelowego zasobu.
2. Użyj jednej lub wiecej struktur `Parameter` w celu deklaracji zmiany wybranego pola.
   Pełen schemat dostępnych do aktulizacji pól przedstawiono w poniższym przykładzie.
3. Zwróć uwagę na składnię niektórych wartości, która obejmuje kolejne segmenty oddzielone znakiem `|` (pipe).

```
PATCH /fhir/Practitioner/{id}
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
      <valueString value="Practitioner"/>
    </part>
    <part>
      <name value="name"/>
      <valueString value="given"/>
    </part>
    <part>
      <name value="value"/>
      <valueString value="patch_given"/>
    </part>
  </parameter>
  <parameter>
    <part>
      <name value="operation"/>
      <valueCode value="replace"/>
    </part>
    <part>
      <name value="path"/>
      <valueString value="Practitioner"/>
    </part>
    <part>
      <name value="name"/>
      <valueString value="family"/>
    </part>
    <part>
      <name value="value"/>
      <valueString value="patch_family"/>
    </part>
  </parameter>
  <parameter>
    <part>
      <name value="operation"/>
      <valueCode value="replace"/>
    </part>
    <part>
      <name value="path"/>
      <valueString value="Practitioner"/>
    </part>
    <part>
      <name value="name"/>
      <valueString value="identifier"/>
    </part>
    <part>
      <name value="value"/>
      <valueString value="2.16.840.1.113883.3.4424.1.6|patch_npwz"/>
    </part>
  </parameter>
  <parameter>
    <part>
      <name value="operation"/>
      <valueCode value="replace"/>
    </part>
    <part>
      <name value="path"/>
      <valueString value="Practitioner"/>
    </part>
    <part>
      <name value="name"/>
      <valueString value="identifier"/>
    </part>
    <part>
      <name value="value"/>
      <valueString value="2.16.840.1.113883.3.4424.11.1.80|123"/>
    </part>
  </parameter>
  <parameter>
    <part>
      <name value="operation"/>
      <valueCode value="replace"/>
    </part>
    <part>
      <name value="path"/>
      <valueString value="Practitioner"/>
    </part>
    <part>
      <name value="name"/>
      <valueString value="identifier"/>
    </part>
    <part>
      <name value="value"/>
      <valueString value="2.16.840.1.113883.3.4424.2.1|patch_nip"/>
    </part>
  </parameter>
  <parameter>
    <part>
      <name value="operation"/>
      <valueCode value="replace"/>
    </part>
    <part>
      <name value="path"/>
      <valueString value="Practitioner"/>
    </part>
    <part>
      <name value="name"/>
      <valueString value="identifier"/>
    </part>
    <part>
      <name value="value"/>
      <valueString value="2.16.840.1.113883.3.4424.2.2|patch_regon"/>
    </part>
  </parameter>
  <parameter>
    <part>
      <name value="operation"/>
      <valueCode value="replace"/>
    </part>
    <part>
      <name value="path"/>
      <valueString value="Practitioner"/>
    </part>
    <part>
      <name value="name"/>
      <valueString value="telecom"/>
    </part>
    <part>
      <name value="value"/>
      <valueString value="phone|mobile|patch_companyphone"/>
    </part>
  </parameter>
  <parameter>
    <part>
      <name value="operation"/>
      <valueCode value="replace"/>
    </part>
    <part>
      <name value="path"/>
      <valueString value="Practitioner"/>
    </part>
    <part>
      <name value="name"/>
      <valueString value="telecom"/>
    </part>
    <part>
      <name value="value"/>
      <valueString value="email|work|patch_companyemail"/>
    </part>
  </parameter>
  <parameter>
    <part>
      <name value="operation"/>
      <valueCode value="replace"/>
    </part>
    <part>
      <name value="path"/>
      <valueString value="Practitioner"/>
    </part>
    <part>
      <name value="name"/>
      <valueString value="telecom"/>
    </part>
    <part>
      <name value="value"/>
      <valueString value="email|temp|patch_emailsignature"/>
    </part>
  </parameter>
  <parameter>
    <part>
      <name value="operation"/>
      <valueCode value="replace"/>
    </part>
    <part>
      <name value="path"/>
      <valueString value="Practitioner"/>
    </part>
    <part>
      <name value="name"/>
      <valueString value="address"/>
    </part>
    <part>
      <name value="value"/>
      <valueString value="patch_street|patch_house|patch_flat"/>
    </part>
  </parameter>
  <parameter>
    <part>
      <name value="operation"/>
      <valueCode value="replace"/>
    </part>
    <part>
      <name value="path"/>
      <valueString value="Practitioner"/>
    </part>
    <part>
      <name value="name"/>
      <valueString value="address-city"/>
    </part>
    <part>
      <name value="value"/>
      <valueString value="patch_city"/>
    </part>
  </parameter>
  <parameter>
    <part>
      <name value="operation"/>
      <valueCode value="replace"/>
    </part>
    <part>
      <name value="path"/>
      <valueString value="Practitioner"/>
    </part>
    <part>
      <name value="name"/>
      <valueString value="address-postalcode"/>
    </part>
    <part>
      <name value="value"/>
      <valueString value="patch_postal"/>
    </part>
  </parameter>
  <parameter>
    <part>
      <name value="operation"/>
      <valueCode value="replace"/>
    </part>
    <part>
      <name value="path"/>
      <valueString value="Practitioner"/>
    </part>
    <part>
      <name value="name"/>
      <valueString value="address-country"/>
    </part>
    <part>
      <name value="value"/>
      <valueString value="pl"/>
    </part>
  </parameter>
  <parameter>
    <part>
      <name value="operation"/>
      <valueCode value="replace"/>
    </part>
    <part>
      <name value="path"/>
      <valueString value="Practitioner"/>
    </part>
    <part>
      <name value="name"/>
      <valueString value="qualification"/>
    </part>
    <part>
      <name value="value"/>
      <valueString value="2.16.840.1.113883.3.4424.2.7.biostat.17.1.12|0828c110ceb04ed78d529a347d64181a"/>
    </part>
  </parameter>
   <parameter>
      <part>
         <name value="operation"/>
         <valueCode value="replace"/>
      </part>
      <part>
         <name value="path"/>
         <valueString value="Practitioner"/>
      </part>
      <part>
         <name value="name"/>
         <valueString value="prefix"/>
      </part>
      <part>
         <name value="value"/>
         <valueString value="Prefix"/>
      </part>
   </parameter>
   <parameter>
      <part>
         <name value="operation"/>
         <valueCode value="replace"/>
      </part>
      <part>
         <name value="path"/>
         <valueString value="Practitioner"/>
      </part>
      <part>
         <name value="name"/>
         <valueString value="active"/>
      </part>
      <part>
         <name value="value"/>
         <valueBoolean value="true"/>
      </part>
   </parameter>
</Parameters>

```
```
PATCH /fhir/Practitioner/{id}
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
                    "valueString": "Practitioner"
                },
                {
                    "name": "name",
                    "valueString": "given"
                },
                {
                    "name": "value",
                    "valueString": "patchgiven"
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
                    "valueString": "Practitioner"
                },
                {
                    "name": "name",
                    "valueString": "family"
                },
                {
                    "name": "value",
                    "valueString": "patchfamily"
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
                    "valueString": "Practitioner"
                },
                {
                    "name": "name",
                    "valueString": "identifier"
                },
                {
                    "name": "value",
                    "valueString": "2.16.840.1.113883.3.4424.1.6|pathnpwz"
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
                    "valueString": "Practitioner"
                },
                {
                    "name": "name",
                    "valueString": "identifier"
                },
                {
                    "name": "value",
                    "valueString": "2.16.840.1.113883.3.4424.11.1.80|123"
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
                    "valueString": "Practitioner"
                },
                {
                    "name": "name",
                    "valueString": "identifier"
                },
                {
                    "name": "value",
                    "valueString": "2.16.840.1.113883.3.4424.2.1|patchnip"
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
                    "valueString": "Practitioner"
                },
                {
                    "name": "name",
                    "valueString": "identifier"
                },
                {
                    "name": "value",
                    "valueString": "2.16.840.1.113883.3.4424.2.2|patchregon"
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
                    "valueString": "Practitioner"
                },
                {
                    "name": "name",
                    "valueString": "telecom"
                },
                {
                    "name": "value",
                    "valueString": "phone|mobile|patchcompanyphone"
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
                    "valueString": "Practitioner"
                },
                {
                    "name": "name",
                    "valueString": "telecom"
                },
                {
                    "name": "value",
                    "valueString": "email|work|patchcompanyemail"
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
                    "valueString": "Practitioner"
                },
                {
                    "name": "name",
                    "valueString": "telecom"
                },
                {
                    "name": "value",
                    "valueString": "email|temp|patchemailsignature"
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
                    "valueString": "Practitioner"
                },
                {
                    "name": "name",
                    "valueString": "address"
                },
                {
                    "name": "value",
                    "valueString": "patchstreet|patchhouse|patchflat"
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
                    "valueString": "Practitioner"
                },
                {
                    "name": "name",
                    "valueString": "address-city"
                },
                {
                    "name": "value",
                    "valueString": "patchcity"
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
                    "valueString": "Practitioner"
                },
                {
                    "name": "name",
                    "valueString": "address-postalcode"
                },
                {
                    "name": "value",
                    "valueString": "patchpostal"
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
                    "valueString": "Practitioner"
                },
                {
                    "name": "name",
                    "valueString": "address-country"
                },
                {
                    "name": "value",
                    "valueString": "pl"
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
                    "valueString": "Practitioner"
                },
                {
                    "name": "name",
                    "valueString": "qualification"
                },
                {
                    "name": "value",
                    "valueString": "2.16.840.1.113883.3.4424.2.7.biostat.17.1.12|0828c110ceb04ed78d529a347d64181a"
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
                "valueString": "Practitioner"
             },
             {
                "name": "name",
                "valueString": "prefix"
             },
             {
                "name": "value",
                "valueString": "Prefix"
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
                "valueString": "Practitioner"
             },
             {
                "name": "name",
                "valueString": "active"
             },
             {
                "name": "value",
                "valueBoolean": true
             }
          ]
       }
    ]
}
```

## Usuwanie lekarza

1. Użyj UUID lekarza dla identyfikacji docelowego zasobu

Przykład żadania XML:
```
DELETE /fhir/Practitioner/{id}
Accept: application/fhir+xml
Prefer: return=representation
```

## Wyszukiwanie lekarzy

1. Użyj filtrów wyszukiwania dołączając odpowiednie parametry kwerendy URL.

Przykład żądania XML:
```
POST /fhir/Practitioner/_search?_page=1&_count=5&name=Test
Content-Type: application/fhir+xml;fhirVersion=5.0
Accept: application/fhir+xml
Prefer: return=representation
```
### Parametry wyszukiwania

Obecnie, operacja wyszukiwania udostępnia poniższe parametry wyszukiwania i ich komparatory.
Jeśli parametr jest użyty bez prefixu komparatora, domyślnie stosuje się komparator eq (równość).
Użycie kilku parametrów jednocześnie interpretowane jest jako koniunkcja warunków (AND).

- pwz - identyfikator PWZ lekarza (komparatory eq, ap)
- name - imię lekarza (komparatory eq, ap)
- family - nazwisko lekarza (komparatory eq, ap)
- _page - numer strony wyników wyszukiwania
- _count - ilość elementów na stronie wyników wyszukiwania

Przykład kwerendy z dokładnym dopasowaniem numeru PWZ (brak komparatora lub komparator eq): 
```
/fhir/Practitioner/_search?pwz=123456
/fhir/Practitioner/_search?pwz=eq123456
```

Przykład kwerendy z przybliżonym dopasowaniem nazwiska (komparator ap):
```
/fhir/Practitioner/_search?family=apKowal
```