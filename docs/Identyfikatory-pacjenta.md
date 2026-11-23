# Identyfikatory pacjenta

Medfile API obsługuje te same identyfikatory pacjenta co Medfile, czyli identyfikatory ze słownika OID platformy P1 (Centrum e-Zdrowia). Pacjenta bez numeru PESEL, np. cudzoziemca, zapisujesz i wyszukujesz tak samo jak pacjenta z PESEL, tylko z innym OID.

> ![FHIR logo](https://fhir.org/assets/images/HL7_FHIR-stacked_1_0_0.png)
> __Dokumentacja zasobów FHIR__
>- Patient.identifier - [https://hl7.org/fhir/R5/patient-definitions.html#Patient.identifier](https://hl7.org/fhir/R5/patient-definitions.html#Patient.identifier)

# Spis treści

- [Identyfikatory pacjenta](#identyfikatory-pacjenta)
- [Spis treści](#spis-treści)
- [Zasady](#zasady)
- [Budowa OID](#budowa-oid)
  - [Polska](#polska)
  - [Kraje z identyfikatorem krajowym, dowodem osobistym lub prawem jazdy](#kraje-z-identyfikatorem-krajowym-dowodem-osobistym-lub-prawem-jazdy)
- [Przykłady](#przykłady)

# Zasady

1. Każdy identyfikator przekaż jako osobny element `identifier`. Pacjent może mieć kilka identyfikatorów, np. PESEL i paszport.
2. `system` ma postać `urn:oid:<OID>`, gdzie OID budujesz według sekcji [Budowa OID](#budowa-oid).
3. `value` to numer identyfikatora lub dokumentu. Nie może być pusty.
4. OID musi być w słowniku P1 obsługiwanym przez Medfile (tabele poniżej). Żądanie z innym OID w `system` zostanie odrzucone.
5. Elementy `identifier`, których `system` nie zaczyna się od `urn:oid:`, nie są zapisywane jako identyfikatory pacjenta.
6. Identyfikator z OID numeru PESEL ustawia też PESEL pacjenta w kartotece.
7. Przy aktualizacji (`PUT`) identyfikator o tym samym OID dostaje nową wartość.

# Budowa OID

| Rodzaj identyfikatora | OID | Dostępny dla |
|---|---|---|
| Identyfikator krajowy (np. PESEL) | `2.16.840.1.113883.3.4424.1.1.<kod kraju>` | wybranych krajów, patrz [tabela krajów](#kraje-z-identyfikatorem-krajowym-dowodem-osobistym-lub-prawem-jazdy) |
| Dowód osobisty | `2.16.840.1.113883.3.4424.1.2.<kod kraju>` | wybranych krajów, patrz tabela krajów |
| Prawo jazdy | `2.16.840.1.113883.3.4424.1.3.<kod kraju>` | wybranych krajów, patrz tabela krajów |
| Paszport | `2.16.840.1.113883.4.330.<kod kraju>` | wszystkich krajów |
| Książeczka żeglarska | `2.16.840.1.113883.3.4424.1.5.<kod kraju>` | wszystkich krajów |
| Tymczasowe zaświadczenie tożsamości cudzoziemca (TZTC) | `2.16.840.1.113883.3.4424.1.8.616` | tylko Polski |

`<kod kraju>` to numeryczny kod kraju według ISO 3166-1, bez zer wiodących, np. Polska `616`, Niemcy `276`, Ukraina `804`, Belgia `56`, Afganistan `4`.

Dwa identyfikatory krajowe nie pasują do wzorca:

- Holandia (BSN, Burgerservicenummer): `2.16.840.1.113883.2.4.6.3`
- Norwegia (fødselsnummer): `2.16.578.1.12.4.1.4.1`

## Polska

| Identyfikator | `system` |
|---|---|
| PESEL | `urn:oid:2.16.840.1.113883.3.4424.1.1.616` |
| Paszport | `urn:oid:2.16.840.1.113883.4.330.616` |
| Książeczka żeglarska | `urn:oid:2.16.840.1.113883.3.4424.1.5.616` |
| TZTC | `urn:oid:2.16.840.1.113883.3.4424.1.8.616` |

Pacjenta z Polski identyfikuj numerem PESEL, a gdy go nie ma, paszportem.

## Kraje z identyfikatorem krajowym, dowodem osobistym lub prawem jazdy

Słownik P1 ma te identyfikatory tylko dla krajów z tabeli. Dla pozostałych krajów użyj paszportu albo książeczki żeglarskiej.

Wskazówki ze słownika P1:

- W wielu krajach identyfikator krajowy jest podany na karcie EKUZ w polu 6.
- Numer prawa jazdy jest zwykle w polu 5 dokumentu.
- Jeśli dowód osobisty lub prawo jazdy zawiera identyfikator osoby (np. w polu 4d prawa jazdy), P1 zaleca przekazać identyfikator krajowy zamiast numeru dokumentu.

| Kraj | Kod kraju | Identyfikator krajowy | Dowód osobisty | Prawo jazdy |
|---|---|---|---|---|
| Austria | 40 | `2.16.840.1.113883.3.4424.1.1.40` | `2.16.840.1.113883.3.4424.1.2.40` | `2.16.840.1.113883.3.4424.1.3.40` |
| Belgia | 56 | `2.16.840.1.113883.3.4424.1.1.56` | `2.16.840.1.113883.3.4424.1.2.56` | `2.16.840.1.113883.3.4424.1.3.56` |
| Bułgaria | 100 | `2.16.840.1.113883.3.4424.1.1.100` | `2.16.840.1.113883.3.4424.1.2.100` | `2.16.840.1.113883.3.4424.1.3.100` |
| Chorwacja | 191 | `2.16.840.1.113883.3.4424.1.1.191` | `2.16.840.1.113883.3.4424.1.2.191` | `2.16.840.1.113883.3.4424.1.3.191` |
| Cypr | 196 | `2.16.840.1.113883.3.4424.1.1.196` | `2.16.840.1.113883.3.4424.1.2.196` | `2.16.840.1.113883.3.4424.1.3.196` |
| Czechy | 203 | `2.16.840.1.113883.3.4424.1.1.203` | `2.16.840.1.113883.3.4424.1.2.203` | `2.16.840.1.113883.3.4424.1.3.203` |
| Dania | 208 | `2.16.840.1.113883.3.4424.1.1.208` | — | `2.16.840.1.113883.3.4424.1.3.208` |
| Estonia | 233 | `2.16.840.1.113883.3.4424.1.1.233` | `2.16.840.1.113883.3.4424.1.2.233` | `2.16.840.1.113883.3.4424.1.3.233` |
| Finlandia | 246 | `2.16.840.1.113883.3.4424.1.1.246` | `2.16.840.1.113883.3.4424.1.2.246` | `2.16.840.1.113883.3.4424.1.3.246` |
| Francja | 250 | `2.16.840.1.113883.3.4424.1.1.250` | `2.16.840.1.113883.3.4424.1.2.250` | `2.16.840.1.113883.3.4424.1.3.250` |
| Grecja | 300 | `2.16.840.1.113883.3.4424.1.1.300` | `2.16.840.1.113883.3.4424.1.2.300` | `2.16.840.1.113883.3.4424.1.3.300` |
| Hiszpania | 724 | `2.16.840.1.113883.3.4424.1.1.724` | `2.16.840.1.113883.3.4424.1.2.724` | `2.16.840.1.113883.3.4424.1.3.724` |
| Holandia | 528 | `2.16.840.1.113883.2.4.6.3` | `2.16.840.1.113883.3.4424.1.2.528` | `2.16.840.1.113883.3.4424.1.3.528` |
| Irlandia | 372 | `2.16.840.1.113883.3.4424.1.1.372` | — | `2.16.840.1.113883.3.4424.1.3.372` |
| Islandia | 352 | `2.16.840.1.113883.3.4424.1.1.352` | — | `2.16.840.1.113883.3.4424.1.3.352` |
| Liechtenstein | 438 | `2.16.840.1.113883.3.4424.1.1.438` | `2.16.840.1.113883.3.4424.1.2.438` | `2.16.840.1.113883.3.4424.1.3.438` |
| Litwa | 440 | `2.16.840.1.113883.3.4424.1.1.440` | `2.16.840.1.113883.3.4424.1.2.440` | `2.16.840.1.113883.3.4424.1.3.440` |
| Luksemburg | 442 | `2.16.840.1.113883.3.4424.1.1.442` | `2.16.840.1.113883.3.4424.1.2.442` | `2.16.840.1.113883.3.4424.1.3.442` |
| Łotwa | 428 | `2.16.840.1.113883.3.4424.1.1.428` | `2.16.840.1.113883.3.4424.1.2.428` | `2.16.840.1.113883.3.4424.1.3.428` |
| Malta | 470 | `2.16.840.1.113883.3.4424.1.1.470` | `2.16.840.1.113883.3.4424.1.2.470` | `2.16.840.1.113883.3.4424.1.3.470` |
| Monako | 492 | — | `2.16.840.1.113883.3.4424.1.2.492` | — |
| Niemcy | 276 | `2.16.840.1.113883.3.4424.1.1.276` | `2.16.840.1.113883.3.4424.1.2.276` | `2.16.840.1.113883.3.4424.1.3.276` |
| Norwegia | 578 | `2.16.578.1.12.4.1.4.1` | — | `2.16.840.1.113883.3.4424.1.3.578` |
| Polska | 616 | `2.16.840.1.113883.3.4424.1.1.616` | — | — |
| Portugalia | 620 | `2.16.840.1.113883.3.4424.1.1.620` | `2.16.840.1.113883.3.4424.1.2.620` | `2.16.840.1.113883.3.4424.1.3.620` |
| Rumunia | 642 | `2.16.840.1.113883.3.4424.1.1.642` | `2.16.840.1.113883.3.4424.1.2.642` | `2.16.840.1.113883.3.4424.1.3.642` |
| San Marino | 674 | `2.16.840.1.113883.3.4424.1.1.674` | — | — |
| Słowacja | 703 | `2.16.840.1.113883.3.4424.1.1.703` | `2.16.840.1.113883.3.4424.1.2.703` | `2.16.840.1.113883.3.4424.1.3.703` |
| Słowenia | 705 | `2.16.840.1.113883.3.4424.1.1.705` | `2.16.840.1.113883.3.4424.1.2.705` | `2.16.840.1.113883.3.4424.1.3.705` |
| Szwajcaria | 756 | `2.16.840.1.113883.3.4424.1.1.756` | `2.16.840.1.113883.3.4424.1.2.756` | `2.16.840.1.113883.3.4424.1.3.756` |
| Szwecja | 752 | `2.16.840.1.113883.3.4424.1.1.752` | `2.16.840.1.113883.3.4424.1.2.752` | `2.16.840.1.113883.3.4424.1.3.752` |
| Ukraina | 804 | — | `2.16.840.1.113883.3.4424.1.2.804` | `2.16.840.1.113883.3.4424.1.3.804` |
| Węgry | 348 | `2.16.840.1.113883.3.4424.1.1.348` | `2.16.840.1.113883.3.4424.1.2.348` | `2.16.840.1.113883.3.4424.1.3.348` |
| Wielka Brytania | 826 | `2.16.840.1.113883.3.4424.1.1.826` | — | `2.16.840.1.113883.3.4424.1.3.826` |
| Włochy | 380 | `2.16.840.1.113883.3.4424.1.1.380` | `2.16.840.1.113883.3.4424.1.2.380` | — |

# Przykłady

Fragment zasobu Patient dla pacjenta z Ukrainy identyfikowanego paszportem i dowodem osobistym (pełne żądanie: [Tworzenie i aktualizacja pacjenta](./Przypadki-uzycia.md#tworzenie-i-aktualizacja-pacjenta)):

```json
{
    "resourceType": "Patient",
    "identifier": [
        {
            "system": "urn:oid:2.16.840.1.113883.4.330.804",
            "value": "FX000000"
        },
        {
            "system": "urn:oid:2.16.840.1.113883.3.4424.1.2.804",
            "value": "000000000"
        }
    ]
}
```

Wyszukiwanie tego pacjenta po paszporcie (parametr `identifier` przyjmuje `system|wartość` albo samą wartość, patrz [Wyszukiwanie pacjentów](./Przypadki-uzycia.md#wyszukiwanie-pacjentów)):

```
POST /fhir/Patient/_search?identifier=urn:oid:2.16.840.1.113883.4.330.804|FX000000
Content-Type: application/fhir+json;fhirVersion=5.0
Accept: application/fhir+json
Prefer: return=representation
```
