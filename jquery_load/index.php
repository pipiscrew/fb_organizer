<button id="btn_ad_fees_new" type="button" class="btn btn-primary">	New</button>

<div id="x_details">
</div>

<script>
		$('#btn_x_new').on('click', function(e) {
			$("#loading").height($('body').height());
			$("#loading").show();

			$("#x_details").load('x_details.php', function() {
				$("#loading").hide();
				$("#x").hide(); //the grid div
				
				$("#x_details").show(); //the table add new elements
			});
		});
</script>
