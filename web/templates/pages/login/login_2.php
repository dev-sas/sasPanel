<div class="cp-auth-wrapper">
	<div class="cp-auth-card">

		<!-- Brand Header -->
		<div class="cp-auth-header">
			<div class="cp-auth-logo">
				<i class="fas fa-shield-halved cp-auth-logo-icon" style="color: #10b981;"></i>
				<span class="cp-auth-brand-name">sas<span class="cp-auth-brand-light">Panel</span></span>
			</div>
			<div class="cp-auth-badge-row">
				<span class="cp-auth-edition-badge" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">Two-Factor Authentication</span>
			</div>
			<p class="cp-auth-subtitle">
				<?= _("Enter the 6-digit verification code from your Authenticator App.") ?>
			</p>
		</div>

		<!-- Error Message Alert -->
		<?php if (!empty($error)) { ?>
			<div class="cp-auth-alert alert-danger" role="alert">
				<i class="fas fa-circle-exclamation"></i>
				<span><?= tohtml($error) ?></span>
			</div>
		<?php } ?>

		<!-- Login Form Step 3: 2FA Token -->
		<form id="login-form" method="post" action="/login/" class="cp-auth-form">
			<input type="hidden" name="token" value="<?= tohtml($_SESSION["token"]) ?>">

			<div class="cp-auth-field">
				<div class="cp-auth-label-row">
					<label for="twofa" class="cp-auth-label">
						<i class="fas fa-mobile-screen-button"></i> <?= tohtml(_("2FA Security Token")) ?>
					</label>
					<a class="cp-auth-link" href="/reset2fa/">
						<?= tohtml(_("Lost token?")) ?>
					</a>
				</div>
				<div class="cp-auth-input-wrap">
					<i class="fas fa-shield cp-input-icon"></i>
					<input
						type="text"
						class="cp-auth-input"
						name="twofa"
						id="twofa"
						placeholder="123456"
						autocomplete="one-time-code"
						required
						autofocus
						style="letter-spacing: 0.2em; font-size: 1.25rem; font-weight: 700; text-align: center;"
					>
				</div>
			</div>

			<div class="cp-auth-btn-group">
				<button type="submit" class="cp-auth-submit-btn">
					<span><?= tohtml(_("Verify & Sign In")) ?></span>
					<i class="fas fa-check-double"></i>
				</button>
				<a href="/login/?logout" class="cp-auth-back-btn">
					<i class="fas fa-arrow-left"></i>
					<span><?= tohtml(_("Back")) ?></span>
				</a>
			</div>
		</form>

		<!-- Security Footer -->
		<div class="cp-auth-footer-info">
			<div class="cp-auth-sec-badge">
				<i class="fas fa-lock"></i>
				<span><?= _("Multi-Factor Protected Authentication") ?></span>
			</div>
		</div>

	</div>
</div>
