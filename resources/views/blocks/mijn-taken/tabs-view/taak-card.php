<?php
$isActive = $vars['isActive'] ?? false;
$title = $vars['title'] ?? '';
$clarification = $vars['clarification'] ?? '';
$date = $vars['date'] ?? '';
$link = $vars['link'] ?? '';
?>
<div class="taak-card <?php echo $isActive ? 'active' : ''; ?>">
	<svg class="taak-card-svg" width="385" height="200" viewBox="0 0 385 200" fill="#F1F1F1" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="none">
		<path d="M260.532 17.39L249.736 1.32659C249.179 0.497369 248.246 0 247.246 0H3C1.34315 0 0 1.34314 0 3V197C0 198.657 1.34315 200 3.00001 200H381.485C383.142 200 384.485 198.657 384.485 197V109.358V21.7166C384.485 20.0597 383.142 18.7166 381.485 18.7166H263.022C262.023 18.7166 261.089 18.2192 260.532 17.39Z" />
	</svg>
	<h2 class="taak-card-title">
		<a href="<?= $link ?: '#'; ?>">
			<?php echo $title ?>
		</a>
	</h2>
	<small><?php echo $clarification; ?></small>
	<div class="taak-card-footer">
		<?php if ($date) : ?>
			<div class="taak-card-date">
				<?php echo $date; ?>
			</div>
		<?php endif; ?>
		<svg class="taak-card-arrow" width="24" height="24" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
			<path d="M12.2929 5.29289C12.6834 4.90237 13.3166 4.90237 13.7071 5.29289L19.7071 11.2929C19.8946 11.4804 20 11.7348 20 12C20 12.2652 19.8946 12.5196 19.7071 12.7071L13.7071 18.7071C13.3166 19.0976 12.6834 19.0976 12.2929 18.7071C11.9024 18.3166 11.9024 17.6834 12.2929 17.2929L16.5858 13L5 13C4.44772 13 4 12.5523 4 12C4 11.4477 4.44772 11 5 11L16.5858 11L12.2929 6.70711C11.9024 6.31658 11.9024 5.68342 12.2929 5.29289Z" />
		</svg>
	</div>
</div>
