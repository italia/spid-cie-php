<!--
    PROXY SAMPLE
    This is only a sample that show how to call proxy to initiate login with an IdP.
    This page can be also the redirect page when we utilize the proxy.
    The login can be performed by a central login page or by calling directly proxy for login with an IdP.
    In production environment you con also implement here the spid access button markup.

    URL to call directly proxy for login with a IdP is formed as follow:
    /proxy-login.php?client_id=<client_id>&action=login&redirect_uri=<redirect_uri>&idp=<idp>&state=<state>

    URL to call proxy logout is formed as follow
    /proxy-logout.php?client_id=<client_id>&action=logout

    <client_id> and <redirect_uri> here is configured during setup
-->

<html>
    <body>
        <a href="/proxy-login-spid.php">Go to Login Page or...</a><br/>
        <a href="/proxy-spid.php?client_id={{PROXY_CLIENT_ID}}&action=login&redirect_uri={{PROXY_REDIRECT_URI}}&idp=DEMOVALIDATOR&state=state">Login with a single IdP (example for DEMO Validator)</a>
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