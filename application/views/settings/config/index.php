<div class="row">
	<div class="col-12">
		<div class="card leaves">
			<div class="card-header leaves d-flex align-items-center">
				<h5 class="m-0"><?php echo $title ?></h5>
			</div>
			<div class="card-body">
				<div class="callout callout-info m-4 mt-3">
					<h6>Informasi</h6>
					<ul>
						<li>Klik kolom VALUE atau KETERANGAN untuk mengubah data.</li>
					</ul>
				</div>

				<div class="table-responsive">
					<table id="table-configs" class="display"></table>
				</div>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
	$(document).ready(function() {
		initDataTable("#table-configs", {
			ajax: {
				url: "<?php echo base_url("settings/config/get_list") ?>",
			},
			layout: {
				topEnd: {
					buttons: [{
						extend: 'customButton',
						text: '<span class="fa fa-plus" aria-hidden="true"></span> Tambah Konfigurasi',
						url: '<?php echo base_url('settings/config/save') ?>',
						className: 'btn btn-sm btn-outline-success btn-modal',
					}]
				}
			},
			ajaxCellInput: [{
					column: 2,
					type: function(row) {
						return row.category == 8 ? "datepicker" : "textfield";
					},
					url: "<?php echo base_url('settings/config/update_value/value') ?>",
					// Supply both URLs; only the one matching the resolved type is called
					// url: function(resolvedType) {
					// 	// You can also just use a single endpoint and let the server distinguish
					// 	return resolvedType === "textfield" ?
					// 		"<php echo base_url('settings/config/update_value/value') ?>" :
					// 		"<php echo base_url('settings/config/update_date/value') ?>";
					// },
					editable: 1,
				},
				{
					column: 3,
					type: "textfield",
					url: "<?php echo base_url('settings/config/update_value/note') ?>",
					editable: 1,
				},
			],
			columns: [{
					data: null,
					title: "No.",
					className: "dt-center",
					render: function(data, type, row, meta) {
						return meta.row + meta.settings._iDisplayStart + 1;
					}
				},
				{
					data: "key",
					title: "Key",
					className: "text-nowrap",
				},
				{
					data: "value",
					title: "Value",
					className: "text-break-all", // Added this class
				},
				{
					data: "note",
					title: "Keterangan",
				},
			]
		});
	});
</script>