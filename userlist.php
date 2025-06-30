<?php
require 'include/main_head.php';
$per = $_SESSION['permissions'];
$lang_code = load_language_code()["language_code"];

if (!in_array('Read_User_List', $per)) {


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
								User List Management</h3>
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
								<div class="mb-3 row">
									<form id="exportForm" method="get" class="col-sm-12">
										<div class="row justify-content-end align-items-start">
											<input type="hidden" name="type" value="export_user_data" />

											<!-- Export Button -->
											<div class="col-md-2">
												<button type="button" id="exportExcel" class="btn btn-success w-100">
													<i class="fa fa-file-excel-o"></i> Export Excel
												</button>
											</div>
										</div>
									</form>
								</div>
								<div class="table-responsive">
									<!-- Centered Search Form -->
									<div class="row justify-content-center">
										<div class="col-md-8">
											<div class="search-container" style="margin-bottom: 20px;">
												<form method="get" action="">
													<div class="input-group">
														<input type="text" name="search" class="form-control" placeholder="Search by name, email, or mobile..."
															value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
														<div class="input-group-append">
															<button class="btn btn-primary" type="submit">Search</button>
															<?php if (isset($_GET['search']) && !empty($_GET['search'])): ?>
																<a href="?" class="btn btn-secondary">Clear</a>
															<?php endif; ?>
														</div>
													</div>
												</form>
											</div>
										</div>
									</div>

									<!-- User Table -->
									<table class="table" id="users-table">
										<thead>
											<tr>
												<th></th>
												<th>Name</th>
												<th>Email</th>
												<th>Mobile</th>
												<th>Join Date</th>
												<th>Status</th>
												<th>IsOwner</th>
												<th>Property Count</th>
												<?php if (in_array('Update_User_List', $per) || in_array('Delete_User_List', $per)): ?>
													<th><?= $lang['Action'] ?></th>
												<?php endif; ?>
											</tr>
										</thead>
										<tbody>
											<?php
											// Pagination configuration
											$records_per_page = isset($_GET['per_page']) ? (int)$_GET['per_page'] : 10;
											$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
											$page = max($page, 1);

											// Base query - start with status condition
											$query = "SELECT * FROM `tbl_user`";

											// Add search condition if search term exists
											if (isset($_GET['search']) && !empty($_GET['search'])) {
												$search_term = $rstate->real_escape_string($_GET['search']);

												// Check if the search term looks like an email
												if (filter_var($search_term, FILTER_VALIDATE_EMAIL)) {
													// For emails, search exact match
													$query .= " where email = '$search_term'";
												} else {
													// For other searches, use LIKE with wildcards
													$query .= " where (name LIKE '%$search_term%' COLLATE utf8mb4_unicode_ci 
                                                  OR email LIKE '%$search_term%' COLLATE utf8mb4_unicode_ci 
                                                  OR mobile LIKE '%$search_term%' COLLATE utf8mb4_unicode_ci)";
												}
											}

											// Get total number of records
											$count_query = str_replace("SELECT *", "SELECT COUNT(*) as total", $query);
											$count_result = $rstate->query($count_query);
											$total_records = $count_result->fetch_assoc()['total'];
											$total_pages = ceil($total_records / $records_per_page) == 0 ? 1 : ceil($total_records / $records_per_page);
											$page = min($page, $total_pages);

											// Add LIMIT to query for pagination
											$offset = ($page - 1) * $records_per_page;
											$query .= " LIMIT $offset, $records_per_page";
											$stmt = $rstate->query($query);
											$i = $offset + 1;

											if ($total_records > 0) {
												while ($row = $stmt->fetch_assoc()) {
											?>
													<tr>
														<td>
															<?php if (!empty($row['pro_pic'])): ?>
																<img class="rounded-circle" width="35" height="35" src="<?php echo htmlspecialchars($row['pro_pic']); ?>" alt="">
															<?php endif; ?>
														</td>
														<td><?php echo htmlspecialchars($row['name']); ?></td>
														<td><?php echo htmlspecialchars($row['email']); ?></td>
														<td><?php echo htmlspecialchars($row['ccode'] . $row['mobile']); ?></td>
														<td><?php echo htmlspecialchars($row['reg_date']); ?></td>

														<td><span data-id="<?php echo $row['id']; ?>" class="badge badge-success">Active</span></td>

														<td>
															<?php echo ($row['is_owner'] == 1) ? 'Owner' : 'Property'; ?>
														</td>
														<td>
															<?php
															$check_owner = $rstate->query("SELECT * FROM tbl_property WHERE add_user_id=" . (int)$row['id'] . " AND is_deleted = 0")->num_rows;
															echo $check_owner;
															?>
														</td>

														<?php if ((in_array('Update_User_List', $per) || in_array('Delete_User_List', $per)) && $row['status'] == '1'): ?>
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
														<?php else: ?>
															<td></td>
														<?php endif; ?>
													</tr>
											<?php
													$i++;
												}
											} else {
												echo '<tr><td colspan="9" class="text-center">No records found</td></tr>';
											}
											?>
										</tbody>
									</table>

									<!-- Manual Pagination Links -->
									<?php if ($total_records > 0): ?>
										<div class="pagination-container">
											<!-- Per Page Dropdown -->
											<div class="per-page-selector">
												<label for="per_page">Items per page:</label>
												<select id="per_page" name="per_page" onchange="updatePerPage(this.value)">
													<?php
													$per_page_options = [10, 20, 25,  50, 100, 200];
													$current_per_page = isset($_GET['per_page']) ? (int)$_GET['per_page'] : $records_per_page;
													foreach ($per_page_options as $option):
													?>
														<option value="<?php echo $option; ?>" <?php echo $option == $current_per_page ? 'selected' : ''; ?>>
															<?php echo $option; ?>
														</option>
													<?php endforeach; ?>
												</select>
											</div>

											<!-- Pagination Links -->
											<div class="pagination">
												<?php if ($page > 1): ?>
													<a href="?page=1&per_page=<?php echo $current_per_page; ?><?php echo isset($_GET['search']) ? '&search=' . urlencode($_GET['search']) : ''; ?>">First</a>
													<a href="?page=<?php echo $page - 1; ?>&per_page=<?php echo $current_per_page; ?><?php echo isset($_GET['search']) ? '&search=' . urlencode($_GET['search']) : ''; ?>">Previous</a>
												<?php else: ?>
													<span class="disabled">First</span>
													<span class="disabled">Previous</span>
												<?php endif; ?>

												<?php
												$start_page = max(1, $page - 2);
												$end_page = min($total_pages, $page + 2);

												for ($p = $start_page; $p <= $end_page; $p++):
												?>
													<?php if ($p == $page): ?>
														<span class="current"><?php echo $p; ?></span>
													<?php else: ?>
														<a href="?page=<?php echo $p; ?>&per_page=<?php echo $current_per_page; ?><?php echo isset($_GET['search']) ? '&search=' . urlencode($_GET['search']) : ''; ?>"><?php echo $p; ?></a>
													<?php endif; ?>
												<?php endfor; ?>

												<?php if ($page < $total_pages): ?>
													<a href="?page=<?php echo $page + 1; ?>&per_page=<?php echo $current_per_page; ?><?php echo isset($_GET['search']) ? '&search=' . urlencode($_GET['search']) : ''; ?>">Next</a>
													<a href="?page=<?php echo $total_pages; ?>&per_page=<?php echo $current_per_page; ?><?php echo isset($_GET['search']) ? '&search=' . urlencode($_GET['search']) : ''; ?>">Last</a>
												<?php else: ?>
													<span class="disabled">Next</span>
													<span class="disabled">Last</span>
												<?php endif; ?>
											</div>
										</div>
									<?php endif; ?>

									<!-- Results Count -->
									<?php if ($total_records > 0): ?>
										<div class="results-count">
											Showing <?php echo ($offset + 1); ?> to <?php echo min($offset + $current_per_page, $total_records); ?> of <?php echo $total_records; ?> records
											<?php if (isset($_GET['search']) && !empty($_GET['search'])): ?>
												(filtered by "<?php echo htmlspecialchars($_GET['search']); ?>")
											<?php endif; ?>
										</div>
									<?php endif; ?>
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
	function updatePerPage(value) {
		const url = new URL(window.location.href);
		url.searchParams.set('per_page', value);
		// Reset to first page when changing items per page
		url.searchParams.set('page', 1);
		window.location.href = url.toString();
	}
	$('#exportExcel').click(function() {

		// Disable the button to prevent multiple clicks
		var saveButton = $(this);
		saveButton.prop('disabled', true);
		var formData = $('#exportForm').serialize();

		// Here you would typically make an AJAX call to save the data
		$.ajax({
			url: "include/property.php",
			type: "POST",
			data: formData,
			xhrFields: {
				responseType: 'blob' // Important for binary response
			},
			success: function(blob, status, xhr) {
				// Check for filename in headers
				var filename = '';
				var disposition = xhr.getResponseHeader('Content-Disposition');
				if (disposition && disposition.indexOf('attachment') !== -1) {
					var filenameRegex = /filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/;
					var matches = filenameRegex.exec(disposition);
					if (matches != null && matches[1]) {
						filename = matches[1].replace(/['"]/g, '');
					}
				}

				// Create download link
				var a = document.createElement('a');
				var url = window.URL.createObjectURL(blob);
				a.href = url;
				a.download = filename || 'download.csv';
				document.body.appendChild(a);
				a.click();


				$.notify('<i class="fas fa-bell"></i> Export completed successfully!', {
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
				saveButton.removeAttr('disabled');

			},
			error: function() {
				$.notify('<i class="fas fa-exclamation-circle"></i> Error Export Excel Sheet ', {
					type: 'danger',
					allow_dismiss: true,
					delay: 5000
				});
				saveButton.removeAttr('disabled');

			}
		});
	});
</script>
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


<!-- Add this CSS -->
<style>
	.table {
		width: 100%;
		border-collapse: collapse;
		margin-bottom: 20px;
	}

	.table th,
	.table td {
		padding: 8px;
		border: 1px solid #ddd;
	}

	.search-container {
		margin-bottom: 20px;
	}

	.pagination-container {
		display: flex;
		justify-content: space-between;
		align-items: center;
		margin: 20px 0;
	}

	.per-page-selector {
		margin-right: 20px;
	}

	.per-page-selector select {
		padding: 5px;
		border-radius: 4px;
		border: 1px solid #ddd;
	}

	.input-group {
		max-width: 500px;
	}

	.pagination {
		margin-top: 15px;
		display: flex;
		justify-content: center;
		gap: 5px;
		flex-wrap: wrap;
	}

	.pagination a,
	.pagination span {
		padding: 5px 10px;
		border: 1px solid #ddd;
		text-decoration: none;
	}

	.pagination .current {
		background: #007bff;
		color: white;
		border-color: #007bff;
	}

	.pagination .disabled {
		color: #aaa;
		cursor: not-allowed;
	}

	.badge-success {
		background: #28a745;
		color: white;
		padding: 3px 6px;
		border-radius: 4px;
	}

	.badge-danger {
		background: #dc3545;
		color: white;
		padding: 3px 6px;
		border-radius: 4px;
	}

	.results-count {
		margin-top: 10px;
		font-size: 0.9em;
		color: #666;
	}

	.search-container .input-group {
		max-width: 600px;
		margin: 0 auto;
	}

	.pagination {
		display: flex;
		justify-content: center;
		flex-wrap: wrap;
		gap: 5px;
		margin: 20px 0;
	}

	.pagination a,
	.pagination span {
		padding: 5px 10px;
		border: 1px solid #dee2e6;
		text-decoration: none;
	}

	.pagination .current {
		background-color: #007bff;
		color: white;
		border-color: #007bff;
	}

	.pagination .disabled {
		color: #6c757d;
		pointer-events: none;
	}

	.results-count {
		text-align: center;
		color: #6c757d;
		margin-bottom: 20px;
	}
</style>

<!-- Prevent DataTables initialization -->
<script>
	document.addEventListener('DOMContentLoaded', function() {
		if (typeof $.fn.DataTable === 'function') {
			$('#users-table').DataTable({
				paging: false,
				searching: false,
				info: false
			});
		}
	});
</script>

</html>