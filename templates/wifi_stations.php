<?php if (empty($networks) && ($wifi_client_enable[0] == '1')): ?>
  <div class="col-md-6 ml-6">
    <p class="lead text-center"><?php echo _('No Wifi stations found') ?></p>
    <p class="text-center"><?php echo _("Click 'Rescan' to search for nearby Wifi stations.") ?></p>
  </div>
<?php elseif (empty($networks) && ($wifi_client_enable[0] == '0')):?>
	<div class="col-md-6 ml-6">
    	<p class="lead text-center"><?php echo _('Please enable WiFi client first.') ?></p>
  	</div>
<?php endif ?>

<?php if (!empty($connected) && $wifi_client_enable[0]): ?>
<h4 class="h-underlined my-3"><?php echo _("Connected") ?></h4>
<div class="card-grid">
	<?php foreach ($connected as $network) : ?>
	<?php $index = isset($network['index']) ? $network['index'] : -1; ?>
	<?php echo renderTemplate("wifi_stations/network", compact('network', 'index')) ?>
	<?php $index++; ?>
	<?php endforeach ?>
</div>
<?php endif ?>

<?php if (!empty($known) && $wifi_client_enable[0]): ?>
<h4 class="h-underlined my-3"><?php echo _("Known") ?></h4>
<div class="card-grid">
	<?php foreach ($known as $network) : ?>
	<?php $index = isset($network['index']) ? $network['index'] : -1; ?>
	<?php echo renderTemplate("wifi_stations/network", compact('network', 'index')) ?>
	<?php $index++; ?>
	<?php endforeach ?>
</div>
<?php endif ?>

<?php if (!empty($nearby) && $wifi_client_enable[0]): ?>
<h4 class="h-underlined my-3"><?php echo _("Nearby") ?></h4>
<div class="card-grid">
	<?php foreach ($nearby as $network) : ?>
	<?php $index = isset($network['index']) ? $network['index'] : -1; ?>
	<?php echo renderTemplate("wifi_stations/network", compact('network', 'index')) ?>
	<?php $index++; ?>
	<?php endforeach ?>
</div>
<?php endif ?>
