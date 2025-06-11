<?php 

    require_once("{{SDKHOME}}/proxy-spid-php.php");

    const PROXY_CONFIG_FILE = "{{SDKHOME}}/spid-php-proxy.json";
    const DEBUG = false;
    const ERR_REDIRECT = "/metadata.xml";

    if(DEBUG) {
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);
    }

    $proxy_config = file_exists(PROXY_CONFIG_FILE)? json_decode(file_get_contents(PROXY_CONFIG_FILE), true) : array();
    // always set to production to avoid test/validator button to be active while testing
    $production = $proxy_config['production'];
    $clients = $proxy_config['clients'];

    $client_id = isset($_GET['client_id'])? $_GET['client_id'] : null;
    $level = (isset($_GET['level']) && $_GET['level'])? $_GET['level'] : 2;
    $redirect_uri = isset($_GET['redirect_uri'])? urldecode($_GET['redirect_uri']) : null;
    $state = (isset($_GET['state']) && $_GET['state'])? $_GET['state'] : '';
    $idp = isset($_GET['idp'])? $_GET['idp'] : null;

    if($client_id==null || $client_id=='') { 
        http_response_code(404); 
        if(DEBUG) {
            echo "client_id not provided"; 
        } else {
            header("Location: " . ERR_REDIRECT);
        }
        die(); 
    }

    if($level==null || $level=='') { 
        http_response_code(404); 
        if(DEBUG) {
            echo "level not provided";
        } else {
            header("Location: " . ERR_REDIRECT);
        }
        die(); 
    }

    if($redirect_uri==null || $redirect_uri=='') { 
        http_response_code(404); 
        if(DEBUG) {
            echo "redirect_uri not provided";
        } else {
            header("Location: " . ERR_REDIRECT);
        }
        die(); 
    }
    
    if(!in_array($client_id, array_keys($clients))) { 
        http_response_code(404); 
        if(DEBUG) {
            echo "client_id not found"; 
        } else {
            header("Location: " . ERR_REDIRECT);
        }
        die(); 
    }

    if(!in_array($redirect_uri, $clients[$client_id]['redirect_uri'])) { 
        http_response_code(404); 
        if(DEBUG) {
            echo "redirect_uri not found"; 
        } else {
            header("Location: " . ERR_REDIRECT);
        }
        die(); 
    }
    

    $service = "service";
    if($idp=="CIE" || $idp=="CIE TEST") $service = "cie";
    $spidsdk = new PROXY_SPID_PHP($client_id, $redirect_uri, $state, $production, $service);

    //$spidsdk->setPurpose("P");

    $organization_name = isset($clients[$client_id]['description'])? $clients[$client_id]['description'] : '';

    if(!$spidsdk->isAuthenticated()) {
        if($idp==null || $idp=='') {    

?>
            <!DOCTYPE html>
            <html lang="it">
                <head>
                    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
                    <meta http-equiv="X-UA-Compatible" content="IE=Edge" />
                    <meta name="viewport" content="width=device-width,initial-scale=1,shrink-to-fit=no" />
                    <link href="/assets/css/style.css" rel="stylesheet" />
                    <link href="/assets/css/custom.css?v=2.1" rel="stylesheet" />
                    <link href="/assets/css/eidas-sp-access-button.min.css" rel="stylesheet" />
                    <link rel="preconnect" href="https://fonts.googleapis.com"> 
                    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
                    <link href="https://fonts.googleapis.com/css2?family=Titillium+Web:wght@200;300;400;600;700;900&display=swap" rel="stylesheet">
                    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta2/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
                    <?php $spidsdk->insertSPIDButtonCSS(); ?>
                </head>
                <body>
                    <div id="root">
                        <header aria-label="Intestazione">
                            <div class="bg-transparent my-header" id="page-header">
                                <div class="row align-items-sm-center">
                                    <div class="col-auto pr-0 pr-md-2">
                                        <img src="<?php echo $proxy_config['clients'][$client_id]['logo']; ?>" alt="Logo" class="logo my-2">
                                    </div>
                                    <div class="col">
                                        <h1>
                                            <?php echo $proxy_config['clients'][$client_id]['name']; ?>
                                        </h1>
                                        
                                    </div>
                                </div>
                            </div>
                        </header>
                        <div id="login" class="container-fluid d-flex flex-column justify-content-between py-3 py-md-4">
                            <div id="loginPage" class="d-flex flex-column justify-content-between">
                                <main id="main" class="mb-5">
                                    <h1 class="align-center"><?php echo $organization_name; ?></h1>
                                    <div id="login-form" class="login-form-lg shadow mx-auto mt-3">
                                        <h2 class="h3">Accedi con identità digitale</h2>
                                        <ul class="nav nav-tabs flex-sm-row flex-sm-nowrap" role="tablist">
                                            <li class="nav-item text-sm-center" role="presentation">
                                                <a href="#tab-spid" class="nav-link h-100 px-4 active" data-bs-toggle="tab" aria-controls="tab-spid" role="tab">
                                                    <i class="fas fa-user-circle mr-2"></i>SPID
                                                </a>
                                            </li>
                                            <li class="nav-item text-sm-center" role="presentation">
                                                <a href="#tab-cie" class="nav-link h-100 px-4" data-bs-toggle="tab" aria-controls="tab-cie" role="tab">
                                                    <i class="fas fa-address-card mr-2"></i>CIE
                                                </a>
                                            </li>
                                        </ul>
                                        <div class="tab-content">
                                            <div id="tab-spid" class="tab-pane fade active show" role="tabpanel" aria-labelledby="tab-spid">
                                                <h3 class="sr-only">Accedi con identità digitale credenziali SPID</h3>
                                                <p>SPID, il&nbsp;<strong>Sistema Pubblico di Identità Digitale</strong>&nbsp;è il sistema di accesso che consente di utilizzare, con un'identità digitale unica, i servizi online della Pubblica Amministrazione e dei privati accreditati. Se sei già in possesso di un'identità digitale, accedi con le credenziali del tuo gestore. Se non hai ancora un'identità digitale, richiedila ad uno dei gestori.</p>

                                                <div class="row align-items-center mt-3">
                                                    <div class="col-12 col-md-6">
                                                        <ul class="list-link px-1">
                                                            <li class="mb-1">
                                                                <a href="https://www.spid.gov.it/" target="_blank" rel="noopener noreferrer">
                                                                    <span class="sr-only">Apre una nuova finestra</span>Maggiori informazioni su SPID</a>
                                                            </li>
                                                            <li class="mb-1">
                                                                <a href="https://www.spid.gov.it/richiedi-spid" target="_blank" rel="noopener noreferrer">
                                                                    <span class="sr-only">Apre una nuova finestra</span>Non hai SPID?</a>
                                                            </li>
                                                            <li class="mb-1">
                                                                <a href="https://www.spid.gov.it/serve-aiuto" target="_blank" rel="noopener noreferrer">
                                                                    <span class="sr-only">Apre una nuova finestra</span>Serve aiuto?</a>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                    <div class="col-12 col-md-6 text-center">
                                                        <?php $spidsdk->insertSPIDButton("M"); ?>
                                                    </div>
                                                </div>

                                                <div class="row align-items-center mt-3">
                                                    <img id="spid-agid" class="img-fluid mx-auto" src="/assets/img/spid-agid-logo-lb.png" alt="Logo SPID - AGID - Agenzia per l'Italia Digitale">
                                                </div>
                                            </div>

                                            <div id="tab-cie" class="tab-pane fade" role="tabpanel" aria-labelledby="tab-cie">
                                                <h3 class="sr-only">Accedi con identità digitale CIE</h3>
                                                <p>
                                                    La&nbsp;<strong>Carta di Identità Elettronica (CIE)</strong>&nbsp;è il documento personale che attesta l'identità del cittadino.&nbsp;Dotata di microprocessore, oltre a comprovare l'identità personale, permette l'accesso ai servizi digitali della Pubblica Amministrazione.
                                                </p>
                                                <!--p>
                                                    <strong>L'autenticazione con CIE è attualmente in manutenzione.</strong>
                                                </p-->
                                                <div class="row align-items-center">
                                                    <div class="col-12 col-md-6">
                                                        <a class="my-3" href="https://www.cartaidentita.interno.gov.it/" target="_blank" rel="noopener noreferrer">
                                                            <span class="sr-only">Apre una nuova finestra</span>Maggiori informazioni
                                                        </a>
                                                    </div>
                                                    <div class="col-12 col-md-6 text-center">
                                                        <a id="btn-accedi" type="submit" class="btn p-0 border-0 my-3"
                                                            href="/proxy.php?action=login&client_id=<?php echo $client_id; ?>&redirect_uri=<?php echo $redirect_uri; ?>&idp=CIE&state=<?php echo $state; ?>">
                                                            <img class="img-fluid" src="/assets/img/button_cie.png" alt="">
                                                            <span class="sr-only">Accedi con identità digitale CIE</span>
                                                        </a>
                                                    </div>
                                                </div>
                                                <img id="ministero-interno" class="img-fluid mx-auto" src="/assets/img/logo_mi.png" alt="Logo del Ministero dell’Interno">
                                            </div>

                                        </div>
                                    </div>
                                </main>
                            </div>
                        </div>
                        <footer id="page-footer">
                            <div class="container-fluid pb-3">
                                <hr aria-hidden="true" />
                                <ul class="list-inline mb-0 w-100">
                                    <li class="list-inline-item">
                                        <a href="#">Privacy</a>
                                    </li>
                                    <li class="list-inline-item">
                                        <a href="#">Note legali</a>
                                    </li>
                                </ul>
                            </div>
                        </footer>
                    </div>

                    <?php $spidsdk->insertSPIDButtonJS(); ?>
                    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js"></script>
                </body>
            </html>

<?php
        } else {
            /***
             * questo branch non viene raggiunto perchè utilizzando 
             * $spidsdk->insertSPIDButton per simplicità
             * il login viene effettuato da proxy.php 
             * con i valori di spid_level e atcs_index predefiniti
             * in spid-php-proxy.json
             * Quindi ogni applicativo (compreso OIDC Plugin)
             * non può gestirli a runtime
             **/

            if($spidsdk->isIdPAvailable($idp)) {
                $spidsdk->login($idp, $level, "", 0); 
            } else {
                if(DEBUG) {
                    echo "idp not valid";
                } else {
                    header("Location: " . ERR_REDIRECT);
                }
            }

            // set AttributeConsumingServiceIndex 2
            //$spidsdk->login($idp, 2, "", 2);
        }

    } else {
        $idp = $spidsdk->getIdPKey();
        $proxy_url = "/proxy.php?action=login&client_id=".$client_id.
                        "&redirect_uri=".$redirect_uri.
                        "&state=".$state.
                        "&idp=".$idp;

        header("Location: " . $proxy_url);
    }
?>