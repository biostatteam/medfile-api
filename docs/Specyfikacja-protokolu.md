# Spis treści


- [Spis treści](#spis-treści)
- [Nagłówki żądań HTTP](#nagłówki-żądań-http)
  - [Nagłówek Accept](#nagłówek-accept)
  - [Nagłówek Content-Type](#nagłówek-content-type)
  - [Nagłówek Prefer](#nagłówek-prefer)
- [Odczytywanie odpowiedzi na podstawie nagłówka Prefer](#odczytywanie-odpowiedzi-na-podstawie-nagłówka-prefer)
  - [minimal](#minimal)
  - [OperationOutcome](#operationoutcome)
  - [representation](#representation)
  - [sparse](#sparse)
  - [DocumentReference](#documentreference)
- [Referencje do zasobów](#referencje-do-zasobów)
  - [Tworzenie referencji do zasobów](#tworzenie-referencji-do-zasobów)
  - [Tworzenie struktur FHIR Path Patch](#tworzenie-struktur-fhir-path-patch)
  - [Użycie kodów i stałych ValueSet](#użycie-kodów-i-stałych-valueset)
- [Wyszukiwanie zasobów](#wyszukiwanie-zasobów)
    - [Parametry stronicowania wyników](#parametry-stronicowania-wyników)
    - [Komparatory](#komparatory)
    - [Rezultaty wyszukiwania](#rezultaty-wyszukiwania)

# Nagłówki żądań HTTP

## Nagłówek Accept

**Przykład**: `Accept: application/fhir+xml`

Nagłówek `Accept` musi być obowiązkowo umieszczany w każdym żądaniu w celu identyfikacji formatu danych zwracanych dla preferencji `representation` ([zobacz: Prefer](#nagłówek-prefer)).
Wartości nagłówka jest zgodna ze specyfikacją FHIR i obejmuje MIME type oraz oznaczenie wersji oddzielone średnikiem.
Obecnie wspierana jest wyłącznie formaty `fhirVersion=5.0`, w związku tym dopuszczalny jest nagłówek posiadający tylko pierwszy segment MIME type.
Aktualnie wspierane formaty to:
- `application/fhir+xml` - zwracany jest rezultat operacji w formacie XML
- `application/fhir+json` - zwracany jest rezultat operacji w formacie JSON

## Nagłówek Content-Type

**Przykład**: `Content-Type: application/fhir+xml;fhirVersion=5.0`

Nagłówek `Content-Type` jest obowiązkowo umieszczony w każdym żądaniu w celu identyfikacji formatu przekazywanych danych.
Wartości nagłówka jest zgodna ze specyfikacją FHIR i obejmuje MIME type oraz oznaczenie wersji oddzielone średnikiem.
Obecnie wspierana jest wyłącznie wersja `fhirVersion=5.0`, w związku tym dopuszczalny jest nagłówek posiadający tylko pierwszy segment MIME type.
Oprócz MIME type właściwych dla specyfikacji FHIR dopuszczalne jest przekazywanie treści żądania w formie zakodowanej Base64.
W takim przypadku MIME type należy rozszerzyć o suffix `+base64` otrzymując np: `Content-Type: application/fhir+xml+base64;fhirVersion=5.0`

## Nagłówek Prefer

**Przykład**: `Prefer: return=representation`

Nagłówek `Prefer` może być opcjonalnie umieszczany w żądaniu w celu identyfikacji formatu zwracanych danych.
Wartości nagłówka jest zgodna ze specyfikacją FHIR ([https://www.hl7.org/fhir/http.html#ops](https://www.hl7.org/fhir/http.html#ops)),
i musi być jedną z wartości:
- **minimal** - zwracany jest pojedynczy literał reprezentujący rezultat operacji
- **representation** - zwracany jest cały zasób reprezentujący rezultat operacji
- **sparse** - zwracany jest zasób reprezentujący rezultat operacji, z wykluczeniem pól pustych
- **OperationOutcome** - zwracany jest zasób OperationOutcome zawierający status operacji
- **DocumentReference** - (rozszerzenie, tylko w wybranych przypadkach) zwracany jest obiekt DocumentReference zawierający reprezentację zasobu w formie dokumentu/pliku. 

Wybraną wartość należy umieść w żądaniu w formie przypisania `Prefer: return={wartość}`.  
Nagłówek jest respektowany w przypadku operacji create, update, patch lub delete.
Przy braku nagłówka treść odpowiedzi poprawnej będzie pusta. 

# Odczytywanie odpowiedzi na podstawie nagłówka Prefer

## minimal

**Nagłówek**: `Prefer: return=minimal`

Odpowiedzi w preferencji `minimal` zawierają pojedynczy literał reprezentujący rezultat operacji.
W przypadku operacji create i update są to identyfikatory GUID, identyfikatory złożone z liczb naturalnych, lub referencje.
W przypadku operacji delete jest to data i czas usunięcia, którym oznaczony został żądany zasób.

## OperationOutcome

**Nagłówek**: `Prefer: return=OperationOutcome`

Odpowiedzi w preferencji `OperationOutcome` zawierają cały zasób HL7 FHIR OperationOutcome przeznaczony do reprezentowania statusu operacji, np:
```xml
<?xml version="1.0" encoding="UTF-8"?>
<OperationOutcome xmlns="http://hl7.org/fhir">
  <issue>
    <severity value="success"/>
    <expression value="Patient/13182d43-f82b-19a4-6030-754693098945"/>
  </issue>
</OperationOutcome>
```

- Pole severity zawiera stałą ValueSet [http://hl7.org/fhir/ValueSet/issue-severity](http://hl7.org/fhir/ValueSet/issue-severity).
- Pole expression zawiera literał reprezentujący rezultat operacji, który może być inny dla poszczególnych operacji.

## representation

**Nagłówek**: `Prefer: return=representation`

Odpowiedzi w preferencji `representation` zawierają cały zasób HL7 FHIR, którego operacja dotyczy.
W przypadku operacji create/update są to reprezentacje XML/JSON na podstawie nagłówka Accept utworzonych/zaktualizowanych zasobów.
W przypadku operacji delete jest to reprezentacja XML/JSON na podstawie nagłówka Accept zasóbu w momencie usunięcia.

## sparse

**Nagłówek**: `Prefer: return=sparse`

Odpowiedzi w preferencji `sparse` zawierają zasób HL7 FHIR, z wykluczeniem pól pustych.
W przypadku operacji create/update są to reprezentacje XML/JSON na podstawie nagłówka Accept utworzonych/zaktualizowanych zasobów.
W przypadku operacji delete jest to reprezentacja XML/JSON na podstawie nagłówka Accept zasóbu w momencie usunięcia.


## DocumentReference

**Nagłówek**: `Prefer: return=DocumentReference`

Odpowiedzi w preferencji `DocumentReference` zawierają obiekt DocumentReference z reprezentacją zasobu w formie strumienia danuch dokumentu/pliku.
Opcja dostępna jest tylko w wybranych przypadkach, i tylko dla operacji odczytu (read). Zwracane dane pliku są zakodowane Base 64.

# Referencje do zasobów

## Tworzenie referencji do zasobów

**Przykład**: `Patient/13182d43-f82b-19a4-6030-754693098945`

Referencje do zasobów należy utworzyć zgodnie ze specyfikacją HL7 FHIR.
Reprezentowane są przez ścieżkę URL i powinny składać się z dwóch segmentów: `<nazwa zasobu>/<identyfikator zasobu`. 
Nazwa zasobu musi być stałą ResourceType ValueSet z terminologii FHIR: [http://hl7.org/fhir/ValueSet/resource-types](http://hl7.org/fhir/ValueSet/resource-types). 
Identyfikator zasobu jest przeważnie identyfikatorem GUID w formacie `8-4-4-4-12` znaków, np: `22379e3b-da4e-d6e9-c70d-f5c2b4faeb06`.
W pojedynczych scenariuszach użycia identyfikator może być liczbą naturalną. Takie przypadki zostały omówione przy odpowiednich scenariuszach.

## Tworzenie struktur FHIR Path Patch

Specyfikacja HL7 FHIR obejmuje zasób Parameter wykorzystywane m.in. do redagowania żądań selektywnej aktualizacji zasobów przez API.
Medfile FHIR API określa parametry poszczególnych pól podlegających aktualizacji w danych zasobie.
Kompletne przykłady struktur Parameters dla różnych zasobów dostępne są w ramach przykładów dokumentacji online Swagger UI ([http://medfile-api.uat.rstat.pl/fhirdoc](http://medfile-api.uat.rstat.pl/fhirdoc)).

## Użycie kodów i stałych ValueSet

**Przykład**: `<system value="http://hl7.org/fhir/ValueSet/all-languages"/>`

Zgodnie ze specyfikacją HL7 FHIR, niektóre wartości zasobów reprezentowane są przez stałe z terminologii FHIR.
W celu identyfikacji słownika, z jakiego pochodzi dana wartość (ValueSet) należy wskazać jego URL w polu `system`, tak jak w przykładzie.
Zawartość URL musi być identyczna z `officialUrl` publikowanym na stronie specyfikacji danego ValueSet. 


# Wyszukiwanie zasobów

Wyszukiwanie realizowane jest w ramach operacji POST search w kontekście poszczególnych zasobów.
Na przykład operacja wyszukiwania lekarza jest udostępniana przez:
```
POST /fhir/Practitioner/_search

POST /fhir/Practitioner/_search?_page=1&_count=5&name=Test
```
Zgodnie ze specyfikacją FHIR, parametry wyszukiwania przekazywane są jako kolejne segmenty query string.
Lista parametrów jest różna dla każdego zasobu.
Aktualne Listy dostępne są przy opisach operacji dokumentacji SwaggerUI udostępnianej pod adresem URL endpointu API, w ścieżce `/fhirdoc`.

### Parametry stronicowania wyników
Parametrami wspólnymi dla większości zasobów są `_page` i `_count`
określające żądaną stronę wyników i ilość wyników na stronie.

### Komparatory
Dopasowania wartości parametrów mogą być realizowane z użyciem komparatorów zdefiniowanych w specyfikacji FHIR ([Prefixes](https://www.hl7.org/fhir/search.html#prefix)).
Poszczególne zasoby mogą deklarować różną listę wspieranych komparatorów.
Aktualne Listy dostępne są przy opisach operacji dokumentacji SwaggerUI udostępnianej pod adresem URL endpointu API, w ścieżce `/fhirdoc`.

Pełna lista komparatorów FHIR i ich znaczenie:
- eq - the resource value is equal to or fully contained by the parameter value
- ne - the resource value is not equal to the parameter value
- gt - the resource value is greater than the parameter value
- lt - the resource value is less than the parameter value 
- ge - the resource value is greater or equal to the parameter value
- le - the resource value is less or equal to the parameter value
- sa - the resource value starts after the parameter value
- eb - the resource value ends before the parameter value
- ap - the resource value is approximately the same to the parameter value


### Rezultaty wyszukiwania
Zgodnie ze specyfikacją FHIR, rezultaty wyszukiwania zwracane są jako zasób typu Bundle.
Identyfikatory zasobów występujących w Bundle zwracane są zbiorczo w sekcji composition.
Serializacja zwracancych zasobów przebiega taką samą metodą jak w przypadku operacji read, i obejmuje tę same pola i wartości.


