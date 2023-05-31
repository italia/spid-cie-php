<img src="https://github.com/italia/spid-graphics/blob/master/spid-logos/spid-logo-b-lb.png" alt="SPID" data-canonical-src="https://github.com/italia/spid-graphics/blob/master/spid-logos/spid-logo-b-lb.png" width="50%" />

[![Minimum PHP Version](https://img.shields.io/badge/php-%3E%3D%208.0.28-8892BF.svg)](https://php.net/)
[![Join the #spid-php channel](https://img.shields.io/badge/Slack%20channel-%23spid--php-blue.svg?logo=slack)](https://developersitalia.slack.com/messages/CB6DCK274)
[![Get invited](https://slack.developers.italia.it/badge.svg)](https://slack.developers.italia.it/)
[![SPID on forum.italia.it](https://img.shields.io/badge/Forum-SPID-blue.svg)](https://forum.italia.it/c/spid)
![SP SPID in produzione con spid-php](https://img.shields.io/badge/SP%20SPID%20in%20produzione%20con%20spid--php-90+-green)

# spid-php
Software Development Kit for easy SPID & CIE access integration with SimpleSAMLphp.\
spid-php has been developed and is maintained by Michele D'Amico (@damikael). **It's highly recommended to use the latest release**.

<img src="doc/spid-php-tutorial.gif" alt="spid-php video tutorial" />

spid-php è uno script composer (https://getcomposer.org/) che semplifica e automatizza il processo di installazione e configurazione di SimpleSAMLphp (https://simplesamlphp.org/) per l'integrazione dell'autenticazione SPID e CIE all'interno di applicazioni PHP. spid-php permette di realizzare un Service Provider (pubblico o privato) per SPID e/o CIE in pochi secondi, ma **non è orientato alla realizzazione di Aggregatori e/o Gestori**.  

**Si raccomanda di mantenere sempre aggiornata la propria installazione all'ultima versione**.

Durante il processo di setup lo script richiede l'inserimento delle seguenti informazioni:
* directory di installazione (directory corrente)
* directory root del webserver
* nome del link al quale collegare SimpleSAMLphp
* se eseguire l'installazione per SPID e/o CIE
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
* scarica l'ultima versione dello grafica per il bottone SPID (https://github.com/italia/spid-sp-access-button)
* scarica l'ultima versione della grafica per il bottone CIE (https://github.com/italia/cie-graphics)
* crea un certificato X.509 per il service provider SPID
* crea un certificato X.509 per il service provider CIE
* scarica i metadata degli IDP SPID di produzione tramite il metadata unico di configurazione (https://registry.spid.gov.it/metadata/idp/spid-entities-idps.xml)
* configura il metadata degli IDP CIE di test e di produzione
* effettua tutte le necessarie configurazioni su SimpleSAMLphp
* predispone il template e le risorse grafiche dello SPID SP Access Button per essere utilizzate con SimpleSAMLphp
* predispone il template e le risorse grafiche CIE essere utilizzate con SimpleSAMLphp

Al termine del processo di setup si potranno scaricare i [metadata](#Metadata) oppure utilizzare i certificato X.509 creati nella directory /cert per registrare il service provider sull'ambiente di test/validazione.

Se si è scelto di copiare i file di esempio, sarà possibile verificare subito l'integrazione accedendo da web a /login.php.

Se si è scelto di copiare i file di esempio come proxy, sarà possibile verificare il funzionamento come proxy accedendo da web a /proxy-sample.php oppure /proxy-login.php

## Requisiti
* Web server
* php >= 8.0.28
* php-xml
* php-mbstring
* php-gmp
* Composer (https://getcomposer.org)
* OpenSSL >= 1.1.1
* OpenSSL aes-256-cbc Cipher Algorithm

## Installazione
Clonare, oppure salvare i file di spid-php, in una __directory non esposta sul web__ (__NON salvare i file del progetto nella root o in una sottodirectory del server web come www o public_html__). Quindi lanciare il comando di installazione dalla directory principale di spid-php.

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

## Aggiornamento Certificati
```
# composer make-certificate <days>
```
Il parametro *&lt;days&gt;* indica il numero di giorni di validità del certificato; se omesso, il valore predefinito è 730 giorni.

## Firma metadata
```
# composer sign-metadata <metadata.xml> <metadata-signed.xml>
```
Il parametro *&lt;metadata.xml&gt;* indica il nome del file xml del metadata da firmare; se omesso, il valore predefinito è metadata.xml.<br/>
Il parametro *&lt;metadata-signed.xml&gt;* indica il nome del file xml del metadata firmato; se omesso, il valore predefinito è metadata-signed.xml.

## Reinstallazione / Aggiornamento
```
# composer uninstall
# composer install
```
Se nella directory locale sono presenti i file *spid-php-setup.json* e *spid-php-openssl.cnf*, l'aggiornamento non richiederà nuovamente le informazioni di configurazione. Per modificare le informazioni di configurazione precedentemente inserite, occorrerà eseguire la disinstallazione, cancellare o modificare manualmente il file *spid-php-setup.json*, quindi procedere alla nuova installazione. Per rigenerare i certificati occorrerà eseguire la disinstallazione, rinominare o cancellare la directory /cert o i certificati *spid-sp.crt* e *spid-sp.pem* in essa presenti, quindi procedere ad una nuova installazione.

## Configurazione nginx
Per utilizzare SimpleSAMLphp su webserver nginx occorre configurare nginx come nell'esempio seguente.
In *myservice* inserire il nome del servizio come specificato durante l'installazione.

### Prerequisiti per certificati self-signed
Per fare valido l'uso del `snippets/snakeoil.conf` file su Nginx, è necessario generare il certificato "self-signed" `/etc/ssl/certs/ssl-cert-snakeoil.pem`  attraverso la installazione del package `ssl-cert` su distribuizioni Debian based (come Ubuntu), con il comando:

```bash
sudo apt-get install ssl-cert
```

O, in alternativa, generare il certificato manualmente tramite OpenSSL, con il comando:

```bash
sudo make-ssl-cert generate-default-snakeoil --force-overwrite
```

### Configurazione
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
Dopo aver completato la procedura di installazione è possibile scaricare i metadati alle seguenti url:

### Metadata SPID
**/*myservice*/module.php/saml/sp/metadata.php/spid**

dove *myservice* è il nome del servizio come specificato durante l'installazione.

### Metadata CIE
**/*myservice*/module.php/saml/sp/metadata.php/cie**

dove *myservice* è il nome del servizio come specificato durante l'installazione.

## API SDK
### Costruttore
```
new SPID_PHP()
```

### isSPIDEnabled
```
bool isSPIDEnabled()
```
restituisce true se è stata eseguita le configurazione per SPID, false altrimenti

### isAuthenticated
```
bool isCIEEnabled()
```
restituisce true se è stata eseguita le configurazione per CIE, false altrimenti

### isAuthenticated
```
bool isAuthenticated()
```
restituisce true se l'utente è autenticato, false altrimenti

### isIdPAvailable
```
bool isIdPAvailable($idp)
```
restituisce true se il valore di **$idp** è tra quelli previsti (vedi login) 

### isIdP
```
bool isIdP($idp)
```
restituisce true se l'utente è autenticato con l'idp **$idp** (vedi login)

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
void insertSPIDButton($size, [$method='GET'])
```
stampa il codice per l'inserimento del pulsante 'Entra con SPID'. 

**$size** specifica la dimensione del pulsante (S|M|L|XL)

**$method** specifica la versione del pulsante (GET|POST)

### insertCIEButton
```
void insertCIEButton([$size='default'])
```
stampa il codice per l'inserimento del pulsante 'Entra con CIE'. 

**$size** specifica la dimensione del pulsante (default)

### setPurpose (solo per SPID)
```
void setPurpose($purpose)
```
imposta l'estensione "Purpose" nell'AuthenticationRequest per inviare una richiesta di autenticazione per identità digitale ad uso professionale ([Avviso SPID n.18 v.2](https://www.agid.gov.it/sites/default/files/repository_files/spid-avviso-n18_v.2-_autenticazione_persona_giuridica_o_uso_professionale_per_la_persona_giuridica.pdf)).
**$purpose** specifica il valore dell'estensione Purpose (P|LP|PG|PF|PX)

### login
```
void login($idp, $level, [$returnTo], [$attributeConsumingServiceIndex], [$post])
```
invia una richiesta di login livello $level verso l'idp **$idp**. Dopo l'autenticazione, l'utente è reindirizzato alla url eventualmente specificata in **$returnTo**. Se il parametro **$returnTo** non è specificato, l'utente è reindirizzato alla pagina di provenienza.

**$idp** può assumere uno dei seguenti valori:
* VALIDATOR
* DEMO
* DEMOVALIDATOR
* ArubaPEC S.p.A.
* EtnaHitech S.C.p.A.
* InfoCert S.p.A.
* IN.TE.S.A. S.p.A.
* Lepida S.p.A.
* Namirial
* Poste Italiane SpA
* Register.it S.p.A.
* Sielte S.p.A.
* TeamSystem s.p.a.
* TI Trust Technologies srl
* CIE TEST
* CIE

**$level** può assumere uno dei seguenti valori
* 1
* 2
* 3

**$post** può assumere valore 
* false
* true. 

Se **$post** è true specifica che la AuthnRequest deve essere inviata in Binding HTTP-Post invece che HTTP-Request (default) 

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
esegue la disconnessione. Dopo la disconnessione, l'utente è reindirizzato alla url specificata in **$returnTo** oppure alla pagina di provenienza se **$returnTo** non è specificato.

### getLogoutURL
```
string getLogoutURL([$returnTo])
```
restituisce la url per eseguire la disconnessione. Dopo la disconnessione, l'utente è reindirizzato alla url specificata in **$returnTo** oppure alla pagina di provenienza se **$returnTo** non è specificato.


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
GET /proxy.php?action=login&client_id=<client_id>&redirect_uri=<redirect_uri>&idp=<idp>&state=<state>
```
Invia la AuthnRequest ad uno specifico IdP e ritorna la Response decodificata o come JWS o JWS(JWE) in POST alla redirect_uri del client.

Parametri:

 - *client_id* è l'identificativo corrispondente al client
 - *redirect_uri* è la URL del client alla quale ritornare la risposta. Deve corrispondere ad una delle URL registrate per il client
 - *idp* è il valore che identifica l'IdP con il quale eseguire l'autenticazione (vedi API SDK login)
 - *state* è il valore del RelayState

### logout
```
GET /proxy.php?action=logout&client_id=<client_id>
```
Esegue la disconnessione dall'IdP.
  
Parametri:
  
 - *client_id* è l'identificativo corrispondente al client. Dopo aver eseguito la disconnessione presso l'IdP, il flusso viene rediretto al primo redirect_uri registrato per il client

### verify
```
GET /proxy.php?action=verify&token=<token>&decrypt=<decrypt>&secret=<secret>
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
<a href="/proxy-login.php">Go to Login Page or...</a><br/>
<a href="/proxy.php?client_id=<client_id>&action=login&redirect_uri=/proxy-sample.php&idp=DEMOVALIDATOR&state=state">Login with a single IdP (example for DEMO Validator)</a>
<p>
    <?php
        foreach($_POST as $attribute=>$value) {
            echo "<p>" . $attribute . ": <b>" . $value . "</b></p>";
        }
    ?>
</p>
<a href="/proxy-spid.php?client_id=61504487f292e&action=logout">Esci</a>

```
## Gestione degli errori SPID

Durante il processo di autenticazione SPID posso verificarsi degli errori che 
vengono generati dagli IDP, determinati dall'interazione dell'utente con la form di login oppure da formati delle richieste che non rispettano le regole tecniche di SPID.

La tabella con i messaggi di errore è disponible [qui](https://docs.italia.it/italia/spid/spid-regole-tecniche/it/stabile/messaggi-errore.html)

Lo script ```/error.php``` viene richiamato alla ricezione di un messaggio 
di Response che contiene i dettagli dell'errore.


## Author
[Michele D'Amico (damikael)](https://it.linkedin.com/in/damikael)
 
## Credits
<a href="https://www.linfaservice.it" target="_blank"><img  style="border:1px solid #ccc; padding:5px; margin-right:20px; vertical-align:middle; width: 200px;" src="https://www.linfaservice.it/common/linfaservice.png" alt="Linfa Service"></a>
<a href="https://www.manydesigns.com" target="_blank"><img  style="border:1px solid #ccc; padding:5px; margin-right:20px; vertical-align:middle; width: 200px;" src="https://manydesigns.com/wp-content/uploads/2022/12/Logo_Manydesigns.png" alt="ManyDesigns"></a>
  
## Contributors
<a href = "https://github.com/italia/spid-php/contributors">
  <img src = "https://contrib.rocks/image?repo=italia/spid-php"/>
</a>

## SPID Compliance

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

