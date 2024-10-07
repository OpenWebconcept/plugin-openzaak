<?php

use function OWC\Zaaksysteem\Foundation\Helpers\view;

?>

<div class="taak-card-wrapper">
	<?php foreach ($vars['taken'] as $taak) {
	    echo view('blocks/mijn-taken/tabs-view/taak-card.php', [
	        'title' => $taak->title(),
	        'clarification' => $taak->clarification(),
	        'isActive' => true,
	    ]);
	} ?>
</div>
