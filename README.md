# spid-php
Software Development Kit for easy SPID access integration with SimpleSAMLphp.

spid-php has been developed and is maintained by Michele D'Amico (@damikael), collaborator at AgID - Agenzia per l'Italia Digitale. **It's highly recommended to use the latest release**.


spid-php è uno script composer (https://getcomposer.org/) per Linux che semplifica e automatizza il processo di installazione e configurazione di SimpleSAMLphp (https://simplesamlphp.org/) per l'integrazione dell'autenticazione SPID all'interno di applicazioni PHP tramite lo SPID SP Access Button (https://github.com/italia/spid-sp-access-button). spid-php permette di realizzare un Service Provider per SPID in pochi secondi. **Si raccomanda di mantenere sempre aggiornata la propria installazione all'ultima versione**.

Durante il processo di setup lo script richiede l'inserimento delle seguenti informazioni:
* directory di installazione (directory corrente)
* directory root del webserver
* nome del link al quale collegare SimpleSAMLphp
* EntityID del service provider
* Informazioni del service provider da inserire nel metadata
* AttributeConsumingServiceIndex da richiedere all'IDP
* Attributi richiesti da inserire nel metadata
* se inserire nella configurazione i dati dell'IDP di test (https://idp.spid.gov.it)
* se inserire nella configurazione i dati dell'IDP di validazione (https://validator.spid.gov.it)
* se copiare nella root del webserver i file di esempio per l'integrazione del bottone
* i dati per la generazione del certificato X.509 per il service provider

e si occupa di eseguire i seguenti passi:
* scarica l'ultima versione di SimpleSAMLphp con le relative dipendenze
* scarica l'ultima versione dello spid-sp-access-button
* crea un certificato X.509 per il service provider
* scarica i metadata degli IDP di produzione tramite il metadata unico di configurazione (https://registry.spid.gov.it/metadata/idp/spid-entities-idps.xml)
* effettua tutte le necessarie configurazioni su SimpleSAMLphp
* predispone il template e le risorse grafiche dello SPID SP Access Button per essere utilizzate con SimpleSAMLphp

Al termine del processo di setup si potrà scaricare il [metadata](#Metadata) oppure utilizzare il certificato X.509 creato nella directory /cert per registrare il service provider sull'ambiente di test tramite l'interfaccia di backoffice (https://idp.spid.gov.it:8080).
Se si è scelto di copiare i file di esempio, inoltre, sarà possibile verificare subito l'integrazione accedendo da web a /login-spid.php

## Requisiti
* Web server
* php >= 7
* php-xml
* php-mbstring
* Composer (https://getcomposer.org)
* OpenSSL 
* OpenSSL aes-256-cbc Cipher Algorithm

## Installazione
```
# composer install
```
Al termine dell'installazione tutte le configurazioni sono salvate nel file *spid-php-setup.json*. In caso di reinstallazione, le informazioni di configurazione saranno recuperate automaticamente da tale file, senza la necessità di doverle reinserire nuovamente.  

## Disinstallazione
```
# composer uninstall
```
La disinstallazione non cancella l'eventuale file *spid-php-setup.json* locale che contiene le configurazioni inserite durante il processo in installazione.

## Aggiornamento Metadata IdP
```
# composer update-metadata
```

## Reinstallazione / Aggiornamento
```
# composer uninstall
# composer install
```
Se nella directory locale è presente il file *spid-php-setup.json* l'aggiornamento non richiederà nuovamente le informazioni di configurazione. Per modificare le informazioni di configurazione precedentemente inserite, occorrerà eseguire la disinstallazione, cancellare manualmente il file *spid-php-setup.json*, quindi procedere alla nuova installazione.

## Configurazione nginx
Per utilizzare SimpleSAMLphp su webserver nginx occorre configurare nginx come nell'esempio seguente.
In *myservice* inserire il nome del servizio come specificato durante l'installazione.

```
server {  
  listen 443 ssl http2;  
  server_name sp.example.com;
  root /var/www;
  include snippets/snakeoil.conf;  
  
  location / {
    try_files $uri $uri/ =404;
    index index.php;
    location ~ \.php$ {
      include snippets/fastcgi-php.conf;  
      fastcgi_pass unix:/var/run/php/php7.0-fpm.sock;  
    }
  }
  
  location /myservice/ {
    index index.php;
    location ~ \.php(/|$) {
      fastcgi_split_path_info ^(.+?\.php)(/.+)$;
      try_files $fastcgi_script_name =404;
      set $path_info $fastcgi_path_info;
      fastcgi_param PATH_INFO $path_info;
      fastcgi_pass unix:/var/run/php/php7.0-fpm.sock;  
      fastcgi_index index.php;
      include fastcgi.conf;
    }
  }
}
```

## Metadata
Dopo aver completato la procedura di installazione il metadata del service provider è scaricabile alla seguente url:

**/*myservice*/module.php/saml/sp/metadata.php/service**

dove *myservice* è il nome del servizio come specificato durante l'installazione.

## API
### Costruttore
```
new SPID_PHP()
```

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

### insertSPIDButtonCSS
```
void insertSPIDButtonCSS()
```
inserisce i riferimenti css necessari per la presentazione del bottone 

### insertSPIDButtonJS
```
void insertSPIDButtonJS()
```
inserisce i riferimenti al codice javascript necessario al funzionamento del bottone

### insertSPIDButton
```
void insertSPIDButton(string $size)
```
stampa il codice per l'inserimento dello smart button. $size specifica la dimensione del pulsante (S|M|L|XL)

### login
```
void login($idp, $level, [$returnTo])
```
invia una richiesta di login livello $level verso l'idp $idp. Dopo l'autenticazione, l'utente è reindirizzato alla url eventualmente specificata in $returnTo. Se il parametro $returnTo non è specificato, l'utente è reindirizzato alla pagina di provenienza.

$idp può assumere uno dei seguenti valori:
* VALIDATOR
* ArubaPEC S.p.A.
* InfoCert S.p.A.
* IN.TE.S.A. S.p.A.
* Lepida S.p.A.
* Namirial
* Poste Italiane SpA
* Sielte S.p.A.
* Register.it S.p.A.
* TI Trust Technologies srl

$level può assumere uno dei seguenti valori
* 1
* 2
* 3

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
void logout([$returnTo])
```
esegue la disconnessione. Dopo la disconnessione, l'utente è reindirizzato alla url specificata in $returnTo oppure alla pagina di provenienza se $returnTo non è specificato.

### getLogoutURL
```
string getLogoutURL([$returnTo])
```
restituisce la url per eseguire la disconnessione. Dopo la disconnessione, l'utente è reindirizzato alla url specificata in $returnTo oppure alla pagina di provenienza se $returnTo non è specificato.


## Esempio di integrazione
```
require_once("<path to spid-php>/spid-php.php");

$spidsdk = new SPID_PHP();

if(!$spidsdk->isAuthenticated()) {
    if(!isset($_GET['idp'])) {
        $spidsdk->insertSPIDButtonCSS();
        $spidsdk->insertSPIDButton("L");       
        $spidsdk->insertSPIDButtonJS();         
    } else {
        $spidsdk->login($_GET['idp'], 1);                
    }
} else {
    foreach($spidsdk->getAttributes() as $attribute=>$value) {
        echo "<p>" . $attribute . ": <b>" . $value[0] . "</b></p>";
    }
    
    echo "<hr/><p><a href='" . $spidsdk->getLogoutURL() . "'>Logout</a></p>";
}


```

## Compliance

|<img src="https://github.com/italia/spid-graphics/blob/master/spid-logos/spid-logo-c-lb.png?raw=true" width="100" /><br />_Compliance with [SPID regulations](http://www.agid.gov.it/sites/default/files/circolari/spid-regole_tecniche_v1.pdf) (for Service Providers)_|status|notes|
|:---|:---|:---|
|**Metadata:**|||
|parsing of IdP XML metadata (1.2.2.4)|✓||
|parsing of AA XML metadata (2.2.4)|||
|SP XML metadata generation (1.3.2)|✓||
|**AuthnRequest generation (1.2.2.1):**|||
|generation of AuthnRequest XML|✓||
|HTTP-Redirect binding|✓||
|HTTP-POST binding|✓||
|`AssertionConsumerServiceURL` customization|||
|`AssertionConsumerServiceIndex` customization|||
|`AttributeConsumingServiceIndex` customization|✓||
|`AuthnContextClassRef` (SPID level) customization|✓||
|`RequestedAuthnContext/@Comparison` customization|✓||
|`RelayState` customization (1.2.2)|✓||
|**Response/Assertion parsing**|||
|verification of `Response/Signature` value (if any)|✓||
|verification of `Response/Signature` certificate (if any) against IdP/AA metadata|✓||
|verification of `Assertion/Signature` value|✓||
|verification of `Assertion/Signature` certificate against IdP/AA metadata|✓||
|verification of `SubjectConfirmationData/@Recipient`|✓||
|verification of `SubjectConfirmationData/@NotOnOrAfter`|✓||
|verification of `SubjectConfirmationData/@InResponseTo`|✓||
|verification of `Issuer`|✓||
|verification of `Destination`|✓||
|verification of `Conditions/@NotBefore`|✓||
|verification of `Conditions/@NotOnOrAfter`|✓||
|verification of `Audience`|✓||
|parsing of Response with no `Assertion` (authentication/query failure)|✓||
|parsing of failure `StatusCode` (Requester/Responder)|✓||
|verification of `RelayState` (saml-bindings-2.0-os 3.5.3)|✓||
|**Response/Assertion parsing for SSO (1.2.1, 1.2.2.2, 1.3.1):**|||
|parsing of `NameID`|✓||
|parsing of `AuthnContextClassRef` (SPID level)|✓||
|parsing of attributes|✓||
|**Response/Assertion parsing for attribute query (2.2.2.2, 2.3.1):**|||
|parsing of attributes|✓||
|**LogoutRequest generation (for SP-initiated logout):**|||
|generation of LogoutRequest XML|✓||
|HTTP-Redirect binding|✓||
|HTTP-POST binding|✓||
|**LogoutResponse parsing (for SP-initiated logout):**|||
|parsing of LogoutResponse XML|✓||
|verification of `LogoutResponse/Signature` value (if any)|✓||
|verification of `LogoutResponse/Signature` certificate (if any) against IdP metadata|✓||
|verification of `Issuer`|✓||
|verification of `Destination`|✓||
|PartialLogout detection|||
|**LogoutRequest parsing (for third-party-initiated logout):**||
|parsing of LogoutRequest XML|✓||
|verification of `LogoutRequest/Signature` value (if any)|✓||
|verification of `LogoutRequest/Signature` certificate (if any) against IdP metadata|✓||
|verification of `Issuer`|✓||
|verification of `Destination`|✓||
|parsing of `NameID`|✓||
|**LogoutResponse generation (for third-party-initiated logout):**||
|generation of LogoutResponse XML|✓||
|HTTP-Redirect binding|✓||
|HTTP-POST binding|✓||
|PartialLogout customization|✓||
|**AttributeQuery generation (2.2.2.1):**||
|generation of AttributeQuery XML|||
|SOAP binding (client)|||

