<div class="cp-auth-wrapper">
	<div class="cp-auth-card">

		<!-- Brand Header -->
		<div class="cp-auth-header">
			<div class="cp-auth-logo">
				<i class="fas fa-cubes-stacked cp-auth-logo-icon"></i>
				<span class="cp-auth-brand-name">sas<span class="cp-auth-brand-light">Panel</span></span>
			</div>
			<div class="cp-auth-badge-row">
				<span class="cp-auth-edition-badge">cPanel &bull; WHM Universal Portal</span>
			</div>
			<p class="cp-auth-subtitle">
				<?= _("Sign in to manage your Web Hosting accounts, domains, or WHM server settings.") ?>
			</p>
		</div>

		<!-- Error Message Alert -->
		<?php if (!empty($error)) { ?>
			<div class="cp-auth-alert alert-danger" role="alert">
				<i class="fas fa-circle-exclamation"></i>
				<span><?= tohtml($error) ?></span>
			</div>
		<?php } ?>

		<!-- Login Form Step 1: Username -->
		<form id="login-form" method="post" action="/login/" class="cp-auth-form">
			<input type="hidden" name="token" value="<?= tohtml($_SESSION["token"]) ?>">

			<div class="cp-auth-field">
				<label for="username" class="cp-auth-label">
					<i class="fas fa-user-circle"></i> <?= tohtml(_("Username or Domain")) ?>
				</label>
				<div class="cp-auth-input-wrap">
					<i class="fas fa-user cp-input-icon"></i>
					<input
						type="text"
						class="cp-auth-input"
						name="user"
						id="username"
						placeholder="<?= _("Enter your username") ?>"
						autocomplete="username"
						required
						autofocus
					>
				</div>
			</div>

			<button type="submit" class="cp-auth-submit-btn">
				<span><?= tohtml(_("Continue to Password")) ?></span>
				<i class="fas fa-arrow-right"></i>
			</button>
		</form>

		<!-- Security & Role Info Footer -->
		<div class="cp-auth-footer-info">
			<div class="cp-auth-sec-badge">
				<i class="fas fa-shield-halved"></i>
				<span><?= _("256-bit SSL Encrypted & Brute-force Protected") ?></span>
			</div>
			<div class="cp-auth-access-hint">
				<span class="cp-hint-pill"><i class="fas fa-grip"></i> cPanel User</span>
				<span class="cp-hint-divider">&bull;</span>
				<span class="cp-hint-pill"><i class="fas fa-server"></i> WHM Root / Reseller</span>
			</div>
		</div>

	</div>
</div>
