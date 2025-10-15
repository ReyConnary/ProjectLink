<?php
// Put the entire path inside quotes so Windows treats it as one argument
$exe = '"I:\\PC Files\\XAMPP\\htdocs\\PERANGKINGAN\\frontend\\ProgramSAW.exe"';

$desc = [
    0 => ["pipe", "r"],
    1 => ["pipe", "w"],
    2 => ["pipe", "w"]
];

$proc = proc_open($exe, $desc, $pipes);

if (is_resource($proc)) {
    fwrite($pipes[0], "2\nTest1\n0.5\nB\nTest2\n0.5\nC\n2\nAlt1\n1\n2\nAlt2\n2\n1\n");
    fclose($pipes[0]);

    echo "<pre>" . stream_get_contents($pipes[1]) . "</pre>";
    fclose($pipes[1]);

    echo "<pre>ERR: " . stream_get_contents($pipes[2]) . "</pre>";
    fclose($pipes[2]);

    proc_close($proc);
} else {
    echo "proc_open failed.";
}
?>
