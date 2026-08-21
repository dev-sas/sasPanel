<?php
// Resolve active user key safely
$active_user = !empty($_SESSION["look"]) ? $_SESSION["look"] : ($_SESSION["user"] ?? ($user_plain ?? (isset($user) ? trim($user, "'\"") : "admin")));
$user_data = $panel[$active_user] ?? ($panel[$user_plain] ?? (is_array($panel) ? reset($panel) : []));

if (!function_exists("get_percentage")) {
	function get_percentage($used, $total) {
		if ($total === "unlimited" || $total === "0" || empty($total) || !is_numeric($total) || !is_numeric($used)) {
			return 0;
		}
		if ($total <= 0) return 0;
		return round(($used / $total) * 100, 1);
	}
}

if (!function_exists("get_meter_color")) {
	function get_meter_color($pct) {
		if ($pct >= 90) return 'fill-red';
		if ($pct >= 70) return 'fill-amber';
		return 'fill-green';
	}
}

$u_disk = $user_data["U_DISK"] ?? 0;
$disk_quota = $user_data["DISK_QUOTA"] ?? "unlimited";
$disk_pct = get_percentage($u_disk, $disk_quota);

$u_bw = $user_data["U_BANDWIDTH"] ?? 0;
$bw_quota = $user_data["BANDWIDTH"] ?? "unlimited";
$bw_pct = get_percentage($u_bw, $bw_quota);

$u_web = $user_data["U_WEB_DOMAINS"] ?? 0;
$max_web = $user_data["WEB_DOMAINS"] ?? "unlimited";
$web_pct = get_percentage($u_web, $max_web);

$u_mail = $user_data["U_MAIL_ACCOUNTS"] ?? 0;
$max_mail = $user_data["MAIL_ACCOUNTS"] ?? "unlimited";
$mail_pct = get_percentage($u_mail, $max_mail);

$u_db = $user_data["U_DATABASES"] ?? 0;
$max_db = $user_data["DATABASES"] ?? "unlimited";
$db_pct = get_percentage($u_db, $max_db);

$u_cron = $user_data["U_CRON_JOBS"] ?? 0;
$max_cron = $user_data["CRON_JOBS"] ?? "unlimited";
$cron_pct = get_percentage($u_cron, $max_cron);

$u_backup = $user_data["U_BACKUPS"] ?? 0;
$max_backup = $user_data["BACKUPS"] ?? "unlimited";
$backup_pct = get_percentage($u_backup, $max_backup);

$u_dns = $user_data["U_DNS_DOMAINS"] ?? 0;
$max_dns = $user_data["DNS_DOMAINS"] ?? "unlimited";
$dns_pct = get_percentage($u_dns, $max_dns);

$server_host = $_SERVER['SERVER_NAME'] ?? gethostname();
$server_ip = $_SERVER['SERVER_ADDR'] ?? get_real_user_ip();
$display_name = !empty($user_data["NAME"]) ? $user_data["NAME"] : $active_user;

[$http_host, $port] = explode(":", $_SERVER["HTTP_HOST"] . ":");
$db_myadmin_link = "//" . $http_host . "/phpmyadmin/";
if (!empty($_SESSION["DB_PMA_ALIAS"])) {
	$db_myadmin_link = "//" . $http_host . "/" . $_SESSION["DB_PMA_ALIAS"] . "/";
}
if (isset($_SESSION['PHPMYADMIN_KEY']) && $_SESSION['PHPMYADMIN_KEY'] != '' && !ipUsed()) {
	$time = time();
	$pma_token = md5($_SESSION['user_combined_ip'] . $time . $_SESSION['PHPMYADMIN_KEY']);
	$db_myadmin_link = "//" . $http_host . "/phpmyadmin/hst_sso.php?user=" . urlencode($active_user) . "&time=" . $time . "&token=" . $pma_token;
}

$webmail_link = "//" . $http_host . "/webmail/";
if (!empty($_SESSION["WEBMAIL_ALIAS"])) {
	$webmail_link = "//" . $http_host . "/" . $_SESSION["WEBMAIL_ALIAS"] . "/";
}
?>

<link rel="stylesheet" href="/css/cpanel.css?<?= JS_LATEST_UPDATE ?>">

