# Food Express - Simulazione Esame di Stato Informatica 2025

Questo progetto fa parte del repository "trasferimento-quaderno-d-informatica-zAlessandro7" e simula una piattaforma web per la gestione di un servizio di consegna cibo a domicilio (Food Express). È stato sviluppato come parte di una simulazione della Seconda Prova di Informatica.

## Descrizione del Progetto

La piattaforma consente agli utenti di navigare tra i ristoranti partner, visualizzare i loro menù, aggiungere piatti al carrello, effettuare l'ordine (dopo aver effettuato il login come cliente) e visualizzare lo storico dei propri ordini.

## Funzionalità Implementate

*   **Installazione Database:** Script per creare e popolare automaticamente il database necessario.
*   **Autenticazione Utente:**
    *   Registrazione di nuovi utenti (come Cliente).
    *   Login utenti esistenti (come Cliente).
    *   Logout.
    *   Pagina Profilo per visualizzare e aggiornare i propri dati.
*   **Gestione Ristoranti e Menù:**
    *   Visualizzazione dell'elenco dei ristoranti attivi.
    *   Visualizzazione del menù completo di un singolo ristorante.
*   **Gestione Carrello:**
    *   Aggiunta di piatti al carrello (limitato a un ristorante per ordine).
    *   Visualizzazione del contenuto del carrello.
    *   Aggiornamento delle quantità o rimozione di piatti nel carrello.
*   **Checkout:**
    *   Riepilogo dell'ordine prima della conferma.
    *   Form per confermare indirizzo di consegna e metodo di pagamento.
*   **Gestione Ordini:**
    *   Salvataggio dell'ordine nel database dopo il checkout.
    *   Visualizzazione dello storico ordini per l'utente loggato ("I Miei Ordini").
    *   Possibilità di "Lasciare Recensione" per gli ordini con stato "Consegnato".

## Tecnologie Utilizzate

*   **Backend:** PHP (con estensione PDO per l'interazione con il database)
*   **Database:** MySQL / MariaDB
*   **Frontend:** HTML, CSS (personalizzato), JavaScript (per validazione client-side base e interazione), Bootstrap (CSS Framework)
*   **Ambiente di Sviluppo:** XAMPP (Apache, MySQL/MariaDB, PHP)

## Setup e Installazione

Per avviare il progetto nel tuo ambiente locale (es. con XAMPP):

1.  **Clona o Scarica il Repository:** Assicurati di avere l'intera struttura del progetto, inclusa la cartella `2simulazione-esame2025/` e tutti i suoi file e sottocartelle (`assets/`, ecc.). Metti la cartella principale (`trasferimento-quaderno-d-informatica-zAlessandro7/`) all'interno della directory `htdocs` del tuo XAMPP.

2.  **Avvia Apache e MySQL:** Assicurati che i moduli Apache e MySQL (MariaDB in XAMPP) siano attivi nel pannello di controllo di XAMPP.

3.  **Configura il Database:**
    *   Lo script di installazione creerà il database e l'utente necessari. Per eseguirlo, devi connetterti come utente amministratore (es. `root` con password vuota di default in XAMPP).
    *   Modifica il file **`trasferimento-quaderno-d-informatica-zAlessandro7/install.php`** (quello nella **root del repository principale**) e verifica che le credenziali di `$username_root` e `$password_root` siano corrette per il tuo ambiente MySQL.
    *   Accedi al file `install.php` tramite browser:
        `http://localhost/trasferimento-quaderno-d-informatica-zAlessandro7/install.php`
    *   Segui le istruzioni visualizzate nella pagina per completare l'installazione del database. Verranno creati i database per tutti i moduli, incluso `202425_5IB_ElTaras_FoodExpressDB`, e l'utente `ElTaras` con password `ciao`.

4.  **Configura il Progetto Food Express:**
    *   Vai nella cartella `2simulazione-esame2025/`.
    *   Apri il file **`database_config.php`** (dentro `2simulazione-esame2025/`).
    *   Verifica che le costanti `DB_HOST`, `DB_USER_APP`, `DB_PASS_APP`, `DB_CHARSET` corrispondano alle credenziali dell'utente `ElTaras` (create da `install.php`). Le costanti `DB_PREFIX` e `DB_NAME_FOODEXPRESS` dovrebbero già essere corrette.

5.  **Prepara le Immagini (Opzionale):**
    *   Crea la cartella `2simulazione-esame2025/assets/images/` e al suo interno `2simulazione-esame2025/assets/images/piatti/`.
    *   Metti immagini placeholder (es. `default_logo.png`, `default_piatto.png`) con i nomi usati nel codice (`index.php`, `ristorante.php`) o negli `INSERT` di `install.php` per le immagini locali.

6.  **Svuota la Cache (se necessario):** Se hai problemi con la visualizzazione della grafica, svuota la cache del tuo browser (Ctrl+F5).

## Come Utilizzare l'Applicazione

1.  Assicurati che Apache e MySQL siano in esecuzione.
2.  Accedi alla homepage del progetto Food Express tramite browser:
    `http://localhost/trasferimento-quaderno-d-informatica-zAlessandro7/2simulazione-esame2025/`
3.  Naviga tra i ristoranti.
4.  Aggiungi articoli al carrello.
5.  Effettua la registrazione o il login come cliente.
6.  Procedi al checkout per salvare l'ordine nel database.
7.  Visualizza i tuoi ordini nella sezione "I Miei Ordini".

**Per testare la funzionalità "Lascia Recensione":**

*   Completa un ordine tramite il checkout.
*   Accedi a **phpMyAdmin**, seleziona il database `202425_5IB_ElTaras_FoodExpressDB`, vai alla tabella `ORDINE`.
*   Trova l'ordine appena creato e modifica manualmente il campo `StatoOrdine` impostandolo su `'consegnato'`. Salva.
*   Torna alla pagina "I Miei Ordini" nel sito. Ora dovresti vedere il link "Lascia Recensione" accanto a quell'ordine.

