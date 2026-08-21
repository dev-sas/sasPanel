<?php
$whm_users = $data["users"] ?? [];
$whm_services = $data["services"] ?? [];
$whm_packages = $data["packages"] ?? [];
$whm_ips = $data["ips"] ?? [];
$whm_stats = $data["stats"] ?? [];

$token = $_SESSION["token"] ?? "";
?>

<link rel="stylesheet" href="/css/cpanel.css?<?= JS_LATEST_UPDATE ?>">

<div class="whm-container">

	<!-- Top WHM Hero & Mode Switcher -->
	<div class="whm-hero">
		<div class="whm-hero-left">
			<div class="whm-hero-badge">
				<i class="fas fa-server"></i> WHM Multi-Tenant Manager
			</div>
			<h1 class="whm-hero-title"><?= _("Web Host Manager (WHM)") ?></h1>
			<p class="whm-hero-subtitle">
				<?= _("Manage, create, provision, and administer individual cPanel accounts and hosting packages.") ?>
			</p>
		</div>

		<div class="whm-hero-actions">
			<button type="button" class="button button-primary whm-btn-accent" onclick="document.getElementById('createAccountModal').style.display='flex'">
				<i class="fas fa-user-plus"></i> <?= _("Create New cPanel Account") ?>
			</button>
			<a href="/add/package/" class="button button-secondary">
				<i class="fas fa-box"></i> <?= _("Add Package") ?>
			</a>
			<a href="/list/dashboard/" class="button button-secondary" title="<?= _("Switch to End-User cPanel Dashboard") ?>">
				<i class="fas fa-grip"></i> <?= _("cPanel Mode") ?>
			</a>
		</div>
	</div>

	<!-- WHM Stat Metric Cards -->
	<div class="whm-metrics-grid">

		<div class="whm-stat-card">
			<div class="whm-stat-icon-box stat-icon-accounts">
				<i class="fas fa-users"></i>
			</div>
			<div class="whm-stat-info">
				<span class="whm-stat-label"><?= _("Total Accounts") ?></span>
				<span class="whm-stat-value"><?= $whm_stats["total_accounts"] ?? 0 ?></span>
				<span class="whm-stat-subtext">
					<span class="text-green"><?= $whm_stats["active_accounts"] ?? 0 ?> <?= _("Active") ?></span> &bull; 
					<span class="text-red"><?= $whm_stats["suspended_accounts"] ?? 0 ?> <?= _("Suspended") ?></span>
				</span>
			</div>
		</div>

		<div class="whm-stat-card">
			<div class="whm-stat-icon-box stat-icon-domains">
				<i class="fas fa-earth-americas"></i>
			</div>
			<div class="whm-stat-info">
				<span class="whm-stat-label"><?= _("Hosted Domains") ?></span>
				<span class="whm-stat-value"><?= $whm_stats["total_domains"] ?? 0 ?></span>
				<span class="whm-stat-subtext"><?= _("Active Virtual Hosts") ?></span>
			</div>
		</div>

		<div class="whm-stat-card">
			<div class="whm-stat-icon-box stat-icon-disk">
				<i class="fas fa-hard-drive"></i>
			</div>
			<div class="whm-stat-info">
				<span class="whm-stat-label"><?= _("Disk Space Used") ?></span>
				<span class="whm-stat-value">
					<?= humanize_usage_size($whm_stats["total_disk_used"] ?? 0) ?> <?= humanize_usage_measure($whm_stats["total_disk_used"] ?? 0) ?>
				</span>
				<span class="whm-stat-subtext"><?= _("Across all cPanel accounts") ?></span>
			</div>
		</div>

		<div class="whm-stat-card">
			<div class="whm-stat-icon-box stat-icon-server">
				<i class="fas fa-microchip"></i>
			</div>
			<div class="whm-stat-info">
				<span class="whm-stat-label"><?= _("Server Load") ?></span>
				<span class="whm-stat-value"><?= htmlspecialchars($whm_stats["load_str"] ?? "0.10, 0.08, 0.05") ?></span>
				<span class="whm-stat-subtext text-green"><?= _("Normal Health") ?></span>
			</div>
		</div>

	</div>

	<!-- Main WHM Section: Accounts List & Daemons Grid -->
	<div class="whm-content-grid">

		<!-- Left: Account Manager -->
		<div class="whm-main-section">

			<div class="whm-card">
				<div class="whm-card-header">
					<div class="whm-card-header-left">
						<h2 class="whm-card-title">
							<i class="fas fa-users-gear icon-blue"></i> <?= _("cPanel Accounts Manager") ?>
						</h2>
						<span class="whm-pill-counter"><?= count($whm_users) ?></span>
					</div>

					<div class="whm-search-box">
						<i class="fas fa-magnifying-glass"></i>
						<input type="text" id="whmUserSearch" class="whm-search-input" placeholder="<?= _("Search accounts by user, domain, package...") ?>" autocomplete="off">
					</div>
				</div>

				<div class="whm-card-body u-p0">
					<div class="table-responsive">
						<table class="whm-table" id="whmAccountsTable">
							<thead>
								<tr>
									<th><?= _("User / Owner") ?></th>
									<th><?= _("Package") ?></th>
									<th><?= _("Web / Mail / DB") ?></th>
									<th><?= _("Disk Usage") ?></th>
									<th><?= _("Bandwidth") ?></th>
									<th><?= _("Status") ?></th>
									<th class="text-right"><?= _("Actions") ?></th>
								</tr>
							</thead>
							<tbody>
								<?php foreach ($whm_users as $uname => $u) {
									$is_suspended = ($u["SUSPENDED"] ?? "no") === "yes";
									$u_disk = intval($u["U_DISK"] ?? 0);
									$disk_limit = $u["DISK_QUOTA"] ?? "unlimited";
									$disk_pct = ($disk_limit === "unlimited" || $disk_limit == 0) ? 1 : round(($u_disk / $disk_limit) * 100, 1);
									$pkg_name = $u["PACKAGE"] ?? "default";
									$display_uname = $uname;
									$full_name = !empty($u["NAME"]) ? $u["NAME"] : $uname;
									$contact_email = $u["CONTACT"] ?? "";
								?>
								<tr class="whm-account-row" data-search="<?= strtolower($uname . " " . $full_name . " " . $pkg_name . " " . $contact_email) ?>">
									<td>
										<div class="whm-user-cell">
											<div class="whm-user-avatar <?= $is_suspended ? 'avatar-suspended' : '' ?>">
												<i class="fas <?= $uname === 'admin' ? 'fa-user-shield' : 'fa-user' ?>"></i>
											</div>
											<div class="whm-user-meta">
												<div class="whm-user-primary">
													<a href="/edit/user/?user=<?= urlencode($uname) ?>&token=<?= $token ?>" class="whm-user-link" title="<?= _("Edit Account") ?>">
														<?= htmlspecialchars($uname) ?>
													</a>
													<?php if ($uname === "admin") { ?>
														<span class="whm-tag-root">root</span>
													<?php } ?>
												</div>
												<span class="whm-user-sub"><?= htmlspecialchars($full_name) ?></span>
											</div>
										</div>
									</td>
									<td>
										<a href="/edit/package/?package=<?= urlencode($pkg_name) ?>&token=<?= $token ?>" class="whm-pkg-badge" title="<?= _("Package: ") . htmlspecialchars($pkg_name) ?>">
											<i class="fas fa-box-open"></i> <?= htmlspecialchars($pkg_name) ?>
										</a>
									</td>
									<td>
										<div class="whm-resource-counts">
											<span title="<?= _("Web Domains") ?>"><i class="fas fa-globe icon-blue"></i> <?= $u["U_WEB_DOMAINS"] ?? 0 ?></span>
											<span title="<?= _("Mail Accounts") ?>"><i class="fas fa-envelope icon-cyan"></i> <?= $u["U_MAIL_ACCOUNTS"] ?? 0 ?></span>
											<span title="<?= _("MySQL Databases") ?>"><i class="fas fa-database icon-orange"></i> <?= $u["U_DATABASES"] ?? 0 ?></span>
										</div>
									</td>
									<td>
										<div class="whm-quota-mini">
											<div class="whm-quota-text">
												<?= humanize_usage_size($u_disk) ?> <?= humanize_usage_measure($u_disk) ?>
												<span class="u-text-dim">/ <?= $disk_limit === "unlimited" ? "∞" : humanize_usage_size($disk_limit) . " " . humanize_usage_measure($disk_limit) ?></span>
											</div>
											<div class="whm-meter-mini">
												<div class="whm-meter-fill <?= $disk_pct > 85 ? 'fill-red' : ($disk_pct > 65 ? 'fill-amber' : 'fill-green') ?>" style="width: <?= min(max($disk_pct, 2), 100) ?>%;"></div>
											</div>
										</div>
									</td>
									<td>
										<div class="whm-quota-mini">
											<div class="whm-quota-text">
												<?= humanize_usage_size($u["U_BANDWIDTH"] ?? 0) ?> <?= humanize_usage_measure($u["U_BANDWIDTH"] ?? 0) ?>
											</div>
										</div>
									</td>
									<td>
										<?php if ($is_suspended) { ?>
											<span class="whm-status-badge status-suspended">
												<i class="fas fa-ban"></i> <?= _("Suspended") ?>
											</span>
										<?php } else { ?>
											<span class="whm-status-badge status-active">
												<i class="fas fa-check-circle"></i> <?= _("Active") ?>
											</span>
										<?php } ?>
									</td>
									<td class="text-right">
										<div class="whm-row-actions">
											<!-- 1-Click Login to cPanel -->
											<a href="/login/?loginas=<?= urlencode($uname) ?>&token=<?= $token ?>" class="whm-action-btn btn-cpanel-login" title="<?= _("Log in to cPanel as ") . htmlspecialchars($uname) ?>">
												<i class="fas fa-arrow-right-to-bracket"></i>
												<span><?= _("cPanel") ?></span>
											</a>

											<!-- Edit Account -->
											<a href="/edit/user/?user=<?= urlencode($uname) ?>&token=<?= $token ?>" class="whm-action-btn btn-edit" title="<?= _("Edit Account & Package") ?>">
												<i class="fas fa-pen-to-square"></i>
											</a>

											<!-- Suspend / Unsuspend -->
											<?php if ($uname !== "admin") { ?>
												<?php if ($is_suspended) { ?>
													<a href="/unsuspend/user/?user=<?= urlencode($uname) ?>&token=<?= $token ?>" class="whm-action-btn btn-unsuspend" title="<?= _("Unsuspend Account") ?>">
														<i class="fas fa-play"></i>
													</a>
												<?php } else { ?>
													<a href="/suspend/user/?user=<?= urlencode($uname) ?>&token=<?= $token ?>" class="whm-action-btn btn-suspend" title="<?= _("Suspend Account") ?>" onclick="return confirm('<?= _("Are you sure you want to suspend this user?") ?>');">
														<i class="fas fa-pause"></i>
													</a>
												<?php } ?>

												<!-- Delete User -->
												<a href="/delete/user/?user=<?= urlencode($uname) ?>&token=<?= $token ?>" class="whm-action-btn btn-delete" title="<?= _("Terminate Account") ?>" onclick="return confirm('<?= _("Are you sure you want to PERMANENTLY delete this cPanel account?") ?>');">
													<i class="fas fa-trash-can"></i>
												</a>
											<?php } ?>
										</div>
									</td>
								</tr>
								<?php } ?>
							</tbody>
						</table>
					</div>
				</div>
			</div>

		</div>

		<!-- Right: Quick Tools, Hosting Packages & Server Services -->
		<div class="whm-sidebar-section">

			<!-- Hosting Packages Card -->
			<div class="whm-card">
				<div class="whm-card-header">
					<h3 class="whm-card-title">
						<i class="fas fa-boxes-packing icon-orange"></i> <?= _("Hosting Packages") ?>
					</h3>
					<a href="/add/package/" class="whm-header-link" title="<?= _("Create Package") ?>">
						<i class="fas fa-plus"></i> <?= _("New") ?>
					</a>
				</div>
				<div class="whm-card-body">
					<div class="whm-pkg-list">
						<?php foreach ($whm_packages as $pkg_id => $pkg) { ?>
							<div class="whm-pkg-item">
								<div class="whm-pkg-item-header">
									<span class="whm-pkg-item-name"><?= htmlspecialchars($pkg_id) ?></span>
									<a href="/edit/package/?package=<?= urlencode($pkg_id) ?>&token=<?= $token ?>" class="whm-btn-icon" title="<?= _("Edit Package") ?>">
										<i class="fas fa-gear"></i>
									</a>
								</div>
								<div class="whm-pkg-limits">
									<span><i class="fas fa-hard-drive"></i> <?= $pkg["DISK_QUOTA"] === "unlimited" ? "∞" : humanize_usage_size($pkg["DISK_QUOTA"]) . " " . humanize_usage_measure($pkg["DISK_QUOTA"]) ?></span>
									<span><i class="fas fa-right-left"></i> <?= $pkg["BANDWIDTH"] === "unlimited" ? "∞" : humanize_usage_size($pkg["BANDWIDTH"]) . " " . humanize_usage_measure($pkg["BANDWIDTH"]) ?></span>
									<span><i class="fas fa-globe"></i> <?= $pkg["WEB_DOMAINS"] === "unlimited" ? "∞" : $pkg["WEB_DOMAINS"] ?></span>
									<span><i class="fas fa-database"></i> <?= $pkg["DATABASES"] === "unlimited" ? "∞" : $pkg["DATABASES"] ?></span>
								</div>
							</div>
						<?php } ?>
					</div>
				</div>
			</div>

			<!-- Server Daemons & Status -->
			<div class="whm-card">
				<div class="whm-card-header">
					<h3 class="whm-card-title">
						<i class="fas fa-server icon-green"></i> <?= _("Server Daemons & Services") ?>
					</h3>
					<a href="/list/services/" class="whm-header-link" title="<?= _("View all services") ?>">
						<?= _("View All") ?>
					</a>
				</div>
				<div class="whm-card-body">
					<div class="whm-services-list">
						<?php
						$key_services = ['nginx', 'apache2', 'httpd', 'php-fpm', 'mariadb', 'mysql', 'exim4', 'dovecot', 'named', 'bind9', 'ssh', 'fail2ban', 'iptables'];
						foreach ($whm_services as $srv_name => $srv) {
							$state = $srv["STATE"] ?? "running";
							$is_running = $state === "running";
						?>
							<div class="whm-service-row">
								<div class="whm-service-info">
									<span class="whm-service-dot <?= $is_running ? 'dot-running' : 'dot-stopped' ?>"></span>
									<span class="whm-service-name"><?= htmlspecialchars($srv_name) ?></span>
								</div>
								<div class="whm-service-ctrl">
									<a href="/restart/service/?srv=<?= urlencode($srv_name) ?>&token=<?= $token ?>" class="whm-srv-btn" title="<?= _("Restart ") . htmlspecialchars($srv_name) ?>">
										<i class="fas fa-rotate-right"></i>
									</a>
								</div>
							</div>
						<?php } ?>
					</div>
				</div>
			</div>

		</div>

	</div>

