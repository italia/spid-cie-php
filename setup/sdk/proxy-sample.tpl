<!-- 
    PROXY SAMPLE
    This is only a sample that show how to call proxy to initiate login with an IdP.
    In production environment you have to implement spid access button markup

    URL to call proxy login is formed as follow:
    /proxy-login.php?client_id=<client_id>&action=login&redirect_uri=<redirect_uri>&idp=<idp>&state=<state>

    URL to call proxy logout is formed as follow
    /proxy-logout.php?client_id=<client_id>&action=logout

    <client_id> and <redirect_uri> are configured during setup
-->

<html>
    <body>
        <a href="/proxy-spid.php?client_id={{PROXY_CLIENT_ID}}&action=login&redirect_uri={{PROXY_REDIRECT_URI}}&idp=VALIDATOR&state=state">Accedi con SPID Validator</a>
        <p>
            <?php
                foreach($_POST as $attribute=>$value) {
                    echo "<p>" . $attribute . ": <b>" . $value . "</b></p>";
                }
            ?>
        </p>
        <a href="/proxy-spid.php?client_id={{PROXY_CLIENT_ID}}&action=logout">Esci</a>
    </body>
</html>