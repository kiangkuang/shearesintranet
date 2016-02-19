<?php

require '../vendor/autoload.php';
use Ifsnop\Mysqldump as IMysqldump;

$local = !isset($_SERVER['CI_ENV']) || $_SERVER['CI_ENV'] === 'local';
if ($local) {
    define('ENVIRONMENT', 'local');
    require '../application/config/local/database.php';
}

date_default_timezone_set('Asia/Singapore');
$date = date("Y-m-d His");

$db_url = $local ? $db['default']['hostname'] : $_SERVER['JAWSDB_URL'];
$db_name = $local ? $db['default']['database'] : $_SERVER['JAWSDB_DB'];
$db_user = $local ? $db['default']['username'] : $_SERVER['JAWSDB_USER'];
$db_pw = $local ? $db['default']['password'] : $_SERVER['JAWSDB_PW'];

dump($db_url, $db_name, $db_user, $db_pw, $date);
if (!$local) {
    email($date);
}

function dump($db_url, $db_name, $db_user, $db_pw, $date) {
    try {
        $dump = new IMysqldump\Mysqldump('mysql:host='.$db_url.';dbname='.$db_name, $db_user, $db_pw, ['add-drop-table' => true]);
        $dump->start('../dump/' . $date . '.sql');
    } catch (\Exception $e) {
        echo 'mysqldump-php error: ' . $e->getMessage();
    }
    echo '<h1>Dump Successful</h1>';
}

function email($date) {
    $sendgrid = new SendGrid($_SERVER['SENDGRID_USERNAME'], $_SERVER['SENDGRID_PASSWORD']);

    $message = new SendGrid\Email();
    $message->addTo($_SERVER['BACKUP_EMAIL'])
            ->setFrom('backup@shearesintranet.nus.edu.sg')
            ->setFromName('Sheares Intranet Backup')
            ->setSubject('Database Backup ' . $date)
            ->addAttachment('../dump/' . $date . '.sql')
            ->setText($date)
            ->setHtml($date);
    try {
        $response = $sendgrid->send($message);
    } catch(\SendGrid\Exception $e) {
        echo 'sendgrid error: ';
        echo $e->getCode() . "\n";
        foreach($e->getErrors() as $er) {
            echo $er . "\n";
        }
    }
    echo '<h1>Email Successful</h1>';
}
