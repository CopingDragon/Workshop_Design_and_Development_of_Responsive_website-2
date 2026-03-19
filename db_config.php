<?php

const DB_HOST = '127.0.0.1';
const DB_NAME = 'workshop';
const DB_USER = 'root';
const DB_PASS = '';

$connection = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME) or die('Connection failed: ' . mysqli_connect_error());


// Must be called after every $stmt->execute() that uses CALL ProcedureName()
function flush_stored_results($con)
{
    while ($con->more_results() && $con->next_result()) {
        $extra = $con->use_result();
        if ($extra instanceof mysqli_result) {
            $extra->free();
        }
    }
}
