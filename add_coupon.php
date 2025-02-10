<?php
require 'include/main_head.php';

$coupon_per = ['Create', 'Update', 'Read', 'Delete'];
$lang_ar = load_specific_langauage('ar');
$lang_en = load_specific_langauage('en');
if (isset($_GET['id'])) {
	if ($_SESSION['restatename'] == 'Staff' && !in_array('Update', $coupon_per)) {



		header('HTTP/1.1 401 Unauthorized');
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
} else {
	if ($_SESSION['restatename'] == 'Staff' && !in_array('Write', $coupon_per)) {



		header('HTTP/1.1 401 Unauthorized');
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
								<?= $lang['Coupon_Management'] ?>

							</h3>
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
								<div class="card-header card-header-tabs-line d-flex justify-content-between align-items-center">
									<div></div>
									<div class="card-toolbar">
										<!-- Add any toolbar buttons or icons here -->
										<ul class="nav nav-tabs nav-bold nav-tabs-line">
											<li class="nav-item">
												<a class="nav-link " data-toggle="tab" href="#ar" onclick="changeLanguage('ar')">العربية</a>
											</li>
											<li class="nav-item">
												<a class="nav-link active" data-toggle="tab" href="#en" onclick="changeLanguage('en')">English</a>
											</li>
										</ul>
									</div>
								</div>
								<?php
								if (isset($_GET['id'])) {
									$data = $rstate->query("select * from tbl_coupon where id=" . $_GET['id'] . "")->fetch_assoc();
									$subtitle = json_decode($data['subtitle'], true);
									$ctitle = json_decode($data['ctitle'], true);
									$c_desc = json_decode($data['c_desc'], true);



								?>
									<form method="post" enctype="multipart/form-data">

										<div class="card-body">

											<div class="row">
												<div class="col-md-4 col-lg-4 col-xs-12 col-sm-12">

													<div class="form-group mb-3">
														<label id="Coupon-Image">
															<?= $lang_en['Coupon_Image'] ?>

														</label>

														<input type="hidden" name="type" value="edit_coupon" />

														<input type="hidden" name="id" value="<?php echo $_GET['id']; ?>" />
														<input type="file" name="coupon_img" class="form-control" required>
														<br>
														<img src="<?php echo $data['c_img']; ?>" width="100" height="100" />
													</div>
												</div>

												<div class="col-md-4 col-lg-4 col-xs-12 col-sm-12">

													<div  class="form-group mb-3">
														<label id='Coupon-Expiry-Date'>
															<?= $lang_en['Coupon_Expiry_Date'] ?>

														</label>
														<input type="date" name="expire_date" value="<?php echo $data['cdate'];?>"  class="form-control"  required>

													</div>
												</div>


												<div class="col-md-4 col-lg-4 col-xs-12 col-sm-12">
													<div class="form-group mb-3">

														<label id='Coupon-Code' for="cname">
															<?= $lang_en['Coupon_Code'] ?>

														</label>
														<div class="row">
															<div class="col-md-8 col-lg-8 col-xs-12 col-sm-12">
																<input type="text" id="ccode" class="form-control" onkeypress="return isNumberKey(event)"
																	maxlength="8" name="coupon_code" required value="<?php echo $data['c_title']; ?>" oninput="this.value = this.value.toUpperCase()">
															</div>

															<div class="col-md-4 col-lg-4 col-xs-12 col-sm-12">
																<button id="gen_code" class="btn btn-success"><i class="fa fa-refresh" aria-hidden="true"></i></button>
															</div>
														</div>
													</div>
												</div>

												<div class="tab-content">
													<!-- English Tab -->
													<div class="tab-pane fade show active" id="en">
														<div class="row">

															<div class="col-md-3 col-lg-3 col-xs-12 col-sm-12">
																<div class="form-group mb-3">
																	<label for="cname">
																		<?= $lang_en['Coupon_title'] ?>

																	</label>
																	<input type="text" class="form-control" name="title_en" value="<?php echo $ctitle['en']; ?>" required>
																</div>
															</div>

															<div class="col-md-3 col-lg-3 col-xs-12 col-sm-12">
																<div class="form-group mb-3">
																	<label for="cname">
																		<?= $lang_en['Coupon_subtitle'] ?>

																	</label>
																	<input type="text" class="form-control" name="subtitle_en" value="<?php echo $subtitle['en']; ?>" required>
																</div>
															</div>


															<div class="col-md-6 col-lg-6 col-xs-12 col-sm-12">
																<div class="form-group mb-3">
																	<label for="cname">
																		<?= $lang_en['Coupon_Description'] ?>

																	</label>
																	<textarea class="form-control" rows="5" name="description_en" style="resize: none;"><?php echo $c_desc['en']; ?></textarea>
																</div>
															</div>
														</div>
													</div>
													<!-- Arabic Tab -->
													<div class="tab-pane fade show" id="ar">
														<div class="row">

															<div class="col-md-3 col-lg-3 col-xs-12 col-sm-12">
																<div class="form-group mb-3">
																	<label for="cname">
																		<?= $lang_ar['Coupon_title'] ?>

																	</label>
																	<input type="text" class="form-control" name="title_ar" value="<?php echo $ctitle['ar']; ?>" required>
																</div>
															</div>

															<div class="col-md-3 col-lg-3 col-xs-12 col-sm-12">
																<div class="form-group mb-3">
																	<label for="cname">
																		<?= $lang_ar['Coupon_subtitle'] ?>

																	</label>
																	<input type="text" class="form-control" name="subtitle_ar" value="<?php echo $subtitle['ar']; ?>" required>
																</div>
															</div>


															<div class="col-md-6 col-lg-6 col-xs-12 col-sm-12">
																<div class="form-group mb-3">
																	<label for="cname">
																		<?= $lang_ar['Coupon_Description'] ?>

																	</label>
																	<textarea class="form-control" rows="5" name="description_ar" style="resize: none;"><?php echo $c_desc['ar']; ?></textarea>
																</div>
															</div>
														</div>
													</div>
												</div>

												<div class="col-md-3 col-lg-3 col-xs-12 col-sm-12">
													<div class="form-group mb-3">
														<label id="Coupon-Status" for="inputGroupSelect01"><?= $lang_en['Coupon_Status'] ?></label>
														<select class="form-control" name="status" id="inputGroupSelect01" required>

															<option value="">
																<?= $lang_en['Select_Coupon_Status'] ?>...</option>
															<option value="1" <?php if ($data['status'] == 1) {
																					echo 'selected';
																				} ?>>
																<?= $lang_en['Publish'] ?>
															</option>
															<option value="0" <?php if ($data['status'] == 0) {
																					echo 'selected';
																				} ?>>
																<?= $lang_en['Unpublish'] ?>

															</option>
														</select>
													</div>
												</div>

												<div class="col-md-3 col-lg-3 col-xs-12 col-sm-12">

													<div class="form-group mb-3">
														<label id='Coupon-Min-Order-Amount'>
															<?= $lang_en['Coupon_Min_Order_Amount'] ?>

														</label>
														<input type="text" id="cname" class="form-control numberonly" value="<?php echo $data['min_amt']; ?>" name="min_amt" required>
													</div>
												</div>

												<div class="col-md-6 col-lg-6 col-xs-12 col-sm-12">
													<div class="form-group mb-3">
														<label id='Coupon-Value' for="cname">
															<?= $lang_en['Coupon_Value'] ?>

														</label>
														<input type="text" id="cname" class="form-control numberonly" value="<?php echo $data['c_value']; ?>" name="coupon_val" required>
													</div>
												</div>


											</div>


										</div>
										<div class="card-footer text-left">
											<button  type="submit" class = "btn btn-primary">
												<?= $lang_en['Edit_Coupon'] ?>

											</button>
										</div>
									</form>
								<?php
								} else {
								?>
									<form method="post" enctype="multipart/form-data" onsubmit="return postForm()">

										<div class="card-body">


											<div class="row">
												<div class="col-md-4 col-lg-4 col-xs-12 col-sm-12">

													<div class="form-group mb-3">
														<label id="Coupon-Image">
															<?= $lang_en['Coupon_Image'] ?>

														</label>

														<input type="hidden" name="type" value="add_coupon" />

														<input type="file" name="coupon_img" class="form-control" required>
													</div>
												</div>

												<div class="col-md-4 col-lg-4 col-xs-12 col-sm-12">

													<div class="form-group mb-3">
														<label id='Coupon-Expiry-Date'>
															<?= $lang_en['Coupon_Expiry_Date'] ?>

														</label>
														<input type="date" name="expire_date" class="form-control" required>
													</div>
												</div>


												<div class="col-md-4 col-lg-4 col-xs-12 col-sm-12">
													<div class="form-group mb-3">

														<label id="Coupon-Code" for=" cname">
															<?= $lang_en['Coupon_Code'] ?>

														</label>
														<div class="row">
															<div class="col-md-8 col-lg-8 col-xs-12 col-sm-12">
																<input type="text" id="ccode" class="form-control" onkeypress="return isNumberKey(event)"
																	maxlength="8" name="coupon_code" required oninput="this.value = this.value.toUpperCase()">
															</div>

															<div class="col-md-4 col-lg-4 col-xs-12 col-sm-12">
																<button id="gen_code" class="btn btn-success"><i class="fa fa-refresh" aria-hidden="true"></i></button>
															</div>
														</div>
													</div>
												</div>

												<div class="tab-content">
													<!-- English Tab -->
													<div class="tab-pane fade show active" id="en">
														<div class="row">

															<div class="col-md-3 col-lg-3 col-xs-12 col-sm-12">
																<div class="form-group mb-3">
																	<label for="cname">
																		<?= $lang_en['Coupon_title'] ?>

																	</label>
																	<input type="text" class="form-control" name="title_en" required>
																</div>
															</div>

															<div class="col-md-3 col-lg-3 col-xs-12 col-sm-12">
																<div class="form-group mb-3">
																	<label for="cname">
																		<?= $lang_en['Coupon_subtitle'] ?>

																	</label>
																	<input type="text" class="form-control" name="subtitle_en" required>
																</div>
															</div>


															<div class="col-md-6 col-lg-6 col-xs-12 col-sm-12">
																<div class="form-group mb-3">
																	<label for="cname">
																		<?= $lang_en['Coupon_Description'] ?>

																	</label>
																	<textarea class="form-control" rows="5" name="description_en" style="resize: none;"></textarea>
																</div>
															</div>
														</div>
													</div>
													<!-- Arabic Tab -->
													<div class="tab-pane fade show" id="ar">
														<div class="row">

															<div class="col-md-3 col-lg-3 col-xs-12 col-sm-12">
																<div class="form-group mb-3">
																	<label for="cname">
																		<?= $lang_ar['Coupon_title'] ?>

																	</label>
																	<input type="text" class="form-control" name="title_ar" required>
																</div>
															</div>

															<div class="col-md-3 col-lg-3 col-xs-12 col-sm-12">
																<div class="form-group mb-3">
																	<label for="cname">
																		<?= $lang_ar['Coupon_subtitle'] ?>

																	</label>
																	<input type="text" class="form-control" name="subtitle_ar" required>
																</div>
															</div>


															<div class="col-md-6 col-lg-6 col-xs-12 col-sm-12">
																<div class="form-group mb-3">
																	<label for="cname">
																		<?= $lang_ar['Coupon_Description'] ?>

																	</label>
																	<textarea class="form-control" rows="5" name="description_ar" style="resize: none;"></textarea>
																</div>
															</div>
														</div>
													</div>
												</div>

												<div class="col-md-3 col-lg-3 col-xs-12 col-sm-12">

													<div class="form-group mb-3">
														<label id="Coupon-Status" for="inputGroupSelect01"><?= $lang_en['Coupon_Status'] ?></label>
														<select class="form-control" name="status" id="inputGroupSelect01" required>
															<option value=""><?= $lang_en['Select_Coupon_Status'] ?>...</option>
															<option value="1"><?= $lang_en['Publish'] ?></option>
															<option value="0"><?= $lang_en['Unpublish'] ?></option>
														</select>
													</div>
												</div>

												<div class="col-md-3 col-lg-3 col-xs-12 col-sm-12">

													<div class="form-group mb-3">
														<label id='Coupon-Min-Order-Amount'>
															<?= $lang_en['Coupon_Min_Order_Amount'] ?>

														</label>
														<input type="text" id="cname" class="form-control numberonly" name="min_amt" required>
													</div>
												</div>

												<div class="col-md-6 col-lg-6 col-xs-12 col-sm-12">
													<div class="form-group mb-3">
														<label id='Coupon-Value'  for="cname">
															<?= $lang_en['Coupon_Value'] ?>

														</label>
														<input type="text" id="cname" class="form-control numberonly" name="coupon_val" required>
													</div>
												</div>


											</div>


										</div>
										<div class="card-footer text-left">
											<button type="submit" id='add-coupon' name="icat" class="btn btn-primary">
												<?= $lang_en['Add_Coupon'] ?>
											</button>
										</div>
									</form>
								<?php } ?>
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

<script>
	function changeLanguage(lang) {
		var langData = (lang === "ar") ? langDataAR : langDataEN;

		document.getElementById('Coupon-Value').textContent = langData.Coupon_Value;
		document.getElementById('Coupon-Min-Order-Amount').textContent = langData.Coupon_Min_Order_Amount;
		document.getElementById('Coupon-Status').textContent = langData.Coupon_Status;
		document.getElementById('Coupon-Code').textContent = langData.Coupon_Code;
		document.getElementById('Coupon-Expiry-Date').textContent = langData.Coupon_Expiry_Date;
		document.getElementById('Coupon-Image').textContent = langData.Coupon_Image;

		if (document.getElementById('add-coupon')) {
			document.querySelector('button[type="submit"]').textContent = langData.Add_Coupon;

		} else {
			document.querySelector('button[type="submit"]').textContent = langData.Edit_Coupon;

		}

		const statusSelect = document.getElementById('inputGroupSelect01');
		statusSelect.querySelector('option[value=""]').textContent = langData.Choose;
		statusSelect.querySelector('option[value="1"]').textContent = langData.Publish;
		statusSelect.querySelector('option[value="0"]').textContent = langData.Unpublish;

	}
</script>
<?php
require 'include/footer.php';
?>
</body>

</html>