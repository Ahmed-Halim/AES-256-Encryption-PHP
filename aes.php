<html>
  <head>
    <style>
    body {
      margin: 50px;
    }
    h1 {
      text-align: center;
      padding-top: 20px;
    }
    select,input,textarea,button {
        display: block;
        margin: 20px auto;
        width: 100%;
        background: #f1f1f1;
        border: none;
        border-radius: 10px;
        padding: 20px;
        outline: none;
        -webkit-box-shadow: none;
        -moz-box-shadow: none;
        box-shadow: none;
        resize: none;
    }

    @media screen and (min-width: 767px) {
      .grid {
          grid-template-columns: repeat(2, 1fr);
          grid-gap: 50px;
          display: grid;
      }
    }
    .yellow {
        background: #fff9ec;
        padding: 20px 30px;
        font-size: 14px;
        white-space: pre-line;
        border-radius: 16px;
    }
    #encrypt-btn {
        background: #cc0000;
        color: #fff;
        font-size: 11px;
        letter-spacing: 3px;
        text-transform: uppercase;
        margin-bottom: 30px;
    }
    #decrypt-btn {
        background: #4CAF50;
        color: #fff;
        font-size: 11px;
        letter-spacing: 3px;
        text-transform: uppercase;
        margin-bottom: 30px;
    }
    </style>
  </head>
  <body>
    <h1>AES 256 CBC Encryption</h1>
    <div class="grid">
      <form method="post" action="">
        <input type="hidden" name="type" value="Encrypt">
        <textarea rows="10" name="text" placeholder="Text to be encrypted .."><?php if (isset($_POST["type"]) && $_POST["type"] == "Encrypt" && isset($_POST["text"])) echo $_POST["text"]; ?></textarea>
        <input required name="password" placeholder="Key" value="<?php if (isset($_POST["type"]) && $_POST["type"] == "Encrypt" && isset($_POST["password"]))  echo $_POST["password"]; ?>">
        <button id="encrypt-btn">Encrypt</button>
      </form>
      <form method="post" action="">
        <input type="hidden" name="type" value="Decrypt">
        <textarea rows="10" name="text" placeholder="Cipher text to be decrypted .."><?php if (isset($_POST["type"]) && $_POST["type"] == "Decrypt" && isset($_POST["text"])) echo $_POST["text"]; ?></textarea>
        <input required name="password" placeholder="Key" value="<?php if (isset($_POST["type"]) && $_POST["type"] == "Decrypt" && isset($_POST["password"])) echo $_POST["password"]; ?>">
        <button id="decrypt-btn">Decrypt</button>
      </form>
    </div>
  </body>
</html>

<?php

function encrypt($plaintext, $password) {
    $key = hash('sha256', $password, true);
    $iv = openssl_random_pseudo_bytes(16);
    $ciphertext = openssl_encrypt($plaintext, "AES-256-CBC", $key, OPENSSL_RAW_DATA, $iv);
    $hash = hash_hmac('sha256', $ciphertext . $iv, $key, true);
    return base64_encode($iv . $hash . $ciphertext);
}

function decrypt($ivHashCiphertext, $password) {
    $ivHashCiphertext = base64_decode($ivHashCiphertext);
    $iv = substr($ivHashCiphertext, 0, 16);
    $hash = substr($ivHashCiphertext, 16, 32);
    $ciphertext = substr($ivHashCiphertext, 48);
    $key = hash('sha256', $password, true);
    if (!hash_equals(hash_hmac('sha256', $ciphertext . $iv, $key, true), $hash)) return null;
    return openssl_decrypt($ciphertext, "AES-256-CBC", $key, OPENSSL_RAW_DATA, $iv);
}

if (!empty($_POST)) {
  if ($_POST["type"] == "Encrypt") {
    $encrypted = encrypt($_POST["text"], $_POST["password"]);
    if ($encrypted == "") $encrypted = "Error";
    echo '<div class="yellow">'.$encrypted.'</div>';
  }
  if ($_POST["type"] == "Decrypt") {
    $decrypted = decrypt($_POST["text"], $_POST["password"]);
    if ($decrypted == "") $decrypted = "Error";
    echo '<div class="yellow">'.$decrypted.'</div>';
  }
}

?>
