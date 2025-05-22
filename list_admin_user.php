<?php
require 'include/main_head.php';
$per = $_SESSION['permissions'];
$lang_code = load_language_code()["language_code"];

if (!in_array('Read_Admin_User', $per)) {


?>
	<style>
		.loader-wrapper {
			display: none;
		}
	</style>
<?php
	require 'auth.php';
	exit();
}
?>
<!-- Loader ends-->
<!-- page-wrapper Start-->
<div class="page-wrapper compact-wrapper" id="pageWrapper">
	<!-- Page Header Start-->
	<?php
	require 'include/inside_top.php';
	?>
	<!-- Page Header Ends                              -->
	<!-- Page Body Start-->
	<div class="page-body-wrapper">
		<!-- Page Sidebar Start-->
		<?php
		require 'include/sidebar.php';
		?>
		<!-- Page Sidebar Ends-->
		<div class="page-body">
			<div class="container-fluid">
				<div class="page-title">
					<div class="row">
						<div class="col-6">
							<h3>
								Admin User List Management</h3>
						</div>
						<div class="col-6">

						</div>
					</div>
				</div>
			</div>
			<!-- Container-fluid starts-->
			<div class="container-fluid">
				<div class="row">

					<div class="col-sm-12">
						<div class="card">
							<div class="card-body">
								<div class="table-responsive">
									<table class="display" id="basic-1">
										<thead>
											<tr>
												<th>User Name</th>
												<th>Email</th>
												<th>User Type</th>
												<th>Join Date</th>

												
												<th>Status</th>

																						<?php
												if (in_array('Update_Admin_User', $per) || in_array('Delete_Admin_User', $per)) {
												?>

													<th>
														<?= $lang['Action'] ?></th>
												<?php
												}
												?>


											</tr>
										</thead>
										<tbody>
											<?php
											$stmt = $rstate->query("SELECT * FROM `tbl_user`");
											$i = 0;
											while ($row = $stmt->fetch_assoc()) {
												$i = $i + 1;
											?>
												<tr>
													
													<td><?php echo $row['name']; ?></td>
													<td><?php echo $row['email']; ?></td>
													<td><?php echo $row['email']; ?></td>
													<td><?php echo $row['reg_date']; ?></td>

													<?php if ($row['status'] == 1) { ?>

														<td><span data-id="<?php echo $row['id']; ?>"  class=" badge badge-success">Active</span></td>
													<?php } else { ?>

														<td>
															<span data-id="<?php echo $row['id']; ?>"  class="badge  badge-danger">Not Active</span>
														</td>
													<?php } ?>




												

													<?php
												if (in_array('Update_Admin_User', $per) || in_array('Delete_Admin_User', $per)) {
													?>
														<td style="white-space: nowrap; width: 15%;">
															<div class="tabledit-toolbar btn-toolbar" style="text-align: left;">
																<div class="btn-group btn-group-sm" style="float: none;">

																	<button type="button"
																		style="background: none; border: none; padding: 0; cursor: pointer;"
																		data-toggle="modal"
																		data-target="#approveModal"
																		data-id="<?php echo $row['id']; ?>"
																		data-status="<?php echo $row['status']; ?>"
																		title="Delete">
																		<svg width="30" height="30" viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg">
																			<rect width="30" height="30" rx="15" fill="#FF6B6B" />
																			<path d="M10 10L20 20M20 10L10 20" stroke="#FFFFFF" stroke-width="2" />
																		</svg>
																	</button>
																</div>
															</div>
														</td>
													<?php } ?>

												</tr>
											<?php } ?>

										</tbody>
									</table>
								</div>
							</div>

						</div>


					</div>







				</div>
			</div>
			<!-- Container-fluid Ends-->
		</div>
		<!-- footer start-->

	</div>
</div>
<!-- latest jquery-->

<!-- Confirmation Modal -->
<div class="modal fade" id="approveModal" tabindex="-1" role="dialog" aria-labelledby="approveModalLabel" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="approveModalLabel">Confirm delete</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<form id="approveForm">

			<input type="hidden" id="approveId" name="id">
			<input type="hidden" id="status" name="status">
			<input type="hidden" name="type" value="delete_user" />
			</form>
			<div class="modal-body">
				Are you sure you want to delete this user?
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">No</button>
				<button type="button" class="btn btn-primary" id="confirmApproveBtn">Yes, Delete</button>
			</div>
		</div>
	</div>
</div>
<?php
require 'include/footer.php';
?>
</body>
<script>
	$('#approveModal').on('show.bs.modal', function(event) {
		var button = $(event.relatedTarget);
		var id = button.data('id');
		var status = button.data('status');

		var modal = $(this);
		modal.find('#approveId').val(id);
		modal.find('#status').val(status);
	});
	// When save button is clicked
	$('#confirmApproveBtn').click(function() {


		var formData = $('#approveForm').serialize();

		// Here you would typically make an AJAX call to save the data
		$.ajax({
			url: "include/property.php",
			type: "POST",
			data: formData,
			success: function(response) {
				let res = JSON.parse(response); // Parse the JSON response

				if (res.ResponseCode === "200" && res.Result === "true") {
					$('#approveModal').removeClass('show');
					$('#approveModal').css('display', 'none');
					$('.modal-backdrop').remove(); // Remove the backdrop

					// Display notification
					$.notify('<i class="fas fa-bell"></i>' + res.title, {
						type: 'theme',
						allow_dismiss: true,
						delay: 2000,
						showProgressbar: true,
						timer: 300,
						animate: {
							enter: 'animated fadeInDown',
							exit: 'animated fadeOutUp',
						},
					});

					// Redirect after a delay if an action URL is provided
					if (res.action) {
						setTimeout(function() {
							window.location.href = res.action;
						}, 2000);
					}
				} else {
					alert("'Error saving user deletion.");
				}
			}
		});
	});
</script>


</html>