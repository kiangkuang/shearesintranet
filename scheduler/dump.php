<?php

require __DIR__ . '/../vendor/autoload.php';
use Ifsnop\Mysqldump as IMysqldump;

define('ENVIRONMENT', isset($_SERVER['CI_ENV']) ? $_SERVER['CI_ENV'] : 'local');
if (ENVIRONMENT === 'local') {
    require __DIR__ . '/../application/config/local/database.php';
}

date_default_timezone_set('Asia/Singapore');
$date = date("Y-m-d His");

$db_url = ENVIRONMENT === 'local' ? $db['default']['hostname'] : $_SERVER['JAWSDB_URL'];
$db_name = ENVIRONMENT === 'local' ? $db['default']['database'] : $_SERVER['JAWSDB_DB'];
$db_user = ENVIRONMENT === 'local' ? $db['default']['username'] : $_SERVER['JAWSDB_USER'];
$db_pw = ENVIRONMENT === 'local' ? $db['default']['password'] : $_SERVER['JAWSDB_PW'];

dump($db_url, $db_name, $db_user, $db_pw, $date);
if (ENVIRONMENT !== 'local') {
    email($date);
}

function dump($db_url, $db_name, $db_user, $db_pw, $date) {
    try {
        $dump = new IMysqldump\Mysqldump('mysql:host='.$db_url.';dbname='.$db_name, $db_user, $db_pw, ['add-drop-table' => true]);
        $dump->start(__DIR__ . '/../dump/' . ENVIRONMENT . ' ' . $date . '.sql');
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
            ->setSubject('Database Backup: ' . ENVIRONMENT . ' ' . $date)
            ->addAttachment(__DIR__ . '/../dump/' . ENVIRONMENT . ' ' . $date . '.sql')
            ->setText(ENVIRONMENT . ' ' . $date)
            ->setHtml(ENVIRONMENT . ' ' . $date);
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
