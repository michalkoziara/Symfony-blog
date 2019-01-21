# Symfony-blog

Wymagania wstępne (dla systemu Windows):

1. Zainstaluj composer w celu pobrania zależności
    (https://getcomposer.org/)

1. Dodaj obsług APCu do PHP, moduł ten wymagany jest do wydajniejszego cachowania strony;
    (Dodatkowa instrukcja: https://stackoverflow.com/questions/24448261/how-to-install-apcu-in-windows)

2. Wejdź do głównego folderu projektu i z linii poleceń wywołaj polecenia

    ``composer install``
    
    ``php bin\console server:run``
    
   Aplikacja powinna zostać uruchomiona.
   
W celu zmiany bazy danych należy odpowiednio zmodyfikować pliki ``.env`` i ``config/packages/doctrine.yaml`` a następnie dokonać migracji
Domyślnie ustawiona jest baza MySQL
``mysql://root:@127.0.0.1:3306/app``
