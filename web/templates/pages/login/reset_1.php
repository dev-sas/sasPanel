<div class="cp-auth-wrapper">
	<div class="cp-auth-card">

		<!-- Brand Header -->
		<div class="cp-auth-header">
			<div class="cp-auth-logo">
				<i class="fas fa-key cp-auth-logo-icon" style="color: #f59e0b;"></i>
				<span class="cp-auth-brand-name">sas<span class="cp-auth-brand-light">Panel</span></span>
			</div>
			<div class="cp-auth-badge-row">
				<span class="cp-auth-edition-badge" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);"><?= _("Password Reset") ?></span>
			</div>
			<p class="cp-auth-subtitle">
				<?= _("Enter your username and email address to receive a secure password recovery code.") ?>
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
		<form method="post" action="/reset/" class="cp-auth-form">
			<input type="hidden" name="token" value="<?= tohtml($_SESSION["token"]) ?>">

			<div class="cp-auth-field">
				<label for="username" class="cp-auth-label">
					<i class="fas fa-user"></i> <?= tohtml(_("Username")) ?>
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

			<div class="cp-auth-field">
				<label for="email" class="cp-auth-label">
					<i class="fas fa-envelope"></i> <?= tohtml(_("Account Email")) ?>
				</label>
				<div class="cp-auth-input-wrap">
					<i class="fas fa-envelope cp-input-icon"></i>
					<input
						type="email"
						class="cp-auth-input"
						name="email"
						id="email"
						placeholder="<?= _("user@domain.com") ?>"
						autocomplete="email"
						required
					>
				</div>
			</div>

			<div class="cp-auth-btn-group">
				<button type="submit" class="cp-auth-submit-btn">
					<span><?= tohtml(_("Send Recovery Code")) ?></span>
					<i class="fas fa-paper-plane"></i>
				</button>
				<a href="/login/?logout" class="cp-auth-back-btn">
					<i class="fas fa-arrow-left"></i>
					<span><?= tohtml(_("Back to Login")) ?></span>
				</a>
			</div>
		</form>

		<!-- Footer -->
		<div class="cp-auth-footer-info">
			<div class="cp-auth-sec-badge">
				<i class="fas fa-shield-halved"></i>
				<span><?= _("Secure Password Self-Recovery") ?></span>
			</div>
		</div>

	</div>
</div>
