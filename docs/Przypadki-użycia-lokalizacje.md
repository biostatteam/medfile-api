# Lokalizacje

Zasób lokalizacji (Location) reprezentuje miejsce związane ze świadczeniem usług medycznych.
Może to być gabinet, placówka medyczna, lub organizacja nadrzędna.


> ![FHIR logo](https://fhir.org/assets/images/HL7_FHIR-stacked_1_0_0.png)
> __Dokumentacja zasobów FHIR__
>- Location - [https://www.hl7.org/fhir/location.html](https://www.hl7.org/fhir/location.html)

# Spis treści

- [Lokalizacje](#lokalizacje)
- [Spis treści](#spis-treści)
- [Pobieranie danych lokalizacji](#pobieranie-danych-lokalizacji)
- [Wyszukiwanie lokalizacji](#wyszukiwanie-lokalizacji)
      - [Obsługiwane parametry wyszukiwania](#obsługiwane-parametry-wyszukiwania)


# Pobieranie danych lokalizacji

Operacja pozwala na pobranie szczegółów pojedynczej lokalizacji.

1. Ustal UUID loaklizacji. W tym celu możesz wykonać operację [Wyszukiwanie lokalizacji](#wyszukiwanie-lokalizacji).

```
GET /fhir/Location/{id}
Content-Type: application/fhir+xml;fhirVersion=5.0
Accept: application/fhir+xml
Prefer: return=representation
```

# Wyszukiwanie lokalizacji

Operacja pobiera listę zarejestrowanych lokalizacji.
Zwracane dane reprezentowane są przez zasoby Location oraz identyfikowane unikatowo przez UUID, analogicznie do innych zasobów FHIR.

```
POST /fhir/Location/_search
Content-Type: application/fhir+xml;fhirVersion=5.0
Accept: application/fhir+xml
```

#### Obsługiwane parametry wyszukiwania
Poniższa tabela obejmuje aktualnie obsługiwane parametry wyszukiwania.

| Nazwa  | Opis                                 | Komparatory |
|--------|--------------------------------------|-------------|
| _page  | numer strony wyników wyszukiwania    | -           |
| _count | ilość wyników wyszukiwania na stronę | -           |
| name   | nazwa lokalizacji                    | eq, ap      |
| partof | identyfikator jednostki nadrzędnej   | -           |