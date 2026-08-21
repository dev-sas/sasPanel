<div class="cp-auth-wrapper">
	<div class="cp-auth-card">

		<!-- Brand Header -->
		<div class="cp-auth-header">
			<div class="cp-auth-logo">
				<i class="fas fa-key cp-auth-logo-icon" style="color: #10b981;"></i>
				<span class="cp-auth-brand-name">sas<span class="cp-auth-brand-light">Panel</span></span>
			</div>
			<div class="cp-auth-badge-row">
				<span class="cp-auth-edition-badge" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);"><?= _("2FA Recovery") ?></span>
			</div>
			<p class="cp-auth-subtitle">
				<?= _("Enter your username and your emergency 2FA reset code.") ?>
			</p>
		</div>

		<?php if ($success) { ?>
			<div class="cp-auth-alert alert-success" role="alert">
				<i class="fas fa-circle-check"></i>
				<span><?= _("Two-factor authentication has been disabled for your account. You can now log in.") ?></span>
			</div>
			<div class="u-mt20">
				<a href="/login/" class="cp-auth-submit-btn" style="text-decoration: none;">
					<span><?= tohtml(_("Proceed to Login")) ?></span>
					<i class="fas fa-arrow-right"></i>
				</a>
			</div>
		<?php } else { ?>

			<!-- Error Message Alert -->
			<?php if (!empty($error)) { ?>
				<div class="cp-auth-alert alert-danger" role="alert">
					<i class="fas fa-circle-exclamation"></i>
					<span><?= tohtml($error) ?></span>
				</div>
			<?php } ?>

			<!-- Form -->
			<form method="post" action="/reset2fa/" class="cp-auth-form">
				<input type="hidden" name="token" value="<?= tohtml($_SESSION["token"]) ?>">

				<div class="cp-auth-field">
					<label for="user" class="cp-auth-label">
						<i class="fas fa-user"></i> <?= tohtml(_("Username")) ?>
					</label>
					<div class="cp-auth-input-wrap">
						<i class="fas fa-user cp-input-icon"></i>
						<input
							type="text"
							class="cp-auth-input"
							name="user"
							id="user"
							placeholder="<?= _("Enter your username") ?>"
							autocomplete="username"
							required
							autofocus
						>
					</div>
				</div>

				<div class="cp-auth-field">
					<label for="twofa" class="cp-auth-label">
						<i class="fas fa-shield-keyhole"></i> <?= tohtml(_("Emergency 2FA Reset Code")) ?>
					</label>
					<div class="cp-auth-input-wrap">
						<i class="fas fa-key cp-input-icon"></i>
						<input
							type="text"
							class="cp-auth-input"
							name="twofa"
							id="twofa"
							placeholder="Enter recovery code"
							autocomplete="off"
							required
						>
					</div>
				</div>

				<div class="cp-auth-btn-group">
					<button type="submit" class="cp-auth-submit-btn">
						<span><?= tohtml(_("Unlock Account")) ?></span>
						<i class="fas fa-unlock"></i>
					</button>
					<a href="/login/?logout" class="cp-auth-back-btn">
						<i class="fas fa-arrow-left"></i>
						<span><?= tohtml(_("Back to Login")) ?></span>
					</a>
				</div>
			</form>
		<?php } ?>

		<!-- Footer -->
		<div class="cp-auth-footer-info">
			<div class="cp-auth-sec-badge">
				<i class="fas fa-shield-halved"></i>
				<span><?= _("Encrypted Multi-Factor Protocol") ?></span>
			</div>
		</div>

	</div>
</div>
