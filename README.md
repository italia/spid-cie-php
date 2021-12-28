<img src="https://github.com/italia/spid-graphics/blob/master/spid-logos/spid-logo-b-lb.png" alt="SPID" data-canonical-src="https://github.com/italia/spid-graphics/blob/master/spid-logos/spid-logo-b-lb.png" width="50%" />

[![Minimum PHP Version](https://img.shields.io/badge/php-%3E%3D%207.2.5-8892BF.svg)](https://php.net/)
[![Join the #spid-php channel](https://img.shields.io/badge/Slack%20channel-%23spid--php-blue.svg?logo=slack)](https://developersitalia.slack.com/messages/CB6DCK274)
[![Get invited](https://slack.developers.italia.it/badge.svg)](https://slack.developers.italia.it/)
[![SPID on forum.italia.it](https://img.shields.io/badge/Forum-SPID-blue.svg)](https://forum.italia.it/c/spid)
![SP SPID in produzione con spid-php](https://img.shields.io/badge/SP%20SPID%20in%20produzione%20con%20spid--php-36+-green)

# spid-php
Software Development Kit for easy SPID access integration with SimpleSAMLphp.

spid-php has been developed and is maintained by Michele D'Amico (@damikael). **It's highly recommended to use the latest release**.


spid-php è uno script composer (https://getcomposer.org/) che semplifica e automatizza il processo di installazione e configurazione di SimpleSAMLphp (https://simplesamlphp.org/) per l'integrazione dell'autenticazione SPID all'interno di applicazioni PHP tramite lo SPID SP Access Button (https://github.com/italia/spid-sp-access-button). spid-php permette di realizzare un Service Provider (pubblico o privato) per SPID in pochi secondi, ma **non è orientato alla realizzazione di Aggregatori e/o Gestori**.  

**Si raccomanda di mantenere sempre aggiornata la propria installazione all'ultima versione**.

Durante il processo di setup lo script richiede l'inserimento delle seguenti informazioni:
* directory di installazione (directory corrente)
* directory root del webserver
* nome del link al quale collegare SimpleSAMLphp
* EntityID del service provider
* Tipologia del service provider (pubblico o privato)
* Informazioni del service provider da inserire nel metadata
* Informazioni del service provider da inserire nel certificato di firma in accordo con quanto previsto dall'[Avviso SPID n°29 v3](https://www.agid.gov.it/sites/default/files/repository_files/spid-avviso-n29v3-specifiche_sp_pubblici_e_privati.pdf) 
* AttributeConsumingServiceIndex da richiedere all'IDP
* Attributi richiesti da inserire nel metadata
* se inserire nella configurazione i dati dell'IDP SPID Demo (https://demo.spid.gov.it)
* se inserire nella configurazione i dati dell'IDP SPID Demo in modalità Validator (https://demo.spid.gov.it/validator)
* se inserire nella configurazione i dati dell'IDP per la validazione di AgID (https://validator.spid.gov.it)
* se copiare nella root del webserver i file di esempio per l'integrazione del bottone
* se copiare nella root del webserver i file di esempio per l'utilizzo come proxy
* le informazioni necessarie per configurare un client di esempio per il proxy
* i dati per la generazione del certificato X.509 per il service provider

e si occupa di eseguire i seguenti passi:
* scarica l'ultima versione di SimpleSAMLphp con le relative dipendenze
* scarica l'ultima versione dello spid-sp-access-button
* crea un certificato X.509 per il service provider
* scarica i metadata degli IDP di produzione tramite il metadata unico di configurazione (https://registry.spid.gov.it/metadata/idp/spid-entities-idps.xml)
* effettua tutte le necessarie configurazioni su SimpleSAMLphp
* predispone il template e le risorse grafiche dello SPID SP Access Button per essere utilizzate con SimpleSAMLphp

Al termine del processo di setup si potrà scaricare il [metadata](#Metadata) oppure utilizzare il certificato X.509 creato nella directory /cert per registrare il service provider sull'ambiente di test/validazione.

Se si è scelto di copiare i file di esempio, sarà possibile verificare subito l'integrazione accedendo da web a /login-spid.php.

Se si è scelto di copiare i file di esempio come proxy, sarà possibile verificare il funzionamento come proxy accedendo da web a /proxy-sample.php oppure /proxy-login-spid.php

## Requisiti
* Web server
* php >= 7.2.5 < 8.0
* php-xml
* php-mbstring
* Composer (https://getcomposer.org)
* OpenSSL >= 1.1.1
* OpenSSL aes-256-cbc Cipher Algorithm

## Installazione
```
# composer install
```
Al termine dell'installazione tutte le configurazioni sono salvate nei file *spid-php-setup.json*, *spid-php-openssl.cnf* e *spid-php-proxy.json*. In caso di reinstallazione, le informazioni di configurazione saranno recuperate automaticamente da tali file, senza la necessità di doverle reinserire nuovamente.  

## Disinstallazione
```
# composer uninstall
```
La disinstallazione non cancella gli eventuali file *spid-php-setup.json*, *spid-php-openssl.cnf* e *spid-php-proxy.json* locali che contengono le configurazioni inserite durante il processo in installazione.

## Aggiornamento Metadata IdP
```
# composer update-metadata
```

## Reinstallazione / Aggiornamento
```
# composer uninstall
# composer install
```
Se nella directory locale sono presenti i file *spid-php-setup.json* e *spid-php-openssl.cnf*, l'aggiornamento non richiederà nuovamente le informazioni di configurazione. Per modificare le informazioni di configurazione precedentemente inserite, occorrerà eseguire la disinstallazione, cancellare o modificare manualmente il file *spid-php-setup.json*, quindi procedere alla nuova installazione. Per rigenerare i certificati occorrerà eseguire la disinstallazione, rinominare o cancellare la directory /cert o i certificati *spid-sp.crt* e *spid-sp.pem* in essa presenti, quindi procedere ad una nuova installazione.

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

## API SDK
### Costruttore
```
new SPID_PHP()
```

### isAuthenticated
```
bool isAuthenticated()
```
restituisce true se l'utente è autenticato, false altrimenti

### isIdPAvailable
```
bool isIdPAvailable($idp)
```
restituisce true se il valore di $idp è tra quelli previsti (vedi login) 

### isIdP
```
bool isIdP($idp)
```
restituisce true se l'utente è autenticato con l'idp $idp (vedi login)

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

### setPurpose
```
void setPurpose(string $purpose)
```
imposta l'estensione "Purpose" nell'AuthenticationRequest per inviare una richiesta di autenticazione per identità digitale ad uso professionale ([Avviso SPID n.18 v.2](https://www.agid.gov.it/sites/default/files/repository_files/spid-avviso-n18_v.2-_autenticazione_persona_giuridica_o_uso_professionale_per_la_persona_giuridica.pdf)).
$purpose specifica il valore dell'estensione Purpose (P|LP|PG|PF|PX)

### login
```
void login($idp, $level, [$returnTo], [$attributeConsumingServiceIndex])
```
invia una richiesta di login livello $level verso l'idp $idp. Dopo l'autenticazione, l'utente è reindirizzato alla url eventualmente specificata in $returnTo. Se il parametro $returnTo non è specificato, l'utente è reindirizzato alla pagina di provenienza.

$idp può assumere uno dei seguenti valori:
* VALIDATOR
* DEMO
* DEMOVALIDATOR
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

### getResponseID
```
string getResponseID()
```
restituisce l'attributo ID della SAML Response ricevuta dall'IdP

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

## Utilizzo come Proxy
Durante l'installazione è possibile scegliere se installare i file di esempio per l'utilizzo di spid-php come proxy. 

In tal caso viene richiesto:
- URL di redirect alla quale inviare i dati dell'utente al termine dell'autenticazione
- se firmare la response
- se cifrare la response

Se si seleziona Y alla domanda se firmare la response, i dati dell'utente saranno inviati alla URL di redirect in POST contenuti in un token in formato JWS firmato con la chiave privata del certificato del Service Provider generato durante l'installazione e salvato in */cert*.

Se non si seleziona Y alla domanda se firmare la response, i dati dell'utente saranno inviati, invece, alla URL di redirect in POST come variabili in chiaro.

Se oltre alla firma, si seleziona Y anche alla domanda se cifrare la response, questa sarà inviata alla URL di redirect in POST come token in formato JWS il cui payload contiene nell'attributo *data* un token JWE cifrato con un *client_secret* contenente i dati dell'utente.

*client_id* e *client_secret* sono generati automaticamente per il client durante il setup, mostrati a video e salvati nel file *spid-php-proxy.json*.


Per inserire ulteriori client con le relative configurazioni di *client_id*, *client_secret* e *redirect_uri*, è possibile editare il file *spid-php-proxy*.json 

## API Proxy
### login
```
GET /proxy-spid.php?action=login&client_id=<client_id>&redirect_uri=<redirect_uri>&idp=<idp>&state=<state>
```
Invia la AuthnRequest ad uno specifico IdP e ritorna la Response decodificata o come JWS o JWS(JWE) in POST alla redirect_uri del client.

Parametri:

 - *client_id* è l'identificativo corrispondente al client
 - *redirect_uri* è la URL del client alla quale ritornare la risposta. Deve corrispondere ad una delle URL registrate per il client
 - *idp* è il valore che identifica l'IdP con il quale eseguire l'autenticazione (vedi API SDK login)
 - *state* è il valore del RelayState

### logout
```
GET /proxy-spid.php?action=logout&client_id=<client_id>
```
Esegue la disconnessione dall'IdP.
  
Parametri:
  
 - *client_id* è l'identificativo corrispondente al client. Dopo aver eseguito la disconnessione presso l'IdP, il flusso viene rediretto al primo redirect_uri registrato per il client

### verify
```
GET /proxy-spid.php?action=verify&token=<token>&decrypt=<decrypt>&secret=<secret>
```
Verifica e decifra il token JWT ricevuto con la response.
  
Parametri:
  
 - *token* è il token JWT ricevuto con la response, nel caso in cui si sia scelto di firmare la response
 - *decrypt* può assumere valore Y o N. Permette di decifrare la response nel caso in cui si sia scelto di cifrare la response
 - *secret* è il client_secret consegnato al client con cui è possibile decifrare la response
  
Nel caso in cui non è possibile verificare la firma oppure non è possibile decifrare la response (perchè il secret non è corretto oppure perchè il token è malformato) viene restituito status code 422.
  
Si consiglia di utilizzare l'endpoint verify esclusivamente per la verifica della firma dei messaggi. Per decifrare i dati dell'utente, invece, si consiglia di implementare la decodifica sul client.

## Esempio di utilizzo del proxy
```
<a href="/proxy-login-spid.php">Go to Login Page or...</a><br/>
<a href="/proxy-spid.php?client_id=<client_id>&action=login&redirect_uri=/proxy-sample.php&idp=DEMOVALIDATOR&state=state">Login with a single IdP (example for DEMO Validator)</a>
<p>
    <?php
        foreach($_POST as $attribute=>$value) {
            echo "<p>" . $attribute . ": <b>" . $value . "</b></p>";
        }
    ?>
</p>
<a href="/proxy-spid.php?client_id=61504487f292e&action=logout">Esci</a>

```
  
## Author
[Michele D'Amico (damikael)](https://it.linkedin.com/in/damikael)
 
## Credits
<a href="https://www.linfaservice.it" target="_blank"><img  style="border:1px solid #ccc; padding: 5px; height: 50px; margin-right: 20px;" src="https://www.linfaservice.it/common/linfaservice.png" alt="Linfa Service"></a>
<a href="https://www.manydesigns.com" target="_blank"><img  style="border:1px solid #ccc; padding: 5px; height: 50px; margin-right: 20px;" src="https://www.manydesigns.com/img/logoMD.png" alt="ManyDesigns"></a>
  
## Contributors
<a href = "https://github.com/italia/spid-php/contributors">
  <img src = "https://contrib.rocks/image?repo=italia/spid-php"/>
</a>

## Compliance

|<img src="https://github.com/italia/spid-graphics/blob/master/spid-logos/spid-logo-c-lb.png?raw=true" width="100" /><br />_Compliance with [SPID regulations](http://www.agid.gov.it/sites/default/files/circolari/spid-regole_tecniche_v1.pdf) (for Service Providers)_|status|notes|
|:---|:---|:---|
|**SPID Avviso n.18 v.2:**|✓||
|**SPID Avviso n.29 v.3:**|✓||
|generation of certificates|✓||
|generation of metadata|✓||
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

