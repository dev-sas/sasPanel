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
				<?= _("Welcome back,") ?> <strong class="text-white"><?= tohtml($_SESSION["login"]["username"]) ?></strong>!
			</p>
		</div>

		<!-- Error Message Alert -->
		<?php if (!empty($error)) { ?>
			<div class="cp-auth-alert alert-danger" role="alert">
				<i class="fas fa-circle-exclamation"></i>
				<span><?= tohtml($error) ?></span>
			</div>
		<?php } ?>

		<!-- Login Form Step 2: Password -->
		<form id="login-form" method="post" action="/login/" class="cp-auth-form">
			<input type="hidden" name="token" value="<?= tohtml($_SESSION["token"]) ?>">

			<div class="cp-auth-field">
				<div class="cp-auth-label-row">
					<label for="password" class="cp-auth-label">
						<i class="fas fa-lock"></i> <?= tohtml(_("Password")) ?>
					</label>
					<?php if ($_SESSION["POLICY_SYSTEM_PASSWORD_RESET"] !== "no") { ?>
						<a class="cp-auth-link" href="/reset/">
							<?= tohtml(_("Forgot password?")) ?>
						</a>
					<?php } ?>
				</div>
				<div class="cp-auth-input-wrap">
					<i class="fas fa-key cp-input-icon"></i>
					<input
						type="password"
						class="cp-auth-input"
						name="password"
						id="password"
						placeholder="<?= _("Enter your password") ?>"
						autocomplete="current-password"
						required
						autofocus
					>
					<button type="button" class="cp-pwd-toggle" onclick="togglePasswordVisibility('password', this)" title="<?= _("Show/Hide Password") ?>">
						<i class="fas fa-eye"></i>
					</button>
				</div>
			</div>

			<div class="cp-auth-btn-group">
				<button type="submit" class="cp-auth-submit-btn">
					<span><?= tohtml(_("Sign In")) ?></span>
					<i class="fas fa-arrow-right-to-bracket"></i>
				</button>
				<a href="/login/?<?= tohtml(http_build_query(["logout" => 'true'])) ?>" class="cp-auth-back-btn">
					<i class="fas fa-arrow-left"></i>
					<span><?= tohtml(_("Back")) ?></span>
				</a>
			</div>
		</form>

		<!-- Security & Role Info Footer -->
		<div class="cp-auth-footer-info">
			<div class="cp-auth-sec-badge">
				<i class="fas fa-shield-halved"></i>
				<span><?= _("256-bit SSL Encrypted & Brute-force Protected") ?></span>
			</div>
		</div>

	</div>
</div>

<script>
	function togglePasswordVisibility(inputId, btn) {
		const input = document.getElementById(inputId);
		if (!input) return;
		const icon = btn.querySelector('i');
		if (input.type === 'password') {
			input.type = 'text';
			if (icon) {
				icon.classList.remove('fa-eye');
				icon.classList.add('fa-eye-slash');
			}
		} else {
			input.type = 'password';
			if (icon) {
				icon.classList.remove('fa-eye-slash');
				icon.classList.add('fa-eye');
			}
		}
	}
</script>
