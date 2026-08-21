<div class="cp-auth-wrapper">
	<div class="cp-auth-card">

		<!-- Brand Header -->
		<div class="cp-auth-header">
			<div class="cp-auth-logo">
				<i class="fas fa-lock-open cp-auth-logo-icon" style="color: #3b82f6;"></i>
				<span class="cp-auth-brand-name">sas<span class="cp-auth-brand-light">Panel</span></span>
			</div>
			<div class="cp-auth-badge-row">
				<span class="cp-auth-edition-badge"><?= _("Set New Password") ?></span>
			</div>
			<p class="cp-auth-subtitle">
				<?= _("Choose a strong new password for your account.") ?>
			</p>
		</div>

		<!-- Error Message Alert -->
		<?php if (!empty($error)) { ?>
			<div class="cp-auth-alert alert-danger" role="alert">
				<i class="fas fa-circle-exclamation"></i>
				<span><?= tohtml($error) ?></span>
			</div>
		<?php } ?>

		<!-- Form -->
		<form method="post" class="cp-auth-form">
			<input type="hidden" name="action" value="confirm">
			<input type="hidden" name="token" value="<?= tohtml($_SESSION["token"]) ?>">
			<input type="hidden" name="user" value="<?= tohtml($_GET["user"]) ?>">
			<input type="hidden" name="code" value="<?= tohtml($_GET["code"]) ?>">

			<div class="cp-auth-field">
				<label for="password" class="cp-auth-label">
					<i class="fas fa-lock"></i> <?= tohtml(_("New Password")) ?>
				</label>
				<div class="cp-auth-input-wrap">
					<i class="fas fa-key cp-input-icon"></i>
					<input
						type="password"
						class="cp-auth-input"
						name="password"
						id="password"
						placeholder="<?= _("Enter strong password") ?>"
						autocomplete="new-password"
						required
						autofocus
					>
				</div>
			</div>

			<div class="cp-auth-field">
				<label for="password_confirm" class="cp-auth-label">
					<i class="fas fa-shield-check"></i> <?= tohtml(_("Confirm Password")) ?>
				</label>
				<div class="cp-auth-input-wrap">
					<i class="fas fa-check-double cp-input-icon"></i>
					<input
						type="password"
						class="cp-auth-input"
						name="password_confirm"
						id="password_confirm"
						placeholder="<?= _("Re-type password") ?>"
						autocomplete="new-password"
						required
					>
				</div>
			</div>

			<div class="cp-auth-btn-group">
				<button type="submit" class="cp-auth-submit-btn">
					<span><?= tohtml(_("Save New Password & Sign In")) ?></span>
					<i class="fas fa-floppy-disk"></i>
				</button>
				<a href="/login/" class="cp-auth-back-btn">
					<i class="fas fa-arrow-left"></i>
					<span><?= tohtml(_("Cancel")) ?></span>
				</a>
			</div>
		</form>

		<!-- Footer -->
		<div class="cp-auth-footer-info">
			<div class="cp-auth-sec-badge">
				<i class="fas fa-shield-halved"></i>
				<span><?= _("Encrypted Credential Storage") ?></span>
			</div>
		</div>

	</div>
</div>
