<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
    <title>POST data</title>
    <script src="/<?php echo $this->data['baseurlpath']; ?>resources/post.js"></script>
    <link type="text/css" rel="stylesheet" href="/<?php echo $this->data['baseurlpath']; ?>resources/post.css" />
</head>
<body>

    <noscript>
        <p><strong>Note:</strong> 
        Since your browser does not support JavaScript, 
        you must press the button below once to proceed.</p> 
    </noscript> 

    <form method="post" action="/error.php">
        <input type="hidden" name="statusCode" value="<?php echo htmlspecialchars($this->data['error']['statusCode']); ?>" />
        <input type="hidden" name="statusMessage" value="<?php echo htmlspecialchars($this->data['error']['statusMessage']); ?>" />
        <input type="hidden" name="errorMessage" value="<?php echo htmlspecialchars($this->data['errorMessage']); ?>" />
        <input type="submit" id="postLoginSubmitButton">
        <noscript>
            <button type="submit" class="btn">Submit</button>
        </noscript>
    </form>
</body>
</html>