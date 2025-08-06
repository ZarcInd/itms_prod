<?php
function logMsg($msg)
{
    $log_enabled = true;
    if ($log_enabled) {
        error_log(json_encode($msg));
    }
}

function logError($msg)
{
    error_log(json_encode($msg));
}
