<?php define('ENTRANCE',__FILE__);

/*
* Show the outputs of commands as plain text on client's browser.
*/
header('Content-Type: text/plain');

/*
* These commands to run:
*/
$command = array(
	'git pull',
	'git submodule update --recursive'
);

chdir('..'); // Run commands from the toplevel of the working tree.
foreach ($command as $cmd) {
	set_time_limit(10*60); // It may takes time.
	echo "$ {$cmd}\n";
	echo shell_exec($cmd.' 2>&1'); // Show both stdout and stderr by `2>&1'.
}

