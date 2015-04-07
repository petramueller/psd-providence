<?php
require_once(__CA_APP_DIR__ . "/plugins/ShelfMarkGen/models/Label.php");
$labels = $this->getVar("labels");
?>
<div class="sectionBox">
	<form method="post" target="_blank" action="<?php echo __CA_URL_ROOT__; ?>/index.php/find/SearchObjects/printLabels">
	<table id="tudLabelList" class="listtable" width="100%" border="0" cellpadding="0" cellspacing="1">
		<thead>
		<tr>
			<th>
				<?php _p('Shelf Mark'); ?>
			</th>
			<th>
				<?php _p('Print'); ?>
			</th>
		</tr>
		</thead>
		<tbody>
		<?php
		if (sizeof($labels)) {
			$i = -1;
			foreach($labels as $label) {
				$i++;

				?>
				<tr>
					<td>
						<?php print $label->getShelfMark(); ?>
					</td>
					<td>
						<input type="checkbox" class="labelCheckbox" checked name="print[<?php echo $i; ?>][shelfmark]" value="<?php echo $label->getShelfMark(); ?>"/>
					</td>
				</tr>
			<?php
			}
		} else {
			?>
			<tr>
				<td colspan="2" align="center"><?php print _t("No labels to print."); ?></td>
			</tr>
		<?php
		}
		?>
		</tbody>
	</table>
		<input type="hidden" name="_formName" value="caPrintLabelsForm"/>
		<input type="hidden" name="form_timestamp" id="form_timestamp" value="caPrintLabelsForm"/>
		<input type="hidden" name="label_form" value="_pdf_tud_all_copies"/>
		<input type="hidden" name="download" value="1"/>
	<input type="button" id="printButton" value="Print Labels"/>
	<input type="submit" formaction="<?php echo __CA_URL_ROOT__; ?>/index.php/ShelfMarkGen/PrintLabels/Delete" value="Delete from Queue"/>
	</form>
	<iframe style="display: none;" id="hiddenIFrame" name="hiddenIFrame">

	</iframe>
	<script type="text/javascript">
		$(function(){
			var generateSearchExpression, tudUrlRoot = "<?php echo __CA_URL_ROOT__; ?>";
			generateSearchExpression = function(){
				var searchExpression = null;
				$(".labelCheckbox:checked").each(function(){
					if(searchExpression === null){
						searchExpression = "ca_objects.preferred_labels:" + $(this).val();
					} else {
						searchExpression += " OR ca_objects.preferred_labels:" + $(this).val();
					}
					$(this).parents("tr").first().hide();
				});
				return searchExpression;
			};
			$("#printButton").click(function(e){
				var timeStamp = new Date().getTime();
				var loadSecondForm = true;
				$("#hiddenIFrame").load(function(){
					if(loadSecondForm){
						var form2=$("<form/>").attr({
							method: "post",
							action: tudUrlRoot + "/index.php/find/SearchObjects/printLabels",
							target: "hiddenIFrame"
						});
						form2.append($("<input/>").attr({name:"_formName", value:"caPrintLabelsForm"}));
						form2.append($("<input/>").attr({name:"form_timestamp", value:timeStamp + 1}));
						form2.append($("<input/>").attr({name:"label_form", value:"_pdf_tud_all_copies"}));
						form2.append($("<input/>").attr({name:"download", value:"1"}));
						$("body").append(form2);
						form2.submit();
						loadSecondForm = false;
					}
				});
				var form=$("<form/>").attr({
					method: "post",
					action: tudUrlRoot + "/index.php/find/SearchObjects/Index",
					target: "hiddenIFrame"
				});
				form.append($("<input/>").attr({name:"_formName", value:"BasicSearchForm"}));
				form.append($("<input/>").attr({name:"form_timestamp",value:timeStamp}));
				form.append($("<input/>").attr({name:"search",value:generateSearchExpression()}));
				$("body").append(form);
				form.submit();
				//$('#MainPopupIframe').load(function(){
				//	$(this).show();
				//	console.log('laod the iframe')
				//});

//				$('#click').on('click', function(){
//					$('#MainPopupIframe').attr('src', 'foo/org/index');
//				});

//				var timeStamp = new Date().getTime();
//				$("#form_timestamp").val(timeStamp);
//				$.get(tudUrlRoot + "/index.php/find/SearchObjects/Index/reset/preference:persistent_search")
//					.done(function(){
//						$.post(tudUrlRoot + "/index.php/find/SearchObjects/Index", {"_formName" : "BasicSearchForm", "form_timestamp" : timeStamp, "search" : generateSearchExpression() })
//							.done(function() {
//								//window.location.href = tudUrlRoot + "/index.php/find/SearchObjects/Download";
//
///*								$.fileDownload(tudUrlRoot + "/index.php/ShelfMarkGen/PrintLabels/printLabels", { //"/index.php/find/SearchObjects/printLabels"
//									preparingMessageHtml: "We are preparing your report, please wait...",
//									failMessageHtml: "There was a problem generating your report, please try again.",
//									httpMethod: "POST",
//									data: {
//										"_formName": "caPrintLabelsForm",
//										"form_timestamp": timeStamp,
//										"label_form": "_pdf_tud_all_copies",
//										"download": "1"
//									}
//								});*/
//							});
/*						$.post(tudUrlRoot + "/index.php/find/SearchObjects/Index", {"_formName" : "BasicSearchForm", "form_timestamp" : timeStamp, "search" : generateSearchExpression() })
							.done(function(data, a , b) {
								$("body").append("<iframe src='" + data+ "' style='display: none;' ></iframe>");
//								//Download file via AJAX as described here: http://stackoverflow.com/posts/23797348/revisions
//								$.post(tudUrlRoot + "/index.php/find/SearchObjects/printLabels", {"_formName" : "caPrintLabelsForm", "form_timestamp" : timeStamp, "label_form" : "_pdf_tud_all_copies", "download" : "1"}, function(response, status, xhr){
//									var filename = "";
//									var disposition = xhr.getResponseHeader('Content-Disposition');
//									if (disposition && disposition.indexOf('attachment') !== -1) {
//										var filenameRegex = /filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/;
//										var matches = filenameRegex.exec(disposition);
//										if (matches != null && matches[1]) filename = matches[1].replace(/['"]/g, '');
//									}
//
//									var type = xhr.getResponseHeader('Content-Type');
//									var blob = new Blob([response], { type: type });
//
//									if (typeof window.navigator.msSaveBlob !== void 0) {
//										// IE workaround for "HTML7007: One or more blob URLs were revoked by closing the blob for which they were created. These URLs will no longer resolve as the data backing the URL has been freed."
//										window.navigator.msSaveBlob(blob, filename);
//									} else {
//										var URL = window.URL || window.webkitURL;
//										var downloadUrl = URL.createObjectURL(blob);
//
//										if (filename) {
//											// use HTML5 a[download] attribute to specify filename
//											var a = document.createElement("a");
//											// safari doesn't support this yet
//											if (typeof a.download === void 0) {
//												window.location = downloadUrl;
//											} else {
//												a.href = downloadUrl;
//												a.download = filename;
//												document.body.appendChild(a);
//												a.click();
//											}
//										} else {
//											window.location = downloadUrl;
//										}
//
//										setTimeout(function () { URL.revokeObjectURL(downloadUrl); }, 100); // cleanup
//									}
//								});
							});*/
				//});
			});
		});
	</script>
</div>