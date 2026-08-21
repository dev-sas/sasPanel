<div class="cp-auth-wrapper">
	<div class="cp-auth-card">

		<!-- Brand Header -->
		<div class="cp-auth-header">
			<div class="cp-auth-logo">
				<i class="fas fa-envelope-circle-check cp-auth-logo-icon" style="color: #10b981;"></i>
				<span class="cp-auth-brand-name">sas<span class="cp-auth-brand-light">Panel</span></span>
			</div>
			<div class="cp-auth-badge-row">
				<span class="cp-auth-edition-badge" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);"><?= _("Verification Code Sent") ?></span>
			</div>
			<p class="cp-auth-subtitle">
				<?= _("Please check your email and enter the reset code below.") ?>
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
		<form method="get" action="/reset/" class="cp-auth-form">
			<input type="hidden" name="action" value="confirm">
			<input type="hidden" name="token" value="<?= tohtml($_SESSION["token"]) ?>">
			<input type="hidden" name="user" value="<?= tohtml($_GET["user"]) ?>">

			<div class="cp-auth-field">
				<label for="code" class="cp-auth-label">
					<i class="fas fa-shield-keyhole"></i> <?= tohtml(_("Reset Code")) ?>
				</label>
				<div class="cp-auth-input-wrap">
					<i class="fas fa-key cp-input-icon"></i>
					<input
						type="text"
						class="cp-auth-input"
						name="code"
						id="code"
						placeholder="e.g. 12345678"
						required
						autofocus
					>
				</div>
			</div>

			<div class="cp-auth-btn-group">
				<button type="submit" class="cp-auth-submit-btn">
					<span><?= tohtml(_("Verify Code")) ?></span>
					<i class="fas fa-check"></i>
				</button>
				<a href="/reset/" class="cp-auth-back-btn">
					<i class="fas fa-arrow-left"></i>
					<span><?= tohtml(_("Back")) ?></span>
				</a>
			</div>
		</form>

		<!-- Footer -->
		<div class="cp-auth-footer-info">
			<div class="cp-auth-sec-badge">
				<i class="fas fa-clock"></i>
				<span><?= _("Code expires in 15 minutes") ?></span>
			</div>
		</div>

	</div>
</div>
