<?php

if (isset($_GET["id"]))
{
 system("gpio -g read ". $_GET["id"]);
}
?>
