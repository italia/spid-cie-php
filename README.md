# spid-php
Software Development Kit for easy SPID access integration with SimpleSAMLphp.

spid-php has been developed and is maintained by Michele D'Amico (@damikael), collaborator at AgID - Agenzia per l'Italia Digitale.

spid-php è uno script composer (https://getcomposer.org/) per Linux che semplifica e automatizza il processo di installazione e configurazione di SimpleSAMLphp (https://simplesamlphp.org/) per l'integrazione dell'autenticazione SPID all'interno di applicazioni PHP tramite lo SPID SP Access Button (https://github.com/italia/spid-sp-access-button). spid-php permette di realizzare un Service Provider per SPID in pochi secondi.

Durante il processo di setup lo script richiede l'inserimento delle seguenti informazioni:
* directory di installazione (directory corrente)
* directory root del webserver
* nome del link al quale collegare SimpleSAMLphp
* EntityID del service provider
* AttributeConsumingServiceIndex da richiedere all'IDP
* se inserire nella configurazione i dati dell'IDP di test (https://idp.spid.gov.it)
* se copiare nella root del webserver i file di esempio per l'integrazione del bottone
* i dati per la generazione del certificato X.509 per il service provider

e si occupa di eseguire i seguenti passi:
* scarica l'ultima versione di SimpleSAMLphp con le relative dipendenze
* scarica l'ultima versione dello spid smart button
* crea un certificato X.509 per il service provider
* scarica i metadata degli IDP di produzione tramite il metadata unico di configurazione (https://registry.spid.gov.it/metadata/idp/spid-entities-idps.xml)
* effettua tutte le necessarie configurazioni su SimpleSAMLphp
* predispone il template e le risorse grafiche dello smart button per essere utilizzate con SimpleSAMLphp

Al termine del processo di setup si potrà utilizzare il certificato X.509 creato nella directory /cert per registrare il service provider sull'ambiente di test tramite l'interfaccia di backoffice (https://idp.spid.gov.it:8080).
Se si è scelto di copiare i file di esempio, inoltre, sarà possibile verificare subito l'integrazione accedendo da web a /login.php o /user.php

## Requisiti
* Web server
* php >= 7
* php-xml
* Composer (https://getcomposer.org)
* OpenSSL 

## Installazione
```
# composer install
```

## Disinstallazione
```
# composer uninstall
```

## API
### Costruttore
```
new SPID_PHP(integer $spid_level)
```
$spid_level indica il livello SPID da richiedere

### isAuthenticated
```
bool isAuthenticated()
```
restituisce true se l'utente è autenticato, false altrimenti

### requireAuth
```
void requireAuth()
```
richiede che l'utente sia autenticato. Se l'utente non è autenticato mostra il pannello di scelta dell'IDP

### insertSPIDButton
```
void insertSPIDButton(string $size)
```
stampa il codice per l'inserimento dello smart button. $size specifica la dimensione del pulsante (S|M|L|XL)

### getAttributes
```
array getAttributes()
```
restituisce gli attributi dell'utente autenticato

### getAttribute
```
string getAttribute(string $attribute)
```
restituisce il valore per lo specifico attributo 

### logout
```
void logout()
```
esegue la disconnessione

### getLogoutURL
```
string getLogoutURL()
```
restituisce la url per eseguire la disconnessione


## Esempio di integrazione
```
require_once("<path to spid-php>/spid-php.php");
    
$spidsdk = new SPID_PHP(1);
if(!$spidsdk->isAuthenticated()) {
  $spidsdk->insertSPIDButton("L");
} else {
  foreach($spidsdk->getAttributes() as $attribute=>$value) {
    echo "<p>" . $attribute . ": <b>" . $value[0] . "</b></p>";
  }
}
```

