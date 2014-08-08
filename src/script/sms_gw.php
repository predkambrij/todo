<?
include(__DIR__ . "/../web/php/include/db.php");
include(__DIR__ . "/twilio-php-master/Services/Twilio.php");

$client = new Services_Twilio($AccountSid, $AuthToken);


try {
    $message = $client->account->messages->create(array(
        "From" => "$twilio_from",
        "To" => "$twilio_to",
        "Body" =>"\n.\n.\n". $argv[1]. "",
    ));
    exit(0);
} catch (Services_Twilio_RestException $e) {
    echo "SMS error: ".$e->getMessage();
    exit(1);
}


?>
