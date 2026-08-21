<div id="token" token="<?= $_SESSION["token"] ?>"></div>

<?php
// Resolve user values safely
$active_user_plain = !empty($_SESSION["look"]) ? $_SESSION["look"] : ($_SESSION["user"] ?? ($user_plain ?? (isset($user) ? trim($user, "'\"") : "admin")));
$user_info = $panel[$active_user_plain] ?? ($panel[$user_plain] ?? (is_array($panel) ? reset($panel) : []));

$u_disk_val = $user_info["U_DISK"] ?? 0;
$disk_quota_val = $user_info["DISK_QUOTA"] ?? "unlimited";
$u_bw_val = $user_info["U_BANDWIDTH"] ?? 0;
$bw_quota_val = $user_info["BANDWIDTH"] ?? "unlimited";
?>

<header class="app-header cpanel-header">

	<div class="top-bar">
		<div class="container top-bar-inner">

			<!-- Logo & Usage Statistics -->
			<div class="top-bar-left">

				<!-- Logo / Home Button -->
				<a href="/list/dashboard/" class="top-bar-logo cp-brand-link" title="sasPanel">
					<div class="cp-brand-logo">
						<i class="fas fa-cubes-stacked cp-brand-icon"></i>
						<span class="cp-brand-name">sas<span class="cp-brand-name-light">Panel</span></span>
						<span class="cp-edition-badge">cPanel Edition</span>
					</div>
				</a>

				<!-- Usage Statistics Capsule -->
				<div class="top-bar-usage cp-stats-capsule">
					<span class="top-bar-usage-item" title="<?= _("Disk Usage") ?>">
						<i class="fas fa-hard-drive"></i>
						<span><?= humanize_usage_size($u_disk_val) ?> <?= humanize_usage_measure($u_disk_val) ?></span>
						<span class="u-text-dim">/</span>
						<span><?= $disk_quota_val === "unlimited" ? "∞" : humanize_usage_size($disk_quota_val) . " " . humanize_usage_measure($disk_quota_val) ?></span>
					</span>
					<span class="top-bar-usage-divider"></span>
					<span class="top-bar-usage-item" title="<?= _("Bandwidth") ?>">
						<i class="fas fa-right-left"></i>
						<span><?= humanize_usage_size($u_bw_val) ?> <?= humanize_usage_measure($u_bw_val) ?></span>
						<span class="u-text-dim">/</span>
						<span><?= $bw_quota_val === "unlimited" ? "∞" : humanize_usage_size($bw_quota_val) . " " . humanize_usage_measure($bw_quota_val) ?></span>
					</span>
				</div>

			</div>

			<!-- Quick Access Tools, Notifications & User Capsule -->
			<div class="top-bar-right">

				<!-- File Manager Shortcut -->
				<?php if (isset($_SESSION["FILE_MANAGER"]) && !empty($_SESSION["FILE_MANAGER"]) && $_SESSION["FILE_MANAGER"] == "true") { ?>
					<?php if (!($_SESSION["userContext"] === "admin" && $_SESSION["look"] === "admin" && $_SESSION["POLICY_SYSTEM_PROTECTED_ADMIN"] == "yes")) { ?>
						<a title="<?= _("File Manager") ?>" class="top-bar-quick-btn <?= $TAB == "FM" ? "active" : "" ?>" href="/fm/">
							<i class="fas fa-folder-open"></i>
							<span class="cp-btn-label"><?= _("Files") ?></span>
						</a>
					<?php } ?>
				<?php } ?>

				<!-- Web Terminal Shortcut -->
				<?php if (isset($_SESSION["WEB_TERMINAL"]) && !empty($_SESSION["WEB_TERMINAL"]) && $_SESSION["WEB_TERMINAL"] == "true" && $_SESSION["login_shell"] != "nologin") { ?>
					<a title="<?= _("Web Terminal") ?>" class="top-bar-quick-btn <?= $TAB == "TERMINAL" ? "active" : "" ?>" href="/list/terminal/">
						<i class="fas fa-terminal"></i>
						<span class="cp-btn-label"><?= _("Terminal") ?></span>
					</a>
				<?php } ?>

				<!-- WHM Server Settings Shortcut (Admin only) -->
				<?php if (($_SESSION["userContext"] === "admin" && $_SESSION["POLICY_SYSTEM_HIDE_SERVICES"] !== "yes") || $_SESSION["user"] === $_SESSION['ROOT_USER']) { ?>
					<?php if ($_SESSION["userContext"] === "admin" && empty($_SESSION["look"])) { ?>
						<a title="<?= _("WHM Panel") ?>" class="top-bar-quick-btn cp-whm-quick-btn <?= in_array($TAB, ["WHM", "SERVER", "SERVICES"]) ? "active" : "" ?>" href="/list/whm/">
							<i class="fas fa-server"></i>
							<span class="cp-btn-label"><?= _("WHM") ?></span>
						</a>
					<?php } ?>
				<?php } ?>

				<!-- Notifications Bell -->
				<?php
				$impersonatingAdmin = $_SESSION["userContext"] === "admin" && ($_SESSION["look"] !== "" && $active_user_plain == "admin");
				if (!$impersonatingAdmin) { ?>
					<div x-data="notifications" class="top-bar-notifications">
						<button
							x-on:click="toggle()"
							x-bind:class="open && 'active'"
							class="top-bar-quick-btn cp-notif-btn"
							type="button"
							title="<?= _("Notifications") ?>"
						>
							<i
								x-bind:class="{
									'animate__animated animate__swing icon-orange': (!initialized && <?= ($user_info["NOTIFICATIONS"] ?? "no") == "yes" ? "true" : "false" ?>) || notifications.length != 0,
									'fas fa-bell': true
								}"
							></i>
							<span class="u-hidden"><?= _("Notifications") ?></span>
						</button>
						<div
							x-cloak
							x-show="open"
							x-on:click.outside="open = false"
							class="top-bar-notifications-panel"
						>
							<template x-if="!initialized">
								<div class="top-bar-notifications-empty">
									<i class="fas fa-circle-notch fa-spin icon-dim"></i>
									<p><?= _("Loading...") ?></p>
								</div>
							</template>
							<template x-if="initialized && notifications.length == 0">
								<div class="top-bar-notifications-empty">
									<i class="fas fa-bell-slash icon-dim"></i>
									<p><?= _("No notifications") ?></p>
								</div>
							</template>
							<template x-if="initialized && notifications.length > 0">
								<ul>
									<template x-for="notification in notifications" :key="notification.ID">
										<li
											x-bind:id="`notification-${notification.ID}`"
											x-bind:class="notification.ACK && 'unseen'"
											class="top-bar-notification-item"
											x-data="{ open: true }"
											x-show="open"
											x-collapse
										>
											<div class="top-bar-notification-inner">
												<div class="top-bar-notification-header">
													<p x-text="notification.TOPIC" class="top-bar-notification-title"></p>
													<button
														x-on:click="open = false; setTimeout(() => remove(notification.ID), 300);"
														type="button"
														class="top-bar-notification-delete"
														title="<?= _("Delete notification") ?>"
													>
														<i class="fas fa-xmark"></i>
													</button>
												</div>
												<div class="top-bar-notification-content" x-html="notification.NOTICE"></div>
												<p class="top-bar-notification-timestamp">
													<time
														:datetime="`${notification.TIMESTAMP_ISO}`"
														x-bind:title="`${notification.TIMESTAMP_TITLE}`"
														x-text="`${notification.TIMESTAMP_TEXT}`"
													></time>
												</p>
											</div>
										</li>
									</template>
								</ul>
							</template>
							<template x-if="initialized && notifications.length > 2">
								<button
									x-on:click="removeAll()"
									type="button"
									class="top-bar-notifications-delete-all"
								>
									<i class="fas fa-check"></i>
									<?= _("Delete all notifications") ?>
								</button>
							</template>
						</div>
					</div>
				<?php } ?>

				<!-- User Profile & Session Capsule -->
				<div class="cp-user-capsule">
					<a href="/edit/user/?user=<?= htmlspecialchars($active_user_plain) ?>&token=<?= $_SESSION["token"] ?>" class="cp-user-pill" title="<?= _("User Preferences") ?>">
						<div class="cp-user-avatar">
							<i class="fas <?= $_SESSION["userContext"] === "admin" ? "fa-user-shield" : "fa-user" ?>"></i>
						</div>
						<div class="cp-user-text-wrap">
							<span class="cp-user-name"><?= htmlspecialchars($active_user_plain) ?></span>
							<span class="cp-user-role-badge <?= $_SESSION["userContext"] === "admin" ? "badge-admin" : "" ?>">
								<?= htmlspecialchars($_SESSION["userContext"] ?? "user") ?>
							</span>
						</div>
					</a>

					<!-- Logout Button -->
					<?php if (isset($_SESSION["look"]) && !empty($_SESSION["look"])) { ?>
						<a title="<?= _("Return to Admin") ?>" class="cp-logout-btn cp-impersonate-exit" href="/logout/?token=<?= $_SESSION["token"] ?>">
							<i class="fas fa-circle-up"></i>
							<span class="u-hide-mobile"><?= _("Exit") ?></span>
						</a>
					<?php } else { ?>
						<a title="<?= _("Log out") ?>" class="cp-logout-btn" href="/logout/?token=<?= $_SESSION["token"] ?>">
							<i class="fas fa-right-from-bracket"></i>
						</a>
					<?php } ?>
				</div>

			</div>

		</div>
	</div>

	<!-- Modern Navigation Tabs -->
	<nav class="main-menu cp-nav-bar">
		<div class="container cp-nav-container">
			<ul class="main-menu-list cp-nav-tabs">

				<!-- Dashboard Tab -->
				<li class="main-menu-item">
					<a class="main-menu-item-link cp-nav-link <?= ($TAB == "DASHBOARD" || empty($TAB)) ? "active" : "" ?>" href="/list/dashboard/" title="<?= _("cPanel Dashboard") ?>">
						<i class="fas fa-grip cp-nav-icon"></i>
						<span class="cp-nav-text"><?= _("Dashboard") ?></span>
					</a>
				</li>

				<!-- WHM Panel Tab (Admin only) -->
				<?php if ($_SESSION["userContext"] == "admin" && empty($_SESSION["look"])) { ?>
				<li class="main-menu-item">
					<a class="main-menu-item-link cp-nav-link cp-whm-tab <?= $TAB == "WHM" ? "active" : "" ?>" href="/list/whm/" title="<?= _("Web Host Manager") ?>">
						<i class="fas fa-server cp-nav-icon"></i>
						<span class="cp-nav-text"><?= _("WHM Panel") ?></span>
						<span class="cp-nav-badge-pill"><?= _("Root") ?></span>
					</a>
				</li>
				<?php } ?>

				<!-- Users Tab (Admin only) -->
				<?php if ($_SESSION["userContext"] == "admin" && empty($_SESSION["look"])) { ?>
				<li class="main-menu-item">
					<a class="main-menu-item-link cp-nav-link <?= in_array($TAB, ["USER", "LOG"]) ? "active" : "" ?>" href="/list/user/" title="<?= _("User Accounts") ?>">
						<i class="fas fa-users cp-nav-icon"></i>
						<span class="cp-nav-text"><?= _("Users") ?></span>
						<span class="cp-nav-badge-pill"><?= htmlspecialchars($user_info["U_USERS"] ?? "1") ?></span>
					</a>
				</li>
				<?php } ?>

				<!-- Web Tab -->
				<?php if (isset($_SESSION["WEB_SYSTEM"]) && !empty($_SESSION["WEB_SYSTEM"])) { ?>
				<li class="main-menu-item">
					<a class="main-menu-item-link cp-nav-link <?= $TAB == "WEB" ? "active" : "" ?>" href="/list/web/" title="<?= _("Web Domains") ?>">
						<i class="fas fa-globe cp-nav-icon"></i>
						<span class="cp-nav-text"><?= _("Web") ?></span>
						<span class="cp-nav-badge-pill"><?= htmlspecialchars($user_info["U_WEB_DOMAINS"] ?? "0") ?></span>
					</a>
				</li>
				<?php } ?>

				<!-- DNS Tab -->
				<?php if (isset($_SESSION["DNS_SYSTEM"]) && !empty($_SESSION["DNS_SYSTEM"])) { ?>
				<li class="main-menu-item">
					<a class="main-menu-item-link cp-nav-link <?= $TAB == "DNS" ? "active" : "" ?>" href="/list/dns/" title="<?= _("DNS Zones") ?>">
						<i class="fas fa-book-atlas cp-nav-icon"></i>
						<span class="cp-nav-text"><?= _("DNS") ?></span>
						<span class="cp-nav-badge-pill"><?= htmlspecialchars($user_info["U_DNS_DOMAINS"] ?? "0") ?></span>
					</a>
				</li>
				<?php } ?>

				<!-- Mail Tab -->
				<?php if (isset($_SESSION["MAIL_SYSTEM"]) && !empty($_SESSION["MAIL_SYSTEM"])) { ?>
				<li class="main-menu-item">
					<a class="main-menu-item-link cp-nav-link <?= $TAB == "MAIL" ? "active" : "" ?>" href="/list/mail/" title="<?= _("Email Accounts") ?>">
						<i class="fas fa-envelope cp-nav-icon"></i>
						<span class="cp-nav-text"><?= _("Mail") ?></span>
						<span class="cp-nav-badge-pill"><?= htmlspecialchars($user_info["U_MAIL_DOMAINS"] ?? "0") ?></span>
					</a>
				</li>
				<?php } ?>

				<!-- Databases Tab -->
				<?php if (isset($_SESSION["DB_SYSTEM"]) && !empty($_SESSION["DB_SYSTEM"])) { ?>
				<li class="main-menu-item">
					<a class="main-menu-item-link cp-nav-link <?= $TAB == "DB" ? "active" : "" ?>" href="/list/db/" title="<?= _("Databases") ?>">
						<i class="fas fa-database cp-nav-icon"></i>
						<span class="cp-nav-text"><?= _("DB") ?></span>
						<span class="cp-nav-badge-pill"><?= htmlspecialchars($user_info["U_DATABASES"] ?? "0") ?></span>
					</a>
				</li>
				<?php } ?>

				<!-- Cron Tab -->
				<?php if (isset($_SESSION["CRON_SYSTEM"]) && !empty($_SESSION["CRON_SYSTEM"])) { ?>
				<li class="main-menu-item">
					<a class="main-menu-item-link cp-nav-link <?= $TAB == "CRON" ? "active" : "" ?>" href="/list/cron/" title="<?= _("Cron Jobs") ?>">
						<i class="fas fa-clock cp-nav-icon"></i>
						<span class="cp-nav-text"><?= _("Cron") ?></span>
					</a>
				</li>
				<?php } ?>

				<!-- Backups Tab -->
				<?php if (isset($_SESSION["BACKUP_SYSTEM"]) && !empty($_SESSION["BACKUP_SYSTEM"])) { ?>
				<li class="main-menu-item">
					<a class="main-menu-item-link cp-nav-link <?= $TAB == "BACKUP" ? "active" : "" ?>" href="/list/backup/" title="<?= _("Backups") ?>">
						<i class="fas fa-box-archive cp-nav-icon"></i>
						<span class="cp-nav-text"><?= _("Backup") ?></span>
						<span class="cp-nav-badge-pill"><?= htmlspecialchars($user_info["U_BACKUPS"] ?? "0") ?></span>
					</a>
				</li>
				<?php } ?>

				<!-- Server Settings Tab (Admin only) -->
				<?php if (($_SESSION["userContext"] === "admin" && $_SESSION["POLICY_SYSTEM_HIDE_SERVICES"] !== "yes") || $_SESSION["user"] === $_SESSION['ROOT_USER']) { ?>
					<?php if ($_SESSION["userContext"] === "admin" && empty($_SESSION["look"])) { ?>
					<li class="main-menu-item">
						<a class="main-menu-item-link cp-nav-link <?= in_array($TAB, ["SERVER", "IP", "RRD", "FIREWALL", "UPDATES"]) ? "active" : "" ?>" href="/list/server/" title="<?= _("Server Settings") ?>">
							<i class="fas fa-gear cp-nav-icon"></i>
							<span class="cp-nav-text"><?= _("Server") ?></span>
						</a>
					</li>
					<?php } ?>
				<?php } ?>

			</ul>
		</div>
	</nav>

</header>

<main class="app-content cp-app-content">
