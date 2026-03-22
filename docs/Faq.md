# FAQ

## Czy konto w Medfile App jest wymagane do korzystania z API?

Tak. Medfile API (FHIR) udostępnia dane powiązane z kontem klienta w aplikacji Medfile App. Bez aktywnego konta nie ma możliwości autoryzacji ani dostępu do zasobów.

## Czym różni się konto główne od konta pracownika?

| Cecha            | Konto główne              | Pracownik                               |
|------------------|---------------------------|-----------------------------------------|
| Uprawnienia      | Pełne — właściciel konta  | Ograniczone — nadane przez konto główne |
| Tworzenie        | Rejestracja w Medfile App | Utworzone w ramach konta głównego       |
| Liczba na koncie | Dokładnie jedno           | Dowolna                                 |

UUID konta głównego jest jednocześnie UUID użytkownika pełniącego rolę właściciela. Każdy pracownik posiada własny, odrębny UUID.

## Czy mogę wprowadzać prawdziwe dane osobowe na koncie testowym?

Nie, dane na koncie testowym powinny być fikcyjne.

## Jak zlecić usunięcie danych na środowisku testowym?

Proszę przesłać wniosek na integracja@medfile.pl.