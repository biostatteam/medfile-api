# Subskrypcje

Zasób subskrypcji (Subscription) służy do ustanawiania proaktywnych powiadomień o zdarzeniach z serwera FHIR do innego systemu.
Subskrybenci żądają powiadomień o zdarzeniach w ramach wstępnie zdefiniowanych SubscriptionTopic, które obsługuje serwer,
Subskrybenci mogą precyzować swoje żądania poprzez filtry.
Zasób SubscriptionTopic może definiować zestaw dozwolonych filtrów (SubscriptionTopic.canFilterBy),
do których subskrybent odwołuje się w ramach zasobu Subscription (Subscription.filterBy).

Po utworzeniu subskrypcji każde zdarzenie pasujące do określonego SubscriptionTopic,
które spełnia kryteria filtrowania, spowoduje wysłanie powiadomienia za pomocą podanego kanału.

> ![FHIR logo](https://fhir.org/assets/images/HL7_FHIR-stacked_1_0_0.png)
> __Dokumentacja zasobów FHIR__
>- Subscription - [https://www.hl7.org/fhir/subscription.html](https://www.hl7.org/fhir/subscription.html)
>- SubscriptionTopic - [www.hl7.org/fhir/subscriptiontopic.html](https://www.hl7.org/fhir/subscriptiontopic.html)
>- SubscriptionStatus - [https://www.hl7.org/fhir/subscriptionstatus.html](https://www.hl7.org/fhir/subscriptionstatus.html)

# Spis treści

- [Subskrypcje](#subskrypcje)
- [Spis treści](#spis-treści)
- [Przetwarzanie zdarzeń i wysyłanie powiadomień](#przetwarzanie-zdarzeń-i-wysyłanie-powiadomień)
    - [Kolejkowanie zdarzeń systemowych](#kolejkowanie-zdarzeń-systemowych)
    - [Kolejkowanie powiadomień do klienta](#kolejkowanie-powiadomień-do-klienta)
- [Pobieranie listy obsługiwanych zdarzeń](#pobieranie-listy-obsługiwanych-zdarzeń)
- [Pobieranie szczegółów obsługiwanego zdarzenia](#pobieranie-szczegółów-obsługiwanego-zdarzenia)
- [Tworzenie subskrypcji](#tworzenie-subskrypcji)
- [Aktualizowanie subskrypcji](#aktualizowanie-subskrypcji)
- [Pobieranie szczegółów subskrypcji](#pobieranie-szczegółów-subskrypcji)
- [Wyszukiwanie subskrypcji](#wyszukiwanie-subskrypcji)
- [Usuwanie subskrypcji](#usuwanie-subskrypcji)
- [Odbieranie powiadomienia](#odbieranie-powiadomienia)

# Przetwarzanie zdarzeń i wysyłanie powiadomień

Dla zapewnienia niezawodnego przetwarzania żądań subskrypcji system stosuje kolejkowanie i ponawianie na następująch etapach:

### Kolejkowanie zdarzeń systemowych
Każde zdarzenie wyzwalane w systemie podlegające subskrypcji jest kolejkowane i przetwarzane sekwencyjnie:
- system wyzwala zdarzenie źródłowe i wysyła je do kolejki
- kolejka opróżniana jest metodą FIFO, z prędkością zależną od obciążenia systemu
- klasa docelowa (handler) reaguje na zdarzenie
- handler sprawdza czy istnieje subskrypcja klienta na to zdarzenie źródłowe
- handler tworzy żądanie powiadomienia wg parametrów zadeklarowanych w subskrypcji (URL, metoda, itp..)
- handler wysyła żądanie powiadomienia do kolejki wyjściowej

### Kolejkowanie powiadomień do klienta
Każde żądanie powiadomienia jest kolejkowane i przetwarzane sekwencyjnie:
- kolejka opróżniana jest metodą FIFO, z prędkością zależną od obciążenia systemu
- klasa docelowa (handler) reaguje na żądanie powiadomienia
- handler wysyła żądanie HTTP wg otrzymanych parametrów
- przy występieniu błędu, zdarzenie wróci do kolejki a handler podejmie kolejną próbę wysyłki (maksymalnie 3)

# Pobieranie listy obsługiwanych zdarzeń

Operacja pobiera listę obługiwanych zdarzeń systemowych które można subskrybować.
Zdarzenia podlegające subskrypcji reprezentowane są jako zasoby SubscriptionTopic, i identyfikowane unikatowo przez UUID, analogicznie do innych zasobów FHIR.
Pola SubscriptionTopic oprócz opisów obejmują wskazanie zasobu i działania wywołujących dane zdarzenie.
Lista jest predefiniowana, mozliwy jest wyłącznie odczyt.


```
POST /fhir/SubscriptionTopic/_search
Content-Type: application/fhir+xml;fhirVersion=5.0
Accept: application/fhir+xml
```

Przykład odpowiedzi XML (fragment, pojedynczy SubscriptionTopic, bez kontekstu Bundle):
```xml
<resource>
      <SubscriptionTopic>
        <id value="6b03e3bf-37aa-4d95-9873-af3448057c50"/>
        <url value="SubscriptionTopic/6b03e3bf-37aa-4d95-9873-af3448057c50"/>
        <version value="1.0"/>
        <name value="fhir.subscription.topic.encounter.insert"/>
        <title value="Encounter insert event subscription topic."/>
        <status value="active"/>
        <description value="Subscribes Encounter insert events."/>
        <resourceTrigger>
          <resource value="Encounter"/>
          <supportedInteraction value="create"/>
        </resourceTrigger>
        <resourceTrigger>
          <resource value="Appointment"/>
          <supportedInteraction value="create"/>
        </resourceTrigger>
      </SubscriptionTopic>
    </resource>
```
Przykład odpowiedzi JSON (fragment, pojedynczy SubscriptionTopic, bez kontekstu Bundle):
```json
{
  "resource": {
    "resourceType": "SubscriptionTopic",
    "id": "6b03e3bf-37aa-4d95-9873-af3448057c50",
    "url": "SubscriptionTopic\/6b03e3bf-37aa-4d95-9873-af3448057c50",
    "version": "1.0",
    "name": "fhir.subscription.topic.encounter.insert",
    "title": "Encounter insert event subscription topic.",
    "status": "active",
    "description": "Subscribes Encounter insert events.",
    "resourceTrigger": [
      {
        "resource": "Encounter",
        "supportedInteraction": [
          "create"
        ]
      },
      {
        "resource": "Appointment",
        "supportedInteraction": [
          "create"
        ]
      }
    ]
  }
}
```

# Pobieranie szczegółów obsługiwanego zdarzenia

Operacja pozwala na pobranie szczegółów pojedynczego zdarzenia systemowego które można subskrybować.

1. Ustal UUID zdarzenia. W tym celu możesz wykonać operację [Pobieranie listy obsługiwanych zdarzeń](#pobieranie-listy-obsługiwanych-zdarzeń).

```
GET /fhir/SubscriptionTopic/{id}
Content-Type: application/fhir+xml;fhirVersion=5.0
Accept: application/fhir+xml
Prefer: return=representation
```
Przykład odpowiedzi XML:
```xml
<?xml version="1.0" encoding="UTF-8"?>
<SubscriptionTopic xmlns="http://hl7.org/fhir">
  <id value="6b03e3bf-37aa-4d95-9873-af3448057c50"/>
  <url value="SubscriptionTopic/6b03e3bf-37aa-4d95-9873-af3448057c50"/>
  <version value="1.0"/>
  <name value="fhir.subscription.topic.encounter.insert"/>
  <title value="Encounter insert event subscription topic."/>
  <status value="active"/>
  <description value="Subscribes Encounter insert events."/>
  <resourceTrigger>
    <resource value="Encounter"/>
    <supportedInteraction value="create"/>
  </resourceTrigger>
  <resourceTrigger>
    <resource value="Appointment"/>
    <supportedInteraction value="create"/>
  </resourceTrigger>
</SubscriptionTopic>
```
Przykład odpowiedzi JSON:
```json
{
  "resourceType": "SubscriptionTopic",
  "id": "6b03e3bf-37aa-4d95-9873-af3448057c50",
  "url": "SubscriptionTopic\/6b03e3bf-37aa-4d95-9873-af3448057c50",
  "version": "1.0",
  "name": "fhir.subscription.topic.encounter.insert",
  "title": "Encounter insert event subscription topic.",
  "status": "active",
  "description": "Subscribes Encounter insert events.",
  "resourceTrigger": [
    {
      "resource": "Encounter",
      "supportedInteraction": [
        "create"
      ]
    },
    {
      "resource": "Appointment",
      "supportedInteraction": [
        "create"
      ]
    }
  ]
}
```

# Tworzenie subskrypcji

Operacja tworzy nową subskrypcję na wybrane zdarzenie.
Polecenie subskrypcji reprezentowane jest jako zasób Subscription, i identyfikowane unikatowo przez UUID, analogicznie do innych zasobów FHIR.
Subskrybowane zdarzenie reprezentowane jest przez UUID zasobu SubscriptionTopic.
Subskrypcja oznaczona statusem __active__ będzie aktywna od momentu zarejestrowania do daty ważności zadeklarowanej w jej właściwościach.  

1. Ustal UUID zdarzenia na które subskrypcja ma reagować. W tym celu możesz wykonać operację [Pobieranie listy obsługiwanych zdarzeń](#pobieranie-listy-obsługiwanych-zdarzeń).
2. Ustal parametry żądania zwrotnego, które ma być wykonane po wystąpieniu zdarzenia - adres URL, metodę HTTP, itp.
3. Ustal filtry zdarzenia, jeśli są dostępne dla danego zdarzenia. Filtry pozwalają na zawężenie warunków reakcji na zdarzenie.
4. Ustal datę ważności jeśli subkrypcja powinna być ograniczona czasowo.

```
POST /fhir/Subscription
Content-Type: application/fhir+xml;fhirVersion=5.0
Accept: application/fhir+xml
Prefer: return=representation
```
Przykład żądania XML:
```xml
<?xml version="1.0" encoding="UTF-8"?>
<Subscription xmlns="http://hl7.org/fhir">
  <status value="active"/>
  <topic value="SubscriptionTopic/6b03e3bf-37aa-4d95-9873-af3448057c50"/>
  <filterBy>
    <filterParameter value="b3681b01-6a5c-4040-b96f-b7d53110eeff"/>
  </filterBy>
  <channelType>
    <system value="http://hl7.org/fhir/ValueSet/subscription-channel-type"/>
    <code value="rest-hook"/>
  </channelType>
  <endpoint value="http://client.url"/>
</Subscription>
```

Przykład żądania JSON:
```json
{
  "resourceType": "Subscription",
  "status": "active",
  "topic": "SubscriptionTopic\/6b03e3bf-37aa-4d95-9873-af3448057c50",
  "filterBy": [
    {
      "filterParameter": "b3681b01-6a5c-4040-b96f-b7d53110eeff"
    }
  ],
  "channelType": {
    "system": "http:\/\/hl7.org\/fhir\/ValueSet\/subscription-channel-type",
    "code": "rest-hook"
  },
  "endpoint": "http:\/\/client.url"
}
```

# Aktualizowanie subskrypcji

1. Ustal UUID uprzednio utworzonej subskrypcji. W tym celu możęsz wykonać operację [Wyszukiwanie subskrypcji](#wyszukiwanie-subskrypcji).
2. Ustal UUID zdarzenia na które subskrypcja ma reagować. W tym celu możesz wykonać operację [Pobieranie listy obsługiwanych zdarzeń](#pobieranie-listy-obsługiwanych-zdarzeń).
3. Ustal parametry żądania zwrotnego, które ma być wykonane po wystąpieniu zdarzenia - adres URL, metodę HTTP, itp.
4. Ustal filtry zdarzenia, jeśli są dostępne dla danego zdarzenia. Filtry pozwalają na zawężenie warunków reakcji na zdarzenie.

```
PUT /fhir/Subscription/{id}
Content-Type: application/fhir+xml;fhirVersion=5.0
Accept: application/fhir+xml
Prefer: return=representation
```

Przykład żądania XML:
```xml
<?xml version="1.0" encoding="UTF-8"?>
<Subscription xmlns="http://hl7.org/fhir">
  <id value="0ee47b2d-e7c5-42dd-b6ef-0d28c45bd67d"/>
  <status value="active"/>
  <topic value="SubscriptionTopic/15d50e9e-fc23-4c4e-87ee-c31a3530f56b"/>
  <channelType>
    <system value="http://hl7.org/fhir/ValueSet/subscription-channel-type"/>
    <code value="rest-hook"/>
  </channelType>
  <endpoint value="http://client.url.updated"/>
</Subscription>
```

Przykład żądania JSON:
```json
{
  "resourceType": "Subscription",
  "id": "0ee47b2d-e7c5-42dd-b6ef-0d28c45bd67d",
  "status": "active",
  "topic": "SubscriptionTopic\/15d50e9e-fc23-4c4e-87ee-c31a3530f56b",
  "channelType": {
    "system": "http:\/\/hl7.org\/fhir\/ValueSet\/subscription-channel-type",
    "code": "rest-hook"
  },
  "endpoint": "http:\/\/client.url.updated"
}
```

# Pobieranie szczegółów subskrypcji

1. Ustal UUID uprzednio utworzonej subskrypcji. W tym celu możęsz wykonać operację [Wyszukiwanie subskrypcji](#wyszukiwanie-subskrypcji).

```
GET /fhir/Subscription/{id}
Content-Type: application/fhir+xml;fhirVersion=5.0
Accept: application/fhir+xml
Prefer: return=representation
```

Przykład odpowiedzi XML:
```xml
<?xml version="1.0" encoding="UTF-8"?>
<Subscription xmlns="http://hl7.org/fhir">
  <id value="ac581a4f-0cf3-46e0-b3e7-573d6d69da0a"/>
  <status value="active"/>
  <topic value="SubscriptionTopic/15d50e9e-fc23-4c4e-87ee-c31a3530f56b"/>
  <end value="2025-05-09T15:00:00+02:00"/>
  <channelType>
    <system value="http://hl7.org/fhir/ValueSet/subscription-channel-type"/>
    <code value="rest-hook"/>
  </channelType>
  <endpoint value="http://client"/>
  <parameter>
    <name value="http.method"/>
    <value value="PUT"/>
  </parameter>
  <contentType value="application/fhir+xml"/>
  <content value=" full-resource"/>
</Subscription>
```

Przykład odpowiedzi JSON:
```json
{
  "resourceType": "Subscription",
  "id": "ac581a4f-0cf3-46e0-b3e7-573d6d69da0a",
  "status": "active",
  "topic": "SubscriptionTopic\/15d50e9e-fc23-4c4e-87ee-c31a3530f56b",
  "end": "2025-05-09T15:00:00+02:00",
  "channelType": {
    "system": "http:\/\/hl7.org\/fhir\/ValueSet\/subscription-channel-type",
    "code": "rest-hook"
  },
  "endpoint": "http:\/\/client",
  "parameter": [
    {
      "name": "http.method",
      "value": "PUT"
    }
  ],
  "contentType": "application\/fhir+xml",
  "content": " full-resource"
}
```

# Wyszukiwanie subskrypcji

Operacja pobiera listę zarejestrowanych subskrypcji.
Zarejestrowane subskrypcje reprezentowane są przez zasoby Subscription, i identyfikowane unikatowo przez UUID, analogicznie do innych zasobów FHIR.

```
POST /fhir/Subscription/_search
Content-Type: application/fhir+xml;fhirVersion=5.0
Accept: application/fhir+xml
```
Przykład odpowiedzi XML:
```xml
<?xml version="1.0" encoding="UTF-8"?>
<Bundle xmlns="http://hl7.org/fhir">
    <identifier>
        <system value="urn:oid:2.16.840.1.113883.3.4424.2.7.biostat.17.1.2"/>
        <value value="a92f7774-010e-c22f-8451-1fc842d5aca8"/>
    </identifier>
    <type value="collection"/>
    <entry>
        <resource>
            <Composition>
                <id value="a92f7774-010e-c22f-8451-1fc842d5aca8"/>
                <identifier>
                    <system value="urn:oid:2.16.840.1.113883.3.4424.2.7.biostat.17.1.2"/>
                    <value value="a92f7774-010e-c22f-8451-1fc842d5aca8"/>
                </identifier>
                <status value="final"/>
                <date value="2025-05-09T10:40:20+02:00"/>
                <section>
                    <entry>
                        <reference value="/Subscription/ac581a4f-0cf3-46e0-b3e7-573d6d69da0a/"/>
                        <type value="Subscription"/>
                    </entry>
                </section>
            </Composition>
        </resource>
    </entry>
    <entry>
        <resource>
            <Subscription>
                <id value="ac581a4f-0cf3-46e0-b3e7-573d6d69da0a"/>
                <status value="active"/>
                <topic value="SubscriptionTopic/15d50e9e-fc23-4c4e-87ee-c31a3530f56b"/>
                <end value="2025-05-09T15:00:00+02:00"/>
                <channelType>
                    <system value="http://hl7.org/fhir/ValueSet/subscription-channel-type"/>
                    <code value="rest-hook"/>
                </channelType>
                <endpoint value="http://client"/>
                <parameter>
                    <name value="http.method"/>
                    <value value="PUT"/>
                </parameter>
                <contentType value="application/fhir+xml"/>
                <content value=" full-resource"/>
            </Subscription>
        </resource>
    </entry>
</Bundle>
```

Przykład odpowiedzi JSON:
```json
{
  "resourceType": "Bundle",
  "identifier": {
    "system": "urn:oid:2.16.840.1.113883.3.4424.2.7.biostat.17.1.2",
    "value": "a92f7774-010e-c22f-8451-1fc842d5aca8"
  },
  "type": "collection",
  "entry": [
    {
      "resource": {
        "resourceType": "Composition",
        "id": "a92f7774-010e-c22f-8451-1fc842d5aca8",
        "identifier": [
          {
            "system": "urn:oid:2.16.840.1.113883.3.4424.2.7.biostat.17.1.2",
            "value": "a92f7774-010e-c22f-8451-1fc842d5aca8"
          }
        ],
        "status": "final",
        "date": "2025-05-09T10:40:20+02:00",
        "section": [
          {
            "entry": [
              {
                "reference": "\/Subscription\/ac581a4f-0cf3-46e0-b3e7-573d6d69da0a\/",
                "type": "Subscription"
              }
            ]
          }
        ]
      }
    },
    {
      "resource": {
        "resourceType": "Subscription",
        "id": "ac581a4f-0cf3-46e0-b3e7-573d6d69da0a",
        "status": "active",
        "topic": "SubscriptionTopic\/15d50e9e-fc23-4c4e-87ee-c31a3530f56b",
        "end": "2025-05-09T15:00:00+02:00",
        "channelType": {
          "system": "http:\/\/hl7.org\/fhir\/ValueSet\/subscription-channel-type",
          "code": "rest-hook"
        },
        "endpoint": "http:\/\/client",
        "parameter": [
          {
            "name": "http.method",
            "value": "PUT"
          }
        ],
        "contentType": "application\/fhir+xml",
        "content": " full-resource"
      }
    }
  ]
}
```

# Usuwanie subskrypcji

Operacja usuwa wskazaną subskrypcję pobiera listę zarejestrowanych subskrypcji.

1. Ustal UUID uprzednio utworzonej subskrypcji. W tym celu możesz wykonać operację [Wyszukiwanie subskrypcji](#wyszukiwanie-subskrypcji).

```
DELETE /fhir/Subscription/{id}
Content-Type: application/fhir+xml;fhirVersion=5.0
Accept: application/fhir+xml
Prefer: return=representation
```

Operacja zwróci rezultat zależnie od preferencji zadeklarowanej w nagłówku.


# Odbieranie powiadomienia

Przy prawidłowo utworzonej i aktywnej subksrypcji, w reakcji na subskrybowane zdarzenie
system wykona żądanie HTTP na URL wskazany w subskrypcji.
Wiadomość HTTP obejmować może różną zawartość w zależności od zadeklarowanych w subskrypcji ustawień.

- w zależności od wartości elementu `Content`:
  - `full-resource` - zwrócony zostanie zasób SubscriptionStatus reprezentujący status powiadomienia.
    - w zależności w wartośći elementu `ContentType` zwrócony zostanie odpowiedni nagłówek HTTP Content-type
  - `id-only` - zwrócona zostanie standardowa referencja do subskrybowanego zasobu w postaci `<resource/<uuid>`
    - zwrócony zostanie nagłówek HTTP Content-type o wartości text/plain
  - brak elementu
    - zwrócona zostanie pusta treść HTTP
    - nagłówek HTTP Content-type zostanie pominięty

Przykład odpowiedzi full-resource XML:
```
<?xml version="1.0" encoding="UTF-8"?>
<SubscriptionStatus xmlns="http://hl7.org/fhir">
  <status value="active"/>
  <type value="event-notification"/>
  <notificationEvent>
    <eventNumber value="1748846037"/>
    <timestamp value="2025-06-02T08:33:57+02:00"/>
    <focus>
      <reference value="Encounter/0d8132d5-edbb-47ec-90ea-bfed25f1c452"/>
      <type value="Encounter"/>
    </focus>
  </notificationEvent>
  <subscription>
    <reference value="Subscription/ac581a4f-0cf3-46e0-b3e7-573d6d69da0a"/>
    <type value="Subscription"/>
  </subscription>
  <topic value="SubscriptionTopic/15d50e9e-fc23-4c4e-87ee-c31a3530f56b"/>
</SubscriptionStatus>
```
  
