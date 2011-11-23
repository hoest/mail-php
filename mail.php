<?php

////////////////////////////////////////////////////////////////////////////////
// Instellingen
// De variabelen hieronder kunnen naar wens aangepast worden
////////////////////////////////////////////////////////////////////////////////

// URL naar het bestelformulier
$formurl = "../pages/form.html";

// URL naar de 'bedankt'-pagina
$thanksurl = "../pages/thanks.html";

// Mailadres waarnaartoe het bericht verstuurd moet worden
$emailaddress = "email@address.com";

////////////////////////////////////////////////////////////////////////////////
// Hieronder is de werkelijke programmatuur; wees erg voorzichtig met aanpassen
// van deze functionaliteit, tenzij je verstand van zaken hebt.
////////////////////////////////////////////////////////////////////////////////

// uniek nummer -> bestelnummer
$uid = strtoupper(uniqid(""));

// controleer of het verplichte veld is ingevuld
function isvalid($fieldname) {
  return (isset($_POST[$fieldname]) && $_POST[$fieldname] != "");
}

// verstuur de bestelling-mail
function sendmail($to) {
  global $uid;
  $subject = "Bestelling: " . $uid;

  $body  = "<html><body>";
  $body .= "<h1>Bestelling " . $uid . "</h1>";
  $body .= "<p>Hieronder vindt u het ingevulde bestelformulier:</p>";

  foreach ($_POST as $key => $value) {
    $body .= "<p><strong>" . $key . ":</strong><br />" . nl2br(htmlentities($value)) . "</p>";
  }

  $body .= "</body></html>";

  return mail($to, $subject, $body, getheaders($_POST["Naam"], $_POST["E-mail"]));
}

// verstuur het 'bedankt'-mailtje
function sendthanks($to) {
  global $emailaddress, $uid;
  $subject = "Bedankt voor uw bestelling";

  $body  = "<html><body>";
  $body .= "<h1>Bestelling verzonden</h1>";
  $body .= "<p>Uw bestelling (nr. " . $uid . ") is verzonden naar " . $emailaddress . ", waarvoor dank.</p>";
  $body .= "<p>M.v.g.,</p><p>Afzender<br />" . $emailaddress . "</p>";

  return mail($to, $subject, $body, getheaders("Mediterranee Food", $emailaddress));
}

// standaard-headers voor de mail
function getheaders($name, $from) {
  $headers  = "From: \"" . strip_tags($name) . "\" <" . strip_tags($from) . ">\r\n";
  $headers .= "Reply-To: " . strip_tags($from) . "\r\n";
  $headers .= "MIME-Version: 1.0\r\n";
  $headers .= "Content-Type: text/html; charset=utf-8\r\n";

  return $headers;
}

// redirect de pagina naar de opgegeven URL
function redirect($url) {
  header("Location: " . $url);
}

// handel het formulier netjes af en voor de acties correct uit
function handle() {
  global $formurl, $emailaddress, $thanksurl;

  // controleer eerst de verplichte velden
  if(!isvalid("Naam") || !isvalid("Bedrijfsnaam") || !isvalid("Telefoonnumer") || !isvalid("E-mail")) {
    redirect($formurl . "?error=invalid");
  }

  // probeer de mails te versturen
  if (sendmail($emailaddress) && sendthanks($_POST["E-mail"])) {
    // redirect naar het bedankje
    redirect($thanksurl);
  } else {
    // het mailen gaat mis; redirect naar formulier met deze parameter
    redirect($formurl . "?error=mail-error");
  }
}

// do it!
handle();

?>