<footer class="app-footer">
	<div class="container">
		<p>
			<a href="https://devsas.shop" class="app-footer-link" target="_blank">
				<?= !empty($_SESSION["APP_NAME"]) ? htmlentities($_SESSION["APP_NAME"]) : "sasPanel" ?>
			</a>
			v<?= $_SESSION["VERSION"] ?? "1.0" ?> &bull; Developed by <a href="https://devsas.shop" class="app-footer-link" target="_blank">Md. Sajibul Alom Sajon</a>
		</p>
	</div>
</footer>

