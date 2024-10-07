<?php

use function OWC\Zaaksysteem\Foundation\Helpers\view;

?>

<ul class="zaak-tabs | nav nav-tabs" id="zaak-tabs" role="tablist">
	<li class="nav-item" role="presentation">
		<button class="zaak-tabs-link | nav-link active" id="current-tab" data-bs-toggle="tab" data-bs-target="#current" type="button" role="tab" aria-controls="current" aria-selected="true">Lopende zaken</button>
	</li>
</ul>

<div class="tab-content" id="myTabContent">
	<div class="tab-pane fade show active" id="current" role="tabpanel" aria-labelledby="current-tab">
		<div class="zaak-card-wrapper">
			<?php foreach ($vars['taken'] as $taak) {
			    echo view('blocks/mijn-taken/tabs-view/taak-card.php', [
			        'title' => $taak->title(),
			        'clarification' => $taak->clarification(),
			        'isActive' => true,
			    ]);
			} ?>
		</div>
	</div>
</div>
