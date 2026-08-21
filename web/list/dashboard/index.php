<?php
$TAB = "DASHBOARD";

// Main include
include $_SERVER["DOCUMENT_ROOT"] . "/inc/main.php";

// Fetch additional stats if available
$sys_load = "";
$server_info = [];

if (function_exists("sys_getloadavg")) {
	$load = sys_getloadavg();
	if (is_array($load) && count($load) >= 3) {
		$sys_load =
			number_format($load[0], 2) .
			", " .
			number_format($load[1], 2) .
			", " .
			number_format($load[2], 2);
	}
}

// Render page
render_page($user, $TAB, "list_dashboard");

// Back uri
$_SESSION["back"] = $_SERVER["REQUEST_URI"];
