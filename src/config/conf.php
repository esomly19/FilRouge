<?php


$config = '
<?php
$username = '.$user.' ;
$password = '.$mdp.' ;
$hostname = '.$host.' ;
$databasename = '.$bdd.';
$mysqli = new mysqli($hostname, $username, $password, $databasename);
?>
';

fwrite($setup ,$config);
fclose($fh);

echo 'ok';
//connect to database
