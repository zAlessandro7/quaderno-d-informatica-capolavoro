# Piattaforma Lingue Interattiva - Simulazione Esame di Stato Informatica 2025

Questo progetto fa parte del repository "trasferimento-quaderno-d-informatica-zAlessandro7" e simula una piattaforma web per l'apprendimento delle lingue straniere tramite corsi ed esercizi interattivi. È stato sviluppato come parte di una simulazione della Seconda Prova di Informatica.

## Descrizione del Progetto

La piattaforma consente a Insegnanti e Studenti di registrarsi e accedere. Gli insegnanti possono creare corsi virtuali e gestirli, mentre gli studenti possono visualizzare i corsi disponibili, iscriversi (concettualmente, tramite codice) e svolgere esercizi (l'implementazione completa degli esercizi e dello svolgimento è un TODO).

## Funzionalità Implementate (e TODO)

*   **Installazione Database:** La struttura del database per questo progetto (`202425_5IB_ElTaras_LinguePlatformDB`) viene creata dallo script `install.php` nella root del repository principale, insieme agli altri database.
*   **Autenticazione Utente:**
    *   Registrazione di nuovi utenti (come Studente o Insegnante).
    *   Login utenti esistenti (come Studente o Insegnante).
    *   Logout.
    *   Pagina Profilo per visualizzare e aggiornare i propri dati.
*   **Gestione Corsi:**
    *   Visualizzazione dell'elenco di tutti i corsi attivi disponibili.
    *   Creazione di nuovi corsi virtuali da parte degli Insegnanti loggati.
    *   Dashboard Insegnante: Elenco dei corsi creati dall'insegnante loggato.
    *   Visualizzazione dei dettagli di un corso specifico e l'elenco degli esercizi al suo interno.
*   **Gestione Esercizi:**
    *   Il database include un catalogo di esercizi.
    *   I corsi sono associati a esercizi specifici del catalogo.
    *   *(TODO: Implementare la visualizzazione completa del contenuto degli esercizi e la logica per registrarne lo svolgimento e il punteggio).*
*   **Classifiche:**
    *   *(TODO: Implementare la visualizzazione delle classifiche per esercizio e per corso).*

## Tecnologie Utilizzate

*   **Backend:** PHP (con estensione PDO per l'interazione con il database)
*   **Database:** MySQL / MariaDB
*   **Frontend:** HTML, CSS (personalizzato), JavaScript (per validazione client-side base e UI), Bootstrap (CSS Framework)
*   **Ambiente di Sviluppo:** XAMPP (Apache, MySQL/MariaDB, PHP)

## Setup e Installazione

Per avviare il progetto nel tuo ambiente locale (es. con XAMPP):

1.  **Clona o Scarica il Repository:** Assicurati di avere l'intera struttura del repository principale (`trasferimento-quaderno-d-informatica-zAlessandro7/`) all'interno della directory `htdocs` del tuo XAMPP.
2.  **Verifica la Struttura delle Cartelle:** Assicurati che la cartella `1simulazione-esame2025/` esista all'interno del repository principale e che tutti i file PHP e la cartella `assets/` descritti di seguito siano al suo interno.
3.  **Avvia Apache e MySQL:** Assicurati che i moduli Apache e MySQL (MariaDB in XAMPP) siano attivi nel pannello di controllo di XAMPP.
4.  **Configura e Installa il Database:**
    *   Modifica il file **`trasferimento-quaderno-d-informatica-zAlessandro7/install.php`** (quello nella **root del repository principale**) e verifica che le credenziali di `$username_root` e `$password_root` siano corrette per il tuo ambiente MySQL.
    *   Accedi al file `install.php` tramite browser:
        `http://localhost/trasferimento-quaderno-d-informatica-zAlessandro7/install.php`
    *   Segui le istruzioni per completare l'installazione. Questo creerà il database `202425_5IB_ElTaras_LinguePlatformDB` (e gli altri database dei moduli) e l'utente `ElTaras` con password `ciao`.

## Configurazione Specifica (1simulazione-esame2025/)

*   Apri il file **`1simulazione-esame2025/lp_database_config.php`**.
*   Verifica che le costanti `LP_DB_HOST`, `LP_DB_USER_APP`, `LP_DB_PASS_APP`, `LP_DB_CHARSET` e `LP_DB_PREFIX_GENERAL` corrispondano alle credenziali e al prefisso definiti in `install.php`.

## Come Utilizzare l'Applicazione (Piattaforma Lingue)

1.  Assicurati che Apache e MySQL siano in esecuzione e che il database sia stato installato correttamente.
2.  Accedi alla homepage della Piattaforma Lingue tramite browser:
    `http://localhost/trasferimento-quaderno-d-informatica-zAlessandro7/1simulazione-esame2025/`
3.  Dalla homepage, puoi:
    *   Registrare un nuovo account (come Studente o Insegnante).
    *   Accedere con un account esistente.
    *   Visualizzare l'elenco dei Corsi disponibili.
4.  Dopo il login, verrai reindirizzato alla tua dashboard (Studente o Insegnante).
5.  Da Insegnante, puoi creare nuovi corsi. Dalla dashboard Insegnante o dalla lista corsi, puoi vedere i dettagli di un corso specifico.
