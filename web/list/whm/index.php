<?php
$TAB = "WHM";

// Main include
include $_SERVER["DOCUMENT_ROOT"] . "/inc/main.php";

// Access Control: WHM is strictly for admin / root accounts
if ($_SESSION["userContext"] !== "admin") {
	header("Location: /list/dashboard/");
	exit();
}

// Impersonation check: If admin is impersonating a normal user, return to dashboard
if (!empty($_SESSION["look"])) {
	header("Location: /list/dashboard/");
	exit();
}

// Fetch all users / cPanel accounts
exec(HESTIA_CMD . "v-list-users json", $output, $return_var);
$users_data = json_decode(implode("", $output), true) ?: [];
unset($output);

// Fetch system services / daemons
exec(HESTIA_CMD . "v-list-sys-services json", $output, $return_var);
$services_data = json_decode(implode("", $output), true) ?: [];
unset($output);

// Fetch hosting packages
exec(HESTIA_CMD . "v-list-user-packages json", $output, $return_var);
$packages_data = json_decode(implode("", $output), true) ?: [];
unset($output);

// Fetch IP addresses
exec(HESTIA_CMD . "v-list-sys-ips json", $output, $return_var);
$ips_data = json_decode(implode("", $output), true) ?: [];
unset($output);

// Calculate WHM Aggregations
$total_accounts = count($users_data);
$active_accounts = 0;
$suspended_accounts = 0;
$total_domains = 0;
$total_disk_used = 0;
$total_bandwidth_used = 0;

foreach ($users_data as $uname => $u) {
	if (($u["SUSPENDED"] ?? "no") === "yes") {
		$suspended_accounts++;
	} else {
		$active_accounts++;
	}
	$total_domains += intval($u["U_WEB_DOMAINS"] ?? 0);
	$total_disk_used += intval($u["U_DISK"] ?? 0);
	$total_bandwidth_used += intval($u["U_BANDWIDTH"] ?? 0);
}

// System Health metrics
$sys_load = sys_getloadavg();
$load_str = is_array($sys_load)
	? implode(", ", array_map(fn($n) => number_format($n, 2), $sys_load))
	: "0.10, 0.08, 0.05";

// Pass data to template
$data = [
	"users" => $users_data,
	"services" => $services_data,
	"packages" => $packages_data,
	"ips" => $ips_data,
	"stats" => [
		"total_accounts" => $total_accounts,
		"active_accounts" => $active_accounts,
		"suspended_accounts" => $suspended_accounts,
		"total_domains" => $total_domains,
		"total_disk_used" => $total_disk_used,
		"total_bandwidth_used" => $total_bandwidth_used,
		"load_str" => $load_str,
	],
];

// Render page
render_page($user, $TAB, "list_whm");

// Back uri
$_SESSION["back"] = $_SERVER["REQUEST_URI"];