</div>

<!-- 1-Click "Create New cPanel Account" Modal -->
<div id="createAccountModal" class="whm-modal-overlay" style="display: none;">
	<div class="whm-modal-card">
		<div class="whm-modal-header">
			<h3 class="whm-modal-title">
				<i class="fas fa-user-plus icon-blue"></i> <?= _("Create New cPanel Account") ?>
			</h3>
			<button type="button" class="whm-modal-close" onclick="document.getElementById('createAccountModal').style.display='none'">
				<i class="fas fa-xmark"></i>
			</button>
		</div>
		<div class="whm-modal-body">
			<form action="/add/user/" method="post" id="whmAddUserForm">
				<input type="hidden" name="token" value="<?= $token ?>">
				<input type="hidden" name="ok" value="Add">

				<div class="form-row-2">
					<div class="whm-form-group">
						<label class="form-label" for="whm_username"><?= _("Username") ?> <span class="text-red">*</span></label>
						<input type="text" name="v_username" id="whm_username" class="form-control" required placeholder="e.g. clientname" pattern="[a-zA-Z0-9_\-]+" maxlength="32">
					</div>

					<div class="whm-form-group">
						<label class="form-label" for="whm_email"><?= _("Contact Email") ?> <span class="text-red">*</span></label>
						<input type="email" name="v_email" id="whm_email" class="form-control" required placeholder="user@domain.com">
					</div>
				</div>

				<div class="form-row-2">
					<div class="whm-form-group">
						<label class="form-label" for="whm_password"><?= _("Password") ?> <span class="text-red">*</span></label>
						<div class="input-group">
							<input type="password" name="v_password" id="whm_password" class="form-control" required placeholder="Secure Password">
							<button type="button" class="btn-input-action" onclick="generateWhmPassword()" title="<?= _("Generate Strong Password") ?>">
								<i class="fas fa-key"></i>
							</button>
						</div>
					</div>

					<div class="whm-form-group">
						<label class="form-label" for="whm_package"><?= _("Hosting Package") ?></label>
						<select name="v_package" id="whm_package" class="form-select">
							<?php foreach ($whm_packages as $pkg_id => $pkg) { ?>
								<option value="<?= htmlspecialchars($pkg_id) ?>" <?= $pkg_id === 'default' ? 'selected' : '' ?>>
									<?= htmlspecialchars($pkg_id) ?> (<?= $pkg["DISK_QUOTA"] === "unlimited" ? "∞ Disk" : humanize_usage_size($pkg["DISK_QUOTA"]) . " " . humanize_usage_measure($pkg["DISK_QUOTA"]) ?>)
								</option>
							<?php } ?>
						</select>
					</div>
				</div>

				<div class="whm-form-group">
					<label class="form-label" for="whm_name"><?= _("Contact / Business Name") ?></label>
					<input type="text" name="v_name" id="whm_name" class="form-control" placeholder="e.g. Acme Corporation">
				</div>

				<div class="whm-form-footer">
					<button type="button" class="button button-secondary" onclick="document.getElementById('createAccountModal').style.display='none'">
						<?= _("Cancel") ?>
					</button>
					<button type="submit" class="button button-primary whm-btn-accent">
						<i class="fas fa-check"></i> <?= _("Provision cPanel Account") ?>
					</button>
				</div>
			</form>
		</div>
	</div>
</div>

<script>
	// Live Account Search Filter in WHM
	document.getElementById('whmUserSearch')?.addEventListener('input', function(e) {
		const term = e.target.value.trim().toLowerCase();
		const rows = document.querySelectorAll('.whm-account-row');
		rows.forEach(row => {
			const searchData = row.getAttribute('data-search') || '';
			if (term === '' || searchData.includes(term)) {
				row.style.display = '';
			} else {
				row.style.display = 'none';
			}
		});
	});

	// Password Generator for WHM Account Modal
	function generateWhmPassword() {
		const chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()_+";
		let pwd = "";
		for (let i = 0; i < 16; i++) {
			pwd += chars.charAt(Math.floor(Math.random() * chars.length));
		}
		const input = document.getElementById('whm_password');
		if (input) {
			input.type = 'text';
			input.value = pwd;
		}
	}
</script>
