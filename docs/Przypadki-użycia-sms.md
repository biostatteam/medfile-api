# Wiadomości SMS

Zasób Communication reprezentuje jedną wiadomość SMS wysłaną do pacjenta lub odebraną od pacjenta przez konto Medfile.

Wiadomości SMS wysyła aplikacja Medfile (np. przypomnienia o wizytach, kody e-recept) albo użytkownik konta. API udostępnia je tylko do odczytu: odczyt wiadomości i wyszukiwanie wiadomości pacjenta lub numeru telefonu w zadanym okresie. Zwracane są wiadomości wychodzące i przychodzące, z wyjątkiem wiadomości zawierających dane poufne (patrz [Wiadomości pomijane](#wiadomości-pomijane)).

Wszystkie operacje obsługują formaty JSON i XML. Przykłady poniżej podano w formacie JSON, przykłady XML dostępne są w dokumentacji online Swagger UI ([https://medfile-api.uat.rstat.pl/fhirdoc](https://medfile-api.uat.rstat.pl/fhirdoc)).

> ![FHIR logo](https://fhir.org/assets/images/HL7_FHIR-stacked_1_0_0.png)
> __Dokumentacja zasobów FHIR__
>- Communication - [https://www.hl7.org/fhir/communication.html](https://www.hl7.org/fhir/communication.html)

# Spis treści

- [Wiadomości SMS](#wiadomości-sms)
- [Spis treści](#spis-treści)
- [Pola zasobu](#pola-zasobu)
  - [Status](#status)
  - [Typ wiadomości](#typ-wiadomości)
  - [Wiadomości pomijane](#wiadomości-pomijane)
- [Pobieranie wiadomości](#pobieranie-wiadomości)
- [Wyszukiwanie wiadomości](#wyszukiwanie-wiadomości)
  - [Obsługiwane parametry wyszukiwania](#obsługiwane-parametry-wyszukiwania)
  - [Wyszukiwanie po numerze telefonu](#wyszukiwanie-po-numerze-telefonu)
  - [Wyszukiwanie po dacie wysłania](#wyszukiwanie-po-dacie-wysłania)
- [Operacje nieobsługiwane](#operacje-nieobsługiwane)

# Pola zasobu

| Pole | Opis |
|---|---|
| `id` | identyfikator wiadomości (liczba) |
| `status` | status wysłania wiadomości, patrz [Status](#status) |
| `category` | typ wiadomości, `system` = `http://www.medfile.pl/fhir/CodeSystem/sms-type`, patrz [Typ wiadomości](#typ-wiadomości) |
| `medium` | zawsze `SMSWRIT` (wiadomość tekstowa SMS), `system` = `http://terminology.hl7.org/CodeSystem/v3-ParticipationMode` |
| `subject` | pacjent, `Patient/{uuid}` |
| `recipient` | wiadomość wychodząca: pacjent, `Patient/{uuid}` |
| `sender` | wiadomość przychodząca (odpowiedź pacjenta): pacjent, `Patient/{uuid}` |
| `encounter` | wizyta, której dotyczy wiadomość, `Encounter/{uuid}` |
| `sent` | data i godzina wysłania (dla wiadomości przychodzącej - odebrania) ze strefą czasową |
| `payload.contentCodeableConcept.text` | treść wiadomości |
| rozszerzenie `http://www.medfile.pl/fhir/StructureDefinition/communication-phone` | numer telefonu pacjenta (`valueContactPoint`, `system` = `sms`) - odbiorcy wiadomości wychodzącej albo nadawcy wiadomości przychodzącej. Numer podawany jest w postaci zapisanej w Medfile: z prefiksem `+48`, `48` albo bez prefiksu. |

Pola `subject`, `recipient`/`sender` i `encounter` występują tylko wtedy, gdy wiadomość jest powiązana z pacjentem lub wizytą. Wiadomość pacjenta usuniętego jest zwracana bez tych pól. Pola bez wartości są pomijane - zasób nie zawiera pustych napisów ani pustych elementów.

## Status

| Status FHIR | Wiadomość w Medfile |
|---|---|
| `in-progress` | wysłana, bez potwierdzenia doręczenia |
| `completed` | doręczona |
| `not-done` | niedoręczona |
| `unknown` | inny status |

## Typ wiadomości

Pole `category.coding.code` zawiera kod typu wiadomości, a `display` jego nazwę (gdy typ ma nazwę w Medfile). Przykładowe typy:

| Kod | Nazwa |
|---|---|
| `notify_about_visit` | Przypomnienie o wizycie |
| `notify_about_visit_scheduled` | Powiadomienie pacjenta o wizycie |
| `notify_about_change` | Powiadomienie pacjenta o zmianie w wizycie |
| `notify_about_close_visit` | Anulowanie wizyty |
| `notify_accept_visit` | Wizyta została zaakceptowana przez lekarza |
| `notify_about_e_visit` | Przypomnienie o e-Wizycie |
| `medconnect_visit_reminder_message` | Zaproszenie pacjenta do e-Wizyty |
| `online_payment_notification` | Wyślij link do płatności za wizytę |
| `visit_paid_message` | Wizyta opłacona |
| `send_csioz_code` | SMS z e-receptą |
| `send_patient_csioz` | Kody e-Recept |
| `send_patient_referral_code` | Kody e-Skierowań |
| `send_medsign_sms` | Dokumenty MedSign |
| `ask_for_opinion` | Prośba o opinię pacjenta |
| `custom` | Indywidualny SMS |
| `marketing` | Marketing (ręczny) |
| `user_marketing` | Inteligentny SMS |
| `patient_answer` | Odpowiedź pacjenta |
| `send_patient_answer_to_user` | Przekazanie odpowiedzi pacjenta do lekarza |
| `send_patient_birthday_wishes` | Wysłanie do pacjenta życzeń urodzinowych |
| `notify_patient_refund_ordered` | Powiadomienie pacjenta o zleceniu zwrotu |

Lista typów może się rozszerzać - integracja nie powinna odrzucać wiadomości o nieznanym kodzie typu.

## Wiadomości pomijane

Wiadomości z kodami logowania (typ `2fa`) i hasłami do eksportu raportów (typ `report_export_password`) nie są udostępniane - nie są zwracane w wyszukiwaniu, a ich odczyt zwraca kod `400 Bad Request`, tak jak dla nieistniejącej wiadomości.

# Pobieranie wiadomości

```
GET /fhir/Communication/{id}
Accept: application/fhir+json
```

Dla wiadomości spoza zautoryzowanego konta, nieistniejącej albo pomijanej serwer zwraca kod `400 Bad Request`.

Przykład odpowiedzi JSON:
```json
{
    "resourceType": "Communication",
    "id": "1024",
    "extension": [
        {
            "url": "http://www.medfile.pl/fhir/StructureDefinition/communication-phone",
            "valueContactPoint": { "system": "sms", "value": "+48600100200" }
        }
    ],
    "status": "completed",
    "category": [
        {
            "coding": [
                {
                    "system": "http://www.medfile.pl/fhir/CodeSystem/sms-type",
                    "code": "notify_about_visit",
                    "display": "Przypomnienie o wizycie"
                }
            ]
        }
    ],
    "medium": [
        {
            "coding": [
                { "system": "http://terminology.hl7.org/CodeSystem/v3-ParticipationMode", "code": "SMSWRIT" }
            ]
        }
    ],
    "subject": { "reference": "Patient/00000000-0000-4000-8000-000000000001", "type": "Patient" },
    "encounter": { "reference": "Encounter/00000000-0000-4000-8000-000000000021", "type": "Encounter" },
    "sent": "2026-09-14T18:00:05+02:00",
    "recipient": [
        { "reference": "Patient/00000000-0000-4000-8000-000000000001", "type": "Patient" }
    ],
    "payload": [
        {
            "contentCodeableConcept": {
                "text": "Przypominamy o wizycie 15.09.2026 o godz. 10:30. Gabinet Przykładowy."
            }
        }
    ]
}
```

# Wyszukiwanie wiadomości

Operacja pobiera listę wiadomości zautoryzowanego konta, od najnowszych. Wyszukiwanie wymaga parametru `patient` albo `phone` - bez żadnego z nich serwer zwraca kod `400 Bad Request`.

Wiadomości pacjenta z września 2026:

```
POST /fhir/Communication/_search?patient=Patient/{uuid pacjenta}&sent=ge2026-09-01&sent=le2026-09-30&_page=1&_count=20
Content-Type: application/fhir+json;fhirVersion=5.0
Accept: application/fhir+json
```

Wiadomości wysłane na numer telefonu od 1 września 2026:

```
POST /fhir/Communication/_search?phone=600100200&sent=ge2026-09-01
Content-Type: application/fhir+json;fhirVersion=5.0
Accept: application/fhir+json
```

#### Obsługiwane parametry wyszukiwania

Poniższa tabela obejmuje aktualnie obsługiwane parametry wyszukiwania. Parametry można łączyć. Parametr spoza listy oraz modyfikatory parametrów (np. `patient:missing`) zwracają kod `400 Bad Request`.

| Nazwa | Opis | Komparatory |
|---|---|---|
| _page | numer strony wyników wyszukiwania (domyślnie 1, maksymalnie 100) | - |
| _count | ilość wyników wyszukiwania na stronę (domyślnie 5, maksymalnie 200); `_count=0` zwraca tylko liczbę wyników (`total`) | - |
| patient | pacjent w postaci `Patient/{uuid}`; jedna wartość | - |
| phone | numer telefonu pacjenta (parametr własny Medfile), patrz [Wyszukiwanie po numerze telefonu](#wyszukiwanie-po-numerze-telefonu) | - |
| sent | data wysłania w formacie `RRRR-MM-DD`, patrz [Wyszukiwanie po dacie wysłania](#wyszukiwanie-po-dacie-wysłania) | eq, ge, le, gt, lt |

## Wyszukiwanie po numerze telefonu

Parametr `phone` wyszukuje wiadomości wysłane na numer pacjenta i odebrane z tego numeru. Standard FHIR nie przewiduje wyszukiwania zasobu Communication po numerze telefonu - jest to parametr własny Medfile.

- Podaj pełny numer telefonu. Wyszukiwanie po fragmencie numeru nie jest obsługiwane - numer niepełny lub nieprawidłowy zwraca kod `400 Bad Request`.
- Znaki inne niż cyfry (spacje, myślniki, nawiasy) są pomijane.
- Numery w Medfile zapisane są z prefiksem `+48`, `48` albo bez prefiksu. Wyszukiwanie znajduje wiadomości niezależnie od zapisu, np. `600100200`, `48600100200`, `+48600100200` i `600 100 200` dają ten sam wynik.

## Wyszukiwanie po dacie wysłania

Parametr `sent` przyjmuje datę w formacie `RRRR-MM-DD` z komparatorem; porównanie dotyczy całego dnia w strefie czasowej Medfile (`Europe/Warsaw`):

| Wartość | Wiadomości wysłane |
|---|---|
| `sent=2026-09-01` lub `sent=eq2026-09-01` | 1 września 2026 |
| `sent=ge2026-09-01` | 1 września 2026 i później |
| `sent=gt2026-09-01` | od 2 września 2026 |
| `sent=le2026-09-30` | 30 września 2026 i wcześniej |
| `sent=lt2026-09-30` | do 29 września 2026 |

Aby wyszukać wiadomości z okresu, powtórz parametr: `sent=ge2026-09-01&sent=le2026-09-30`.

> **Uwaga:** podawaj datę bez godziny. Wartość z godziną (np. `sent=ge2026-09-01T10:00:00Z`) nie jest obecnie porównywana z dokładnością do podanej chwili i może dać niepełny wynik. Do pobierania nowych wiadomości używaj zakresu dni i pomijaj wiadomości o znanym już `id`.

# Operacje nieobsługiwane

Wiadomości SMS są tylko do odczytu. Wysyłanie wiadomości (`POST /fhir/Communication`), aktualizacja (PUT, PATCH), usuwanie, historia zmian (`_history`) oraz pozostałe operacje zwracają kod `501 Not Implemented`. API nie udostępnia subskrypcji powiadomień o nowych wiadomościach SMS.