<div class="cpanel-dashboard-container">

	<!-- Top Hero & Search Bar -->
	<div class="cpanel-hero">
		<div class="cpanel-hero-welcome">
			<div class="cpanel-hero-avatar">
				<i class="fas <?= $_SESSION["userContext"] === "admin" ? "fa-user-shield" : "fa-user" ?>"></i>
			</div>
			<div>
				<h1 class="cpanel-hero-title">
					<?= _("Welcome to your Control Panel") ?>, <?= htmlspecialchars($display_name) ?>!
				</h1>
				<p class="cpanel-hero-subtitle">
					<?= _("Manage your websites, domains, databases, emails, and server settings.") ?>
				</p>
			</div>
		</div>

		<div class="cpanel-hero-actions">
			<div class="cpanel-search-wrapper">
				<i class="fas fa-magnifying-glass cpanel-search-icon"></i>
				<input
					type="search"
					id="cpanelSearchInput"
					class="cpanel-search-input"
					placeholder="<?= _("Search tools, domains, databases, emails...") ?>"
					autocomplete="off"
					aria-label="<?= _("Search tools") ?>"
				>
				<span class="cpanel-search-badge" title="<?= _("Press / or Ctrl+K to search") ?>">/</span>
				<button type="button" id="cpanelSearchClear" class="cpanel-search-clear" title="<?= _("Clear search") ?>">
					<i class="fas fa-xmark"></i>
				</button>
			</div>

			<div class="cpanel-quick-controls">
				<button type="button" id="cpanelExpandAll" class="cpanel-control-btn" title="<?= _("Expand all categories") ?>">
					<i class="fas fa-angles-down"></i>
					<span class="u-hide-mobile"><?= _("Expand") ?></span>
				</button>
				<button type="button" id="cpanelCollapseAll" class="cpanel-control-btn" title="<?= _("Collapse all categories") ?>">
					<i class="fas fa-angles-up"></i>
					<span class="u-hide-mobile"><?= _("Collapse") ?></span>
				</button>
			</div>
		</div>
	</div>

	<!-- Two-Column cPanel Grid Layout -->
	<div class="cpanel-grid-layout">

		<!-- Left/Main: Collapsible Category Cards -->
		<div class="cpanel-main-content">

			<!-- Empty Search Results Message -->
			<div id="cpanelNoResults" class="cpanel-no-results">
				<div class="cpanel-no-results-icon">
					<i class="fas fa-magnifying-glass"></i>
				</div>
				<h3 class="cpanel-no-results-title"><?= _("No matching tools found") ?></h3>
				<p class="cpanel-no-results-desc"><?= _("Please try another keyword or search query.") ?></p>
				<button type="button" id="cpanelResetSearch" class="button button-secondary">
					<i class="fas fa-rotate-left"></i> <?= _("Reset Search") ?>
				</button>
			</div>

			<!-- 1. FILES CATEGORY -->
			<div class="cpanel-category-card" data-category-id="files">
				<div class="cpanel-category-header">
					<div class="cpanel-category-header-left">
						<div class="cpanel-category-icon cat-icon-files">
							<i class="fas fa-folder-tree"></i>
						</div>
						<h2 class="cpanel-category-title"><?= _("Files") ?></h2>
						<span class="cpanel-category-count">6</span>
					</div>
					<div class="cpanel-category-toggle">
						<i class="fas fa-chevron-down"></i>
					</div>
				</div>
				<div class="cpanel-category-body">
					<div class="cpanel-tools-grid">

						<?php if (isset($_SESSION["FILE_MANAGER"]) && $_SESSION["FILE_MANAGER"] == "true") { ?>
						<a href="/fm/" class="cpanel-tool-item" data-keywords="file manager upload download edit explorer directory htdocs">
							<div class="cpanel-tool-icon-box tool-icon-fm">
								<i class="fas fa-folder-open"></i>
							</div>
							<span class="cpanel-tool-name"><?= _("File Manager") ?></span>
							<span class="cpanel-tool-desc"><?= _("Browse and manage files") ?></span>
						</a>
						<?php } ?>

						<?php if (isset($_SESSION["BACKUP_SYSTEM"]) && !empty($_SESSION["BACKUP_SYSTEM"])) { ?>
						<a href="/list/backup/" class="cpanel-tool-item" data-keywords="backups restore download archive snapshots tar zip">
							<div class="cpanel-tool-icon-box tool-icon-backup">
								<i class="fas fa-box-archive"></i>
							</div>
							<span class="cpanel-tool-name"><?= _("Backups") ?></span>
							<span class="cpanel-tool-desc"><?= _("Create & restore backups") ?></span>
						</a>

						<a href="/list/backup/exclusions/" class="cpanel-tool-item" data-keywords="backup exclusions ignore filter exclude">
							<div class="cpanel-tool-icon-box tool-icon-backup">
								<i class="fas fa-filter"></i>
							</div>
							<span class="cpanel-tool-name"><?= _("Backup Exclusions") ?></span>
							<span class="cpanel-tool-desc"><?= _("Configure ignored paths") ?></span>
						</a>
						<?php } ?>

						<?php if (isset($_SESSION["WEB_TERMINAL"]) && $_SESSION["WEB_TERMINAL"] == "true" && $_SESSION["login_shell"] != "nologin") { ?>
						<a href="/list/terminal/" class="cpanel-tool-item" data-keywords="terminal console ssh shell bash command line">
							<div class="cpanel-tool-icon-box tool-icon-terminal">
								<i class="fas fa-terminal"></i>
							</div>
							<span class="cpanel-tool-name"><?= _("Web Terminal") ?></span>
							<span class="cpanel-tool-desc"><?= _("In-browser SSH console") ?></span>
						</a>
						<?php } ?>

						<a href="/list/web/" class="cpanel-tool-item" data-keywords="ftp accounts credentials sftp file transfer upload">
							<div class="cpanel-tool-icon-box tool-icon-fm">
								<i class="fas fa-network-wired"></i>
							</div>
							<span class="cpanel-tool-name"><?= _("FTP Accounts") ?></span>
							<span class="cpanel-tool-desc"><?= _("Manage FTP / SFTP users") ?></span>
						</a>

						<a href="/list/server/?disk" class="cpanel-tool-item" data-keywords="disk usage quota space storage analyzer">
							<div class="cpanel-tool-icon-box tool-icon-disk">
								<i class="fas fa-hard-drive"></i>
							</div>
							<span class="cpanel-tool-name"><?= _("Disk Usage") ?></span>
							<span class="cpanel-tool-desc"><?= _("Inspect storage consumption") ?></span>
						</a>

					</div>
				</div>
			</div>

			<!-- 2. DOMAINS & WEB CATEGORY -->
			<?php if (isset($_SESSION["WEB_SYSTEM"]) && !empty($_SESSION["WEB_SYSTEM"])) { ?>
			<div class="cpanel-category-card" data-category-id="domains">
				<div class="cpanel-category-header">
					<div class="cpanel-category-header-left">
						<div class="cpanel-category-icon cat-icon-domains">
							<i class="fas fa-earth-americas"></i>
						</div>
						<h2 class="cpanel-category-title"><?= _("Domains & Web") ?></h2>
						<span class="cpanel-category-count">7</span>
					</div>
					<div class="cpanel-category-toggle">
						<i class="fas fa-chevron-down"></i>
					</div>
				</div>
				<div class="cpanel-category-body">
					<div class="cpanel-tools-grid">

						<a href="/list/web/" class="cpanel-tool-item" data-keywords="web domains sites virtualhosts vhosts apache nginx aliases">
							<div class="cpanel-tool-icon-box tool-icon-web">
								<i class="fas fa-globe"></i>
							</div>
							<span class="cpanel-tool-name"><?= _("Web Domains") ?></span>
							<span class="cpanel-tool-desc"><?= _("Manage websites & aliases") ?></span>
						</a>

						<a href="/add/web/" class="cpanel-tool-item" data-keywords="add domain new website create host domain">
							<div class="cpanel-tool-icon-box tool-icon-web">
								<i class="fas fa-circle-plus"></i>
							</div>
							<span class="cpanel-tool-name"><?= _("Add Domain") ?></span>
							<span class="cpanel-tool-desc"><?= _("Create new web domain") ?></span>
						</a>

						<?php if (isset($_SESSION["DNS_SYSTEM"]) && !empty($_SESSION["DNS_SYSTEM"])) { ?>
						<a href="/list/dns/" class="cpanel-tool-item" data-keywords="dns zone editor records a mx cname txt ns bind named">
							<div class="cpanel-tool-icon-box tool-icon-dns">
								<i class="fas fa-book-atlas"></i>
							</div>
							<span class="cpanel-tool-name"><?= _("Zone Editor") ?></span>
							<span class="cpanel-tool-desc"><?= _("DNS zones & records") ?></span>
						</a>

						<a href="/add/dns/" class="cpanel-tool-item" data-keywords="add dns new zone domain name servers">
							<div class="cpanel-tool-icon-box tool-icon-dns">
								<i class="fas fa-plus-circle"></i>
							</div>
							<span class="cpanel-tool-name"><?= _("Add DNS Zone") ?></span>
							<span class="cpanel-tool-desc"><?= _("Create custom DNS zone") ?></span>
						</a>
						<?php } ?>

						<a href="/list/web/" class="cpanel-tool-item" data-keywords="ssl tls lets encrypt certificates https secure certs">
							<div class="cpanel-tool-icon-box tool-icon-ssl">
								<i class="fas fa-shield-halved"></i>
							</div>
							<span class="cpanel-tool-name"><?= _("SSL / HTTPS") ?></span>
							<span class="cpanel-tool-desc"><?= _("Let's Encrypt & certs") ?></span>
						</a>

						<a href="/add/webapp/" class="cpanel-tool-item" data-keywords="wordpress quick install web apps joomla drupal phpbb nodejs">
							<div class="cpanel-tool-icon-box tool-icon-app">
								<i class="fas fa-cubes"></i>
							</div>
							<span class="cpanel-tool-name"><?= _("Web Apps / WordPress") ?></span>
							<span class="cpanel-tool-desc"><?= _("1-Click App Installer") ?></span>
						</a>

						<a href="/list/web-log/" class="cpanel-tool-item" data-keywords="web logs access error logs visitor traffic nginx apache">
							<div class="cpanel-tool-icon-box tool-icon-logs">
								<i class="fas fa-file-lines"></i>
							</div>
							<span class="cpanel-tool-name"><?= _("Access & Error Logs") ?></span>
							<span class="cpanel-tool-desc"><?= _("Live web server logs") ?></span>
						</a>

					</div>
				</div>
			</div>
			<?php } ?>

			<!-- 3. DATABASES CATEGORY -->
			<?php if (isset($_SESSION["DB_SYSTEM"]) && !empty($_SESSION["DB_SYSTEM"])) { ?>
			<div class="cpanel-category-card" data-category-id="databases">
				<div class="cpanel-category-header">
					<div class="cpanel-category-header-left">
						<div class="cpanel-category-icon cat-icon-databases">
							<i class="fas fa-database"></i>
						</div>
						<h2 class="cpanel-category-title"><?= _("Databases") ?></h2>
						<span class="cpanel-category-count"><?= !empty($user_data["DATABASES_PMA"]) ? "3" : "2" ?></span>
					</div>
					<div class="cpanel-category-toggle">
						<i class="fas fa-chevron-down"></i>
					</div>
				</div>
				<div class="cpanel-category-body">
					<div class="cpanel-tools-grid">

						<a href="/list/db/" class="cpanel-tool-item" data-keywords="mysql databases mariadb db sql users tables postgresql">
							<div class="cpanel-tool-icon-box tool-icon-db">
								<i class="fas fa-database"></i>
							</div>
							<span class="cpanel-tool-name"><?= _("MySQL Databases") ?></span>
							<span class="cpanel-tool-desc"><?= _("Manage DBs & users") ?></span>
						</a>

						<a href="/add/db/" class="cpanel-tool-item" data-keywords="add database new mysql mariadb create db user">
							<div class="cpanel-tool-icon-box tool-icon-db">
								<i class="fas fa-folder-plus"></i>
							</div>
							<span class="cpanel-tool-name"><?= _("Add Database") ?></span>
							<span class="cpanel-tool-desc"><?= _("Create new database") ?></span>
						</a>

						<a href="<?= htmlspecialchars($db_myadmin_link) ?>" target="_blank" rel="noopener" class="cpanel-tool-item" data-keywords="phpmyadmin pma sql query export import table database admin">
							<div class="cpanel-tool-icon-box tool-icon-pma">
								<i class="fas fa-table-columns"></i>
							</div>
							<span class="cpanel-tool-name">phpMyAdmin</span>
							<span class="cpanel-tool-desc"><?= _("Web database manager") ?></span>
						</a>

					</div>
				</div>
			</div>
			<?php } ?>

			<!-- 4. EMAIL CATEGORY -->
			<?php if (isset($_SESSION["MAIL_SYSTEM"]) && !empty($_SESSION["MAIL_SYSTEM"])) { ?>
			<div class="cpanel-category-card" data-category-id="email">
				<div class="cpanel-category-header">
					<div class="cpanel-category-header-left">
						<div class="cpanel-category-icon cat-icon-email">
							<i class="fas fa-envelope"></i>
						</div>
						<h2 class="cpanel-category-title"><?= _("Email") ?></h2>
						<span class="cpanel-category-count">4</span>
					</div>
					<div class="cpanel-category-toggle">
						<i class="fas fa-chevron-down"></i>
					</div>
				</div>
				<div class="cpanel-category-body">
					<div class="cpanel-tools-grid">

						<a href="/list/mail/" class="cpanel-tool-item" data-keywords="email accounts mail inbox mailbox imap pop3 smtp">
							<div class="cpanel-tool-icon-box tool-icon-mail">
								<i class="fas fa-envelope-open-text"></i>
							</div>
							<span class="cpanel-tool-name"><?= _("Email Accounts") ?></span>
							<span class="cpanel-tool-desc"><?= _("Manage mailboxes & quotas") ?></span>
						</a>

						<a href="/add/mail/" class="cpanel-tool-item" data-keywords="add mail domain new email address create">
							<div class="cpanel-tool-icon-box tool-icon-mail">
								<i class="fas fa-plus-circle"></i>
							</div>
							<span class="cpanel-tool-name"><?= _("Add Mail Domain") ?></span>
							<span class="cpanel-tool-desc"><?= _("Setup new email domain") ?></span>
						</a>

						<a href="<?= htmlspecialchars($webmail_link) ?>" target="_blank" rel="noopener" class="cpanel-tool-item" data-keywords="webmail roundcube snappymail rainloop read email inbox">
							<div class="cpanel-tool-icon-box tool-icon-webmail">
								<i class="fas fa-inbox"></i>
							</div>
							<span class="cpanel-tool-name"><?= _("Webmail") ?></span>
							<span class="cpanel-tool-desc"><?= _("Access Roundcube / Webmail") ?></span>
						</a>

						<a href="/list/dns/" class="cpanel-tool-item" data-keywords="mail dns dkim spf dmarc spam authentication records">
							<div class="cpanel-tool-icon-box tool-icon-mail">
								<i class="fas fa-shield-cat"></i>
							</div>
							<span class="cpanel-tool-name"><?= _("Mail DNS & DKIM") ?></span>
							<span class="cpanel-tool-desc"><?= _("SPF, DKIM & deliverability") ?></span>
						</a>

					</div>
				</div>
			</div>
			<?php } ?>

			<!-- 5. METRICS & MONITORING CATEGORY -->
			<div class="cpanel-category-card" data-category-id="metrics">
				<div class="cpanel-category-header">
					<div class="cpanel-category-header-left">
						<div class="cpanel-category-icon cat-icon-metrics">
							<i class="fas fa-chart-pie"></i>
						</div>
						<h2 class="cpanel-category-title"><?= _("Metrics & Logs") ?></h2>
						<span class="cpanel-category-count">4</span>
					</div>
					<div class="cpanel-category-toggle">
						<i class="fas fa-chevron-down"></i>
					</div>
				</div>
				<div class="cpanel-category-body">
					<div class="cpanel-tools-grid">

						<a href="/list/stats/" class="cpanel-tool-item" data-keywords="statistics analytics visitors awstats bandwidth traffic chart">
							<div class="cpanel-tool-icon-box tool-icon-stats">
								<i class="fas fa-chart-line"></i>
							</div>
							<span class="cpanel-tool-name"><?= _("Web Statistics") ?></span>
							<span class="cpanel-tool-desc"><?= _("Visitor analytics & traffic") ?></span>
						</a>

						<a href="/list/rrd/" class="cpanel-tool-item" data-keywords="rrd graphs health monitoring cpu ram load bandwidth server status">
							<div class="cpanel-tool-icon-box tool-icon-stats">
								<i class="fas fa-chart-area"></i>
							</div>
							<span class="cpanel-tool-name"><?= _("System Health (RRD)") ?></span>
							<span class="cpanel-tool-desc"><?= _("CPU, RAM & Network Graphs") ?></span>
						</a>

						<a href="/list/log/" class="cpanel-tool-item" data-keywords="activity logs history audit trail actions events">
							<div class="cpanel-tool-icon-box tool-icon-logs">
								<i class="fas fa-clock-rotate-left"></i>
							</div>
							<span class="cpanel-tool-name"><?= _("Activity Logs") ?></span>
							<span class="cpanel-tool-desc"><?= _("Recent account actions") ?></span>
						</a>

						<a href="/list/log/auth/" class="cpanel-tool-item" data-keywords="auth logs login failed attempts security audit ip">
							<div class="cpanel-tool-icon-box tool-icon-logs">
								<i class="fas fa-fingerprint"></i>
							</div>
							<span class="cpanel-tool-name"><?= _("Auth Logs") ?></span>
							<span class="cpanel-tool-desc"><?= _("Login audit & history") ?></span>
						</a>

					</div>
				</div>
			</div>

			<!-- 6. SECURITY & ACCESS CATEGORY -->
			<div class="cpanel-category-card" data-category-id="security">
				<div class="cpanel-category-header">
					<div class="cpanel-category-header-left">
						<div class="cpanel-category-icon cat-icon-security">
							<i class="fas fa-shield-halved"></i>
						</div>
						<h2 class="cpanel-category-title"><?= _("Security & Access") ?></h2>
						<span class="cpanel-category-count"><?= $_SESSION["userContext"] === "admin" ? "5" : "3" ?></span>
					</div>
					<div class="cpanel-category-toggle">
						<i class="fas fa-chevron-down"></i>
					</div>
				</div>
				<div class="cpanel-category-body">
					<div class="cpanel-tools-grid">

						<a href="/list/key/" class="cpanel-tool-item" data-keywords="ssh keys public key authorized keys rsa ed25519 authentication">
							<div class="cpanel-tool-icon-box tool-icon-ssh">
								<i class="fas fa-key"></i>
							</div>
							<span class="cpanel-tool-name"><?= _("SSH Keys") ?></span>
							<span class="cpanel-tool-desc"><?= _("Manage SSH authentication") ?></span>
						</a>

						<a href="/list/access-key/" class="cpanel-tool-item" data-keywords="api access keys tokens authentication rest automation">
							<div class="cpanel-tool-icon-box tool-icon-ssh">
								<i class="fas fa-id-badge"></i>
							</div>
							<span class="cpanel-tool-name"><?= _("API Access Keys") ?></span>
							<span class="cpanel-tool-desc"><?= _("Manage REST API tokens") ?></span>
						</a>

						<a href="/edit/user/?user=<?= htmlspecialchars($active_user) ?>&token=<?= $_SESSION["token"] ?>" class="cpanel-tool-item" data-keywords="2fa two factor authentication security otp qr code google authenticator">
							<div class="cpanel-tool-icon-box tool-icon-2fa">
								<i class="fas fa-mobile-screen-button"></i>
							</div>
							<span class="cpanel-tool-name"><?= _("2-Factor Auth (2FA)") ?></span>
							<span class="cpanel-tool-desc"><?= _("Secure your account with 2FA") ?></span>
						</a>

						<?php if (($_SESSION["userContext"] === "admin" && $_SESSION["POLICY_SYSTEM_HIDE_SERVICES"] !== "yes") || $_SESSION["user"] === $_SESSION['ROOT_USER']) { ?>
						<a href="/list/firewall/" class="cpanel-tool-item" data-keywords="firewall iptables ufw ports rules block allow security">
							<div class="cpanel-tool-icon-box tool-icon-firewall">
								<i class="fas fa-shield"></i>
							</div>
							<span class="cpanel-tool-name"><?= _("Firewall Rules") ?></span>
							<span class="cpanel-tool-desc"><?= _("Port & IP filtering") ?></span>
						</a>

						<a href="/list/firewall/banlist/" class="cpanel-tool-item" data-keywords="banlist fail2ban blacklists blocked ips brute force">
							<div class="cpanel-tool-icon-box tool-icon-firewall">
								<i class="fas fa-ban"></i>
							</div>
							<span class="cpanel-tool-name"><?= _("IP Ban List") ?></span>
							<span class="cpanel-tool-desc"><?= _("Fail2ban blocked clients") ?></span>
						</a>
						<?php } ?>

					</div>
				</div>
			</div>

			<!-- 7. SERVER & SYSTEM CATEGORY (ADMIN / CRON) -->
			<div class="cpanel-category-card" data-category-id="server">
				<div class="cpanel-category-header">
					<div class="cpanel-category-header-left">
						<div class="cpanel-category-icon cat-icon-software">
							<i class="fas fa-gears"></i>
						</div>
						<h2 class="cpanel-category-title"><?= _("Software & Advanced") ?></h2>
						<span class="cpanel-category-count"><?= $_SESSION["userContext"] === "admin" ? "6" : "2" ?></span>
					</div>
					<div class="cpanel-category-toggle">
						<i class="fas fa-chevron-down"></i>
					</div>
				</div>
				<div class="cpanel-category-body">
					<div class="cpanel-tools-grid">

						<?php if (isset($_SESSION["CRON_SYSTEM"]) && !empty($_SESSION["CRON_SYSTEM"])) { ?>
						<a href="/list/cron/" class="cpanel-tool-item" data-keywords="cron jobs crontab scheduled tasks automation timing schedule">
							<div class="cpanel-tool-icon-box tool-icon-cron">
								<i class="fas fa-clock"></i>
							</div>
							<span class="cpanel-tool-name"><?= _("Cron Jobs") ?></span>
							<span class="cpanel-tool-desc"><?= _("Automated scheduled tasks") ?></span>
						</a>
						<?php } ?>

						<a href="/edit/user/?user=<?= htmlspecialchars($active_user) ?>&token=<?= $_SESSION["token"] ?>" class="cpanel-tool-item" data-keywords="user profile settings password language theme php preferences">
							<div class="cpanel-tool-icon-box tool-icon-users">
								<i class="fas fa-user-gear"></i>
							</div>
							<span class="cpanel-tool-name"><?= _("User Preferences") ?></span>
							<span class="cpanel-tool-desc"><?= _("Password, theme & language") ?></span>
						</a>

						<?php if (($_SESSION["userContext"] === "admin" && $_SESSION["POLICY_SYSTEM_HIDE_SERVICES"] !== "yes") || $_SESSION["user"] === $_SESSION['ROOT_USER']) { ?>
						<a href="/list/user/" class="cpanel-tool-item" data-keywords="user accounts management hosting packages customers clients">
							<div class="cpanel-tool-icon-box tool-icon-users">
								<i class="fas fa-users"></i>
							</div>
							<span class="cpanel-tool-name"><?= _("User Accounts") ?></span>
							<span class="cpanel-tool-desc"><?= _("Manage server users & packages") ?></span>
						</a>

						<a href="/list/server/" class="cpanel-tool-item" data-keywords="server settings config configuration apache nginx php mysql bind">
							<div class="cpanel-tool-icon-box tool-icon-server">
								<i class="fas fa-server"></i>
							</div>
							<span class="cpanel-tool-name"><?= _("Server Settings") ?></span>
							<span class="cpanel-tool-desc"><?= _("Core daemon configurations") ?></span>
						</a>

						<a href="/list/server/" class="cpanel-tool-item" data-keywords="services status daemons restart stop start health">
							<div class="cpanel-tool-icon-box tool-icon-server">
								<i class="fas fa-wave-square"></i>
							</div>
							<span class="cpanel-tool-name"><?= _("Service Status") ?></span>
							<span class="cpanel-tool-desc"><?= _("Monitor & restart daemons") ?></span>
						</a>

						<a href="/list/ip/" class="cpanel-tool-item" data-keywords="ip addresses network netmask interface shared dedicated">
							<div class="cpanel-tool-icon-box tool-icon-server">
								<i class="fas fa-network-wired"></i>
							</div>
							<span class="cpanel-tool-name"><?= _("IP Addresses") ?></span>
							<span class="cpanel-tool-desc"><?= _("Manage server IP bindings") ?></span>
						</a>

						<a href="/list/updates/" class="cpanel-tool-item" data-keywords="updates upgrade packages software patches version">
							<div class="cpanel-tool-icon-box tool-icon-server">
								<i class="fas fa-arrows-rotate"></i>
							</div>
							<span class="cpanel-tool-name"><?= _("System Updates") ?></span>
							<span class="cpanel-tool-desc"><?= _("Check and apply upgrades") ?></span>
						</a>
						<?php } ?>

					</div>
				</div>
			</div>

		</div>

		<!-- Right/Sidebar: General Info & Resource Usage Statistics -->
		<div class="cpanel-sidebar">

			<!-- General Information Card -->
			<div class="cpanel-sidebar-card">
				<div class="cpanel-sidebar-header">
					<h3 class="cpanel-sidebar-title">
						<i class="fas fa-circle-info icon-blue"></i>
						<?= _("General Information") ?>
					</h3>
				</div>
				<div class="cpanel-sidebar-body">
					<ul class="cpanel-info-list">
						<li class="cpanel-info-row">
							<span class="cpanel-info-label">
								<i class="fas fa-user"></i> <?= _("Current User") ?>
							</span>
							<span class="cpanel-info-value">
								<?= htmlspecialchars($active_user) ?>
								<span class="cpanel-pill-badge <?= $_SESSION["userContext"] === "admin" ? "badge-admin" : "" ?>">
									<?= htmlspecialchars($_SESSION["userContext"] ?? "user") ?>
								</span>
							</span>
						</li>

						<li class="cpanel-info-row">
							<span class="cpanel-info-label">
								<i class="fas fa-server"></i> <?= _("Hostname") ?>
							</span>
							<span class="cpanel-info-value" title="<?= htmlspecialchars($server_host) ?>">
								<?= htmlspecialchars(strlen($server_host) > 20 ? substr($server_host, 0, 18) . "…" : $server_host) ?>
							</span>
						</li>

						<li class="cpanel-info-row">
							<span class="cpanel-info-label">
								<i class="fas fa-network-wired"></i> <?= _("Shared IP") ?>
							</span>
							<span class="cpanel-info-value">
								<?= htmlspecialchars($server_ip) ?>
							</span>
						</li>

						<li class="cpanel-info-row">
							<span class="cpanel-info-label">
								<i class="fas fa-folder"></i> <?= _("Home Directory") ?>
							</span>
							<span class="cpanel-info-value">
								<code>/home/<?= htmlspecialchars($active_user) ?></code>
							</span>
						</li>

						<?php if (!empty($user_data["PACKAGE"])) { ?>
						<li class="cpanel-info-row">
							<span class="cpanel-info-label">
								<i class="fas fa-box"></i> <?= _("Package Plan") ?>
							</span>
							<span class="cpanel-info-value">
								<?= htmlspecialchars($user_data["PACKAGE"]) ?>
							</span>
						</li>
						<?php } ?>

						<?php if (!empty($sys_load)) { ?>
						<li class="cpanel-info-row">
							<span class="cpanel-info-label">
								<i class="fas fa-microchip"></i> <?= _("Server Load") ?>
							</span>
							<span class="cpanel-info-value">
								<?= htmlspecialchars($sys_load) ?>
							</span>
						</li>
						<?php } ?>
					</ul>
				</div>
			</div>

			<!-- Resource Usage & Statistics Card -->
			<div class="cpanel-sidebar-card">
				<div class="cpanel-sidebar-header">
					<h3 class="cpanel-sidebar-title">
						<i class="fas fa-chart-simple icon-green"></i>
						<?= _("Statistics & Quota") ?>
					</h3>
				</div>
				<div class="cpanel-sidebar-body">
					<div class="cpanel-meters-list">

						<!-- Disk Usage -->
						<div class="cpanel-meter-item">
							<div class="cpanel-meter-header">
								<span class="cpanel-meter-title">
									<i class="fas fa-hard-drive"></i> <?= _("Disk Usage") ?>
								</span>
								<span class="cpanel-meter-value">
									<?= humanize_usage_size($u_disk) ?> <?= humanize_usage_measure($u_disk) ?> /
									<?= $disk_quota === "unlimited" ? "∞" : humanize_usage_size($disk_quota) . " " . humanize_usage_measure($disk_quota) ?>
									<?php if ($disk_quota !== "unlimited") { echo " (" . $disk_pct . "%)"; } ?>
								</span>
							</div>
							<div class="cpanel-meter-track">
								<div class="cpanel-meter-fill <?= get_meter_color($disk_pct) ?>" style="width: 0%;" data-percentage="<?= $disk_pct ?>"></div>
							</div>
						</div>

						<!-- Bandwidth -->
						<div class="cpanel-meter-item">
							<div class="cpanel-meter-header">
								<span class="cpanel-meter-title">
									<i class="fas fa-right-left"></i> <?= _("Monthly Bandwidth") ?>
								</span>
								<span class="cpanel-meter-value">
									<?= humanize_usage_size($u_bw) ?> <?= humanize_usage_measure($u_bw) ?> /
									<?= $bw_quota === "unlimited" ? "∞" : humanize_usage_size($bw_quota) . " " . humanize_usage_measure($bw_quota) ?>
									<?php if ($bw_quota !== "unlimited") { echo " (" . $bw_pct . "%)"; } ?>
								</span>
							</div>
							<div class="cpanel-meter-track">
								<div class="cpanel-meter-fill <?= get_meter_color($bw_pct) ?>" style="width: 0%;" data-percentage="<?= $bw_pct ?>"></div>
							</div>
						</div>

						<!-- Web Domains -->
						<?php if (isset($_SESSION["WEB_SYSTEM"]) && !empty($_SESSION["WEB_SYSTEM"])) { ?>
						<div class="cpanel-meter-item">
							<div class="cpanel-meter-header">
								<span class="cpanel-meter-title">
									<i class="fas fa-globe"></i> <?= _("Web Domains") ?>
								</span>
								<span class="cpanel-meter-value">
									<?= $u_web ?> / <?= $max_web === "unlimited" ? "∞" : $max_web ?>
								</span>
							</div>
							<div class="cpanel-meter-track">
								<div class="cpanel-meter-fill <?= get_meter_color($web_pct) ?>" style="width: 0%;" data-percentage="<?= $web_pct ?>"></div>
							</div>
						</div>
						<?php } ?>

						<!-- MySQL Databases -->
						<?php if (isset($_SESSION["DB_SYSTEM"]) && !empty($_SESSION["DB_SYSTEM"])) { ?>
						<div class="cpanel-meter-item">
							<div class="cpanel-meter-header">
								<span class="cpanel-meter-title">
									<i class="fas fa-database"></i> <?= _("MySQL Databases") ?>
								</span>
								<span class="cpanel-meter-value">
									<?= $u_db ?> / <?= $max_db === "unlimited" ? "∞" : $max_db ?>
								</span>
							</div>
							<div class="cpanel-meter-track">
								<div class="cpanel-meter-fill <?= get_meter_color($db_pct) ?>" style="width: 0%;" data-percentage="<?= $db_pct ?>"></div>
							</div>
						</div>
						<?php } ?>

						<!-- Email Accounts -->
						<?php if (isset($_SESSION["MAIL_SYSTEM"]) && !empty($_SESSION["MAIL_SYSTEM"])) { ?>
						<div class="cpanel-meter-item">
							<div class="cpanel-meter-header">
								<span class="cpanel-meter-title">
									<i class="fas fa-envelope"></i> <?= _("Email Accounts") ?>
								</span>
								<span class="cpanel-meter-value">
									<?= $u_mail ?> / <?= $max_mail === "unlimited" ? "∞" : $max_mail ?>
								</span>
							</div>
							<div class="cpanel-meter-track">
								<div class="cpanel-meter-fill <?= get_meter_color($mail_pct) ?>" style="width: 0%;" data-percentage="<?= $mail_pct ?>"></div>
							</div>
						</div>
						<?php } ?>

						<!-- DNS Zones -->
						<?php if (isset($_SESSION["DNS_SYSTEM"]) && !empty($_SESSION["DNS_SYSTEM"])) { ?>
						<div class="cpanel-meter-item">
							<div class="cpanel-meter-header">
								<span class="cpanel-meter-title">
									<i class="fas fa-book-atlas"></i> <?= _("DNS Zones") ?>
								</span>
								<span class="cpanel-meter-value">
									<?= $u_dns ?> / <?= $max_dns === "unlimited" ? "∞" : $max_dns ?>
								</span>
							</div>
							<div class="cpanel-meter-track">
								<div class="cpanel-meter-fill <?= get_meter_color($dns_pct) ?>" style="width: 0%;" data-percentage="<?= $dns_pct ?>"></div>
							</div>
						</div>
						<?php } ?>

						<!-- Cron Jobs -->
						<?php if (isset($_SESSION["CRON_SYSTEM"]) && !empty($_SESSION["CRON_SYSTEM"])) { ?>
						<div class="cpanel-meter-item">
							<div class="cpanel-meter-header">
								<span class="cpanel-meter-title">
									<i class="fas fa-clock"></i> <?= _("Cron Jobs") ?>
								</span>
								<span class="cpanel-meter-value">
									<?= $u_cron ?> / <?= $max_cron === "unlimited" ? "∞" : $max_cron ?>
								</span>
							</div>
							<div class="cpanel-meter-track">
								<div class="cpanel-meter-fill <?= get_meter_color($cron_pct) ?>" style="width: 0%;" data-percentage="<?= $cron_pct ?>"></div>
							</div>
						</div>
						<?php } ?>

						<!-- Backups -->
						<?php if (isset($_SESSION["BACKUP_SYSTEM"]) && !empty($_SESSION["BACKUP_SYSTEM"])) { ?>
						<div class="cpanel-meter-item">
							<div class="cpanel-meter-header">
								<span class="cpanel-meter-title">
									<i class="fas fa-box-archive"></i> <?= _("Backups") ?>
								</span>
								<span class="cpanel-meter-value">
									<?= $u_backup ?> / <?= $max_backup === "unlimited" ? "∞" : $max_backup ?>
								</span>
							</div>
							<div class="cpanel-meter-track">
								<div class="cpanel-meter-fill <?= get_meter_color($backup_pct) ?>" style="width: 0%;" data-percentage="<?= $backup_pct ?>"></div>
							</div>
						</div>
						<?php } ?>

					</div>
				</div>
			</div>

		</div>

	</div>

</div>
