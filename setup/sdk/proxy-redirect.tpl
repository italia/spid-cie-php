<!--
    PROXY SAMPLE
    This is only a sample of redirect page when we utilize proxy.
-->

<html>
<body>
<a href="/proxy-login-spid.php">Login</a>
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