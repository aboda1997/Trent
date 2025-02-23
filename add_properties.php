<?php
require 'include/main_head.php';

$coupon_per = ['Create', 'Update', 'Read', 'Delete'];
$lang_code = load_language_code()["language_code"];

if (isset($_GET['id'])) {
	if ($_SESSION['restatename'] == 'Staff' && !in_array('Update', $property_per)) {



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
	if ($_SESSION['restatename'] == 'Staff' && !in_array('Write', $property_per)) {



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
								Property Management</h3>
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
								$data = $rstate->query("select * from tbl_property where id=" . $_GET['id'] . "")->fetch_assoc();
								$title = json_decode($data['title'], true);
								$address = json_decode($data['address'], true);
								$description = json_decode($data['description'], true);
								$guest_rules = json_decode($data['guest_rules'], true);
								$compound_name = json_decode($data['compound_name'], true);
								$city = json_decode($data['city'], true);
								$floor = json_decode($data['floor'], true);


							?>
								<form method="post" enctype="multipart/form-data">

									<div class="card-body">
										<div id="alert-container" class="mb-3" style="display: none;">
											<div class="alert alert-danger" id="alert-message"></div>
										</div>
										<div class="tab-content">
											<!-- English Tab -->
											<div class="tab-pane fade show active" id="en">
												<div class="row">

													<div class="col-md-3 col-lg-3 col-xs-12 col-sm-12">
														<div class="form-group mb-3">
															<label>
																<?= $lang_en['Property_Title'] ?>

															</label>
															<input
																value="<?php echo $title['en']; ?>"
																type="text" class="form-control" name="title_en" required="">
															<div class="invalid-feedback" id="title_en_feedback" style="display: none;">
																<?= $lang_en['prop_title'] ?>

															</div>
														</div>
													</div>
													<div class="col-md-6 col-lg-6 col-xs-12 col-sm-12">
														<div class="form-group mb-3">
															<label>
																<?= $lang_en['Full_Address'] ?>

															</label>
															<input

																value="<?php echo $address['en']; ?>"
																type="text" class="form-control" name="address_en" required="">
															<div class="invalid-feedback" id="address_en_feedback" style="display: none;">
																<?= $lang_en['prop_address'] ?>

															</div>
														</div>
													</div>
													<div class="col-md-4 col-lg-4 col-xs-12 col-sm-12">
														<div class="form-group mb-3">
															<label for="cname">
																<?= $lang_en['Property_Description'] ?>

															</label>
															<textarea class="form-control" rows="10" name="description_en" style="resize: none;">
															<?php echo trim($description['en']); ?>
															</textarea>
														</div>
													</div>

													<div class="col-md-4 col-lg-4 col-xs-12 col-sm-12">
														<div class="form-group mb-3">
															<label for="cname">
																<?= $lang_en['Guest_Rules'] ?>

															</label>
															<textarea class="form-control" rows="10" name="guest_rules_en" required="" style="resize: none;">
															<?php echo trim($guest_rules['en']); ?>

															</textarea>
															<div class="invalid-feedback" id="prop_guest_rules_en_feedback" style="display: none;">
																<?= $lang_en['prop_guest_rules'] ?>

															</div>
														</div>
													</div>



													<div class="col-md-4 col-lg-4 col-xs-12 col-sm-12">
														<div class="form-group mb-3">
															<label>
																<?= $lang_en['Compound_Name'] ?>

															</label>
															<input
																value="<?php echo $compound_name['en']; ?>"

																type="text" class="form-control" name="compound_name_en" required="">
															<div class="invalid-feedback" id="prop_compound_name_en_feedback" style="display: none;">
																<?= $lang_en['prop_compound_name'] ?>

															</div>
														</div>
													</div>
													<div class="col-md-4 col-lg-4 col-xs-12 col-sm-12">
														<div class="form-group mb-3">
															<label>
																<?= $lang_en['Floor'] ?>
															</label>
															<input
																value="<?php echo $floor['en']; ?>"

																type="text" class="form-control" name="floor_en" required="">
															<div class="invalid-feedback" id="floor_en_feedback" style="display: none;">
																<?= $lang_en['prop_floor'] ?>

															</div>
														</div>
													</div>

													<div class="col-md-4 col-lg-4 col-xs-12 col-sm-12">
														<div class="form-group mb-3">
															<label>
																<?= $lang_en['City'] ?>

															</label>

															<input
																value="<?php echo $city['en']; ?>"

																type="text" class="form-control" name="city_en" required="">
															<div class="invalid-feedback" id="city_en_feedback" style="display: none;">
																<?= $lang_en['prop_city'] ?>

															</div>
														</div>
													</div>

												</div>
											</div>
											<div class="tab-pane fade show" id="ar">
												<div class="row">

													<div class="row">

														<div class="col-md-3 col-lg-3 col-xs-12 col-sm-12">
															<div class="form-group mb-3">
																<label>
																	<?= $lang_ar['Property_Title'] ?>

																</label>
																<input
																	value="<?php echo $title['ar']; ?>"

																	type="text" class="form-control" name="title_ar" required="">
																<div class="invalid-feedback" id="title_ar_feedback" style="display: none;">
																	<?= $lang_ar['prop_title'] ?>

																</div>
															</div>
														</div>
														<div class="col-md-6 col-lg-6 col-xs-12 col-sm-12">
															<div class="form-group mb-3">
																<label>
																	<?= $lang_ar['Full_Address'] ?>

																</label>
																<input
																	value="<?php echo $address['ar']; ?>"

																	type="text" class="form-control" name="address_ar" required="">
																<div class="invalid-feedback" id="address_ar_feedback" style="display: none;">
																	<?= $lang_ar['prop_address'] ?>

																</div>
															</div>
														</div>
														<div class="col-md-4 col-lg-4 col-xs-12 col-sm-12">
															<div class="form-group mb-3">
																<label for="cname">
																	<?= $lang_ar['Property_Description'] ?>

																</label>
																<textarea class="form-control" rows="10" name="description_ar" style="resize: none;">

																<?php echo trim($description['ar']); ?>

																</textarea>
															</div>
														</div>

														<div class="col-md-4 col-lg-4 col-xs-12 col-sm-12">
															<div class="form-group mb-3">
																<label for="cname">
																	<?= $lang_ar['Guest_Rules'] ?>

																</label>
																<textarea class="form-control" rows="10" name="guest_rules_ar" required="" style="resize: none;">
																<?php echo trim($guest_rules['ar']); ?>

																</textarea>
																<div class="invalid-feedback" id="prop_guest_rules_ar_feedback" style="display: none;">
																	<?= $lang_ar['prop_guest_rules'] ?>

																</div>
															</div>
														</div>



														<div class="col-md-4 col-lg-4 col-xs-12 col-sm-12">
															<div class="form-group mb-3">
																<label>
																	<?= $lang_ar['Compound_Name'] ?>

																</label>
																<input
																	value="<?php echo $compound_name['ar']; ?>"

																	type="text" class="form-control" name="compound_name_ar" required="">
																<div class="invalid-feedback" id="prop_compound_name_ar_feedback" style="display: none;">
																	<?= $lang_ar['prop_compound_name'] ?>

																</div>
															</div>
														</div>
														<div class="col-md-4 col-lg-4 col-xs-12 col-sm-12">
															<div class="form-group mb-3">
																<label>
																	<?= $lang_ar['Floor'] ?>
																</label>
																<input
																	value="<?php echo $floor['ar']; ?>"

																	type="text" class="form-control" name="floor_ar" required="">
																<div class="invalid-feedback" id="floor_ar_feedback" style="display: none;">
																	<?= $lang_ar['prop_floor'] ?>

																</div>
															</div>
														</div>

														<div class="col-md-4 col-lg-4 col-xs-12 col-sm-12">
															<div class="form-group mb-3">
																<label>
																	<?= $lang_ar['City'] ?>

																</label>

																<input
																	value="<?php echo $city['ar']; ?>"

																	type="text" class="form-control" name="city_ar" required="">
																<div class="invalid-feedback" id="city_ar_feedback" style="display: none;">
																	<?= $lang_ar['prop_city'] ?>

																</div>
															</div>
														</div>

													</div>
												</div>
											</div>


											<div class="row">

												<div class="col-md-3 col-lg-3 col-xs-12 col-sm-12">
													<div class="form-group mb-3">
														<label id="prop_image">
															<?= $lang_en['Property_Image'] ?>


														</label>
														<input type="file" class="form-control" name="prop_img[]" accept=".jpg, .jpeg, .png, .gif" multiple />

														<input type="hidden" name="type" value="edit_property" />
														<input type="hidden" name="id" value="<?php echo $_GET['id']; ?>" />

														<br>
														<img src="<?php echo $data['image']; ?>" width="100" height="100" />
														<div class="invalid-feedback" id="prop_img_feedback" style="display: none;">
															<?= $lang_en['prop_img'] ?>

														</div>

													</div>
												</div>

												<div class="col-md-3 col-lg-3 col-xs-12 col-sm-12">
													<div class="form-group mb-3">
														<label id="prop_video">
															<?= $lang_en['Property_video'] ?>


														</label>
														<input type="file" class="form-control" name="prop_video" id="video" accept="video/mp4,video/avi,video/mov,video/mkv">

														<div class="invalid-feedback" id="prop_video_feedback" style="display: none;">
															<?= $lang_en['prop_video'] ?>

														</div>
													</div>
												</div>


												<div class="col-md-3 col-lg-3 col-xs-12 col-sm-12">
													<div class="form-group mb-3">
														<label id="prop_price">
															<?= $lang_en['Property_Price_Per_Night'] ?>

														</label>
														<input
															value="<?php echo $data['price']; ?>"
															type="text" class="form-control numberonly" id="price" name="prop_price" required="">
														<div class="invalid-feedback" id="prop_price_feedback" style="display: none;">
															<?= $lang_en['prop_price'] ?>

														</div>
													</div>
												</div>


												<div class="col-md-3 col-lg-3 col-xs-12 col-sm-12">
													<div class="form-group mb-3">
														<label id="prop_security">
															<?= $lang_en['security_deposit'] ?>

														</label>
														<input
															value="<?php echo $data['security_deposit']; ?>"
															type="text" class="form-control numberonly" id="price" name="prop_security" required="">
														<div class="invalid-feedback" id="security_deposit_feedback" style="display: none;">
															<?= $lang_en['prop_security'] ?>

														</div>
													</div>
												</div>

												<div class="col-md-3 col-lg-3 col-xs-12 col-sm-12">
													<div class="form-group mb-3">
														<label id="plable">
															<?= $lang_en['min_days'] ?>

														</label>
														<input
															value="<?php echo $data['min_days']; ?>"

															type="text" class="form-control numberonly" id="min_day" name="min_day">
													</div>
												</div>

												<div class="col-md-3 col-lg-3 col-xs-12 col-sm-12">
													<div class="form-group mb-3">
														<label id="plable">
															<?= $lang_en['max_days'] ?>

														</label>
														<input
															value="<?php echo $data['max_days']; ?>"

															type="text" class="form-control numberonly" id="max_day" name="max_day">
													</div>
												</div>



												<div class="col-md-3 col-lg-3 col-xs-12 col-sm-12">

													<div class="form-group mb-3">
														<label id="property-status" for="inputGroupSelect01"><?= $lang_en['Property_Status'] ?></label>
														<select class="form-control" name="status" id="inputGroupSelect01" required>
															<option value=""><?= $lang_en['Select_property_Status'] ?>...</option>
															<option value="1"
																<?php if ($data['status'] == 1) {
																	echo 'selected';
																} ?>><?= $lang_en['Publish'] ?></option>
															<option value="0"
																<?php if ($data['status'] == 0) {
																	echo 'selected';
																} ?>><?= $lang_en['Unpublish'] ?></option>
														</select>
														<div class="invalid-feedback" id="status_feedback" style="display: none;">
															<?= $lang_en['property_status'] ?>

														</div>
													</div>
												</div>


												<div class="col-md-3 col-lg-3 col-xs-12 col-sm-12">
													<div class="form-group mb-3">
														<label id="limitlable">
															<?= $lang_en['total_allowed_persons'] ?>

															?</label>
														<input
															value="<?php echo $data['plimit']; ?>"

															type="text" class="form-control numberonly" id="plimit" name="plimit" required="">
														<div class="invalid-feedback" id="limit_feedback" style="display: none;">
															<?= $lang_en['property_limit'] ?>

														</div>
													</div>
												</div>



												<div class="col-md-6 col-lg-6 col-xs-12 col-sm-12">
													<div class="form-group mb-3">
														<label id="prop_facility">
															<?= $lang_en['Select_Property_Facility'] ?>

														</label>
														<select

															name="facility[]" id="product" class=" form-control" multiple required>

															<?php
															$zone = $rstate->query("select * from tbl_facility");
															while ($row = $zone->fetch_assoc()) {
																$title = json_decode($row['title'], true);
																$isSelected = in_array($row['id'],  explode(',', $data['facility'])) ? 'selected' : '';

															?>
																<option value="<?php echo $row['id']; ?>"
																	<?php echo $isSelected; ?>><?php echo $title[$lang_code]; ?></option>
															<?php
															}
															?>
														</select>
														<div class="invalid-feedback" id="facility_feedback" style="display: none;">
															<?= $lang_en['prop_facility'] ?>

														</div>
													</div>
												</div>

												<div class="col-md-4 col-lg-4 col-xs-12 col-sm-12">
													<div class="form-group mb-3">
														<label id="propmap">
															<?= $lang_en['google_map_url'] ?>

														</label>
														<input
															value="<?php echo $data['google_maps_url']; ?>"

															type="text" class="form-control" name="mapurl" required="">
														<div class="invalid-feedback" id="mapurl_feedback" style="display: none;">
															<?= $lang_en['prop_mapurl'] ?>

														</div>
													</div>
												</div>

												<div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
													<div class="row">
														<div class="col-md-3 col-lg-3 col-xs-12 col-sm-12">
															<div class="form-group mb-3">
																<label id="prop_beds">
																	<?= $lang_en['Total_Beds'] ?>

																</label>
																<input
																	value="<?php echo $data['beds']; ?>"

																	type="text" class="form-control numberonly" name="beds" required="">
																<div class="invalid-feedback" id="beds_feedback" style="display: none;">
																	<?= $lang_en['prop_beds'] ?>

																</div>
															</div>
														</div>

														<div class="col-md-3 col-lg-3 col-xs-12 col-sm-12">
															<div class="form-group mb-3">
																<label id="prop_bathroom">
																	<?= $lang_en['Total_Bathroom'] ?>

																</label>
																<input
																	value="<?php echo $data['bathroom']; ?>"

																	type="text" class="form-control numberonly" name="bathroom" required="">
																<div class="invalid-feedback" id="bathroom_feedback" style="display: none;">
																	<?= $lang_en['prop_bathroom'] ?>

																</div>
															</div>
														</div>

														<div class="col-md-3 col-lg-3 col-xs-12 col-sm-12">
															<div class="form-group mb-3">
																<label id="prop_sqft">
																	<?= $lang_en['Property_SQFT'] ?>

																	.</label>
																<input
																	value="<?php echo $data['sqrft']; ?>"

																	type="text" class="form-control numberonly" name="sqft" required="">
																<div class="invalid-feedback" id="sqft_feedback" style="display: none;">
																	<?= $lang_en['prop_sqft'] ?>

																</div>
															</div>
														</div>





														<div class="col-md-4 col-lg-4 col-xs-12 col-sm-12">
															<div class="form-group mb-3">
																<label id="prop_type">
																	<?= $lang_en['Select_Property_Type'] ?>

																</label>
																<select name="ptype" id="propt_type" class=" form-control" required>
																	<option value="" disabled selected>
																		<?= $lang_en['Select_Property_Type'] ?>

																	</option>
																	<?php
																	$zone = $rstate->query("select * from tbl_category");

																	while ($row = $zone->fetch_assoc()) {
																		$title = json_decode($row['title'], true);
																		$isSelected = in_array($row['id'],  explode(',', $data['ptype'])) ? 'selected' : '';

																	?>
																		<option value="<?php echo $row['id']; ?>"
																			<?php echo $isSelected; ?>><?php echo $title[$lang_code]; ?></option>
																	<?php
																	}
																	?>
																</select>
																<div class="invalid-feedback" id="prop_type_feedback" style="display: none;">
																	<?= $lang_en['prop_type'] ?>

																</div>
															</div>
														</div>



														<div class="col-md-4 col-lg-4 col-xs-12 col-sm-12">
															<div class="form-group mb-3">
																<label id="prop_governemnt">
																	<?= $lang_en['Select_Government'] ?>

																</label>
																<select name="pgov" id="government" class=" form-control" required>
																	<option value="" disabled selected>

																		<?= $lang_en['Select_Government'] ?>

																	</option>
																	<?php
																	$zone = $rstate->query("select * from tbl_government");
																	while ($row = $zone->fetch_assoc()) {
																		$title = json_decode($row['name'], true);
																		$isSelected = in_array($row['id'],  explode(',', $data['government'])) ? 'selected' : '';

																	?>
																		<option value="<?php echo $row['id']; ?>"
																			<?php echo $isSelected; ?>><?php echo $title[$lang_code]; ?></option>
																	<?php
																	}
																	?>
																</select>
																<div class="invalid-feedback" id="government_feedback" style="display: none;">
																	<?= $lang_en['prop_governemnt'] ?>

																</div>
															</div>
														</div>

														<div class="col-md-4 col-lg-4 col-xs-12 col-sm-12">
															<div class="form-group mb-3">
																<label id="prop_owner">
																	<?= $lang_en['Select_Owner'] ?>

																</label>
																<select
																	name="propowner" id="owner" class=" form-control" required>
																	<option value="" disabled selected>
																		<?= $lang_en['Select_Owner'] ?>
																	</option>
																	<?php
																	$zone = $rstate->query("select * from tbl_user");
																	while ($row = $zone->fetch_assoc()) {
																		$title = $row['name'];
																		$isSelected = in_array($row['id'],  explode(',', $data['add_user_id'])) ? 'selected' : '';

																	?>
																		<option value="<?php echo $row['id']; ?>"
																			<?php echo $isSelected; ?>><?php echo $title ?></option>
																	<?php
																	}
																	?>
																</select>
																<div class="invalid-feedback" id="owner_feedback" style="display: none;">
																	<?= $lang_en['prop_owner'] ?>

																</div>
															</div>
														</div>


													</div>
												</div>
											</div>
										</div>
										<div class="card-footer text-left">
											<button onclick="return validateForm(true)" type="submit" class="btn btn-primary">
												<?= $lang_en['Edit_Property'] ?>

											</button>
										</div>
								</form>
							<?php
							} else {
							?>
								<form method="post" enctype="multipart/form-data">

									<div class="card-body">
										<div id="alert-container" class="mb-3" style="display: none;">
											<div class="alert alert-danger" id="alert-message"></div>
										</div>
										<div class="tab-content">
											<!-- English Tab -->
											<div class="tab-pane fade show active" id="en">
												<div class="row">

													<div class="col-md-3 col-lg-3 col-xs-12 col-sm-12">
														<div class="form-group mb-3">
															<label>
																<?= $lang_en['Property_Title'] ?>

															</label>
															<input type="text" class="form-control" name="title_en" required="">
															<div class="invalid-feedback" id="title_en_feedback" style="display: none;">
																<?= $lang_en['prop_title'] ?>

															</div>
														</div>
													</div>
													<div class="col-md-6 col-lg-6 col-xs-12 col-sm-12">
														<div class="form-group mb-3">
															<label>
																<?= $lang_en['Full_Address'] ?>

															</label>
															<input type="text" class="form-control" name="address_en" required="">
															<div class="invalid-feedback" id="address_en_feedback" style="display: none;">
																<?= $lang_en['prop_address'] ?>

															</div>
														</div>
													</div>
													<div class="col-md-4 col-lg-4 col-xs-12 col-sm-12">
														<div class="form-group mb-3">
															<label for="cname">
																<?= $lang_en['Property_Description'] ?>

															</label>
															<textarea class="form-control" rows="10" name="description_en" style="resize: none;"></textarea>
														</div>
													</div>

													<div class="col-md-4 col-lg-4 col-xs-12 col-sm-12">
														<div class="form-group mb-3">
															<label for="cname">
																<?= $lang_en['Guest_Rules'] ?>

															</label>
															<textarea class="form-control" rows="10" name="guest_rules_en" required="" style="resize: none;"></textarea>
															<div class="invalid-feedback" id="prop_guest_rules_en_feedback" style="display: none;">
																<?= $lang_en['prop_guest_rules'] ?>

															</div>
														</div>
													</div>



													<div class="col-md-4 col-lg-4 col-xs-12 col-sm-12">
														<div class="form-group mb-3">
															<label>
																<?= $lang_en['Compound_Name'] ?>

															</label>
															<input type="text" class="form-control" name="compound_name_en" required="">
															<div class="invalid-feedback" id="prop_compound_name_en_feedback" style="display: none;">
																<?= $lang_en['prop_compound_name'] ?>

															</div>
														</div>
													</div>
													<div class="col-md-4 col-lg-4 col-xs-12 col-sm-12">
														<div class="form-group mb-3">
															<label>
																<?= $lang_en['Floor'] ?>
															</label>
															<input type="text" class="form-control" name="floor_en" required="">
															<div class="invalid-feedback" id="floor_en_feedback" style="display: none;">
																<?= $lang_en['prop_floor'] ?>

															</div>
														</div>
													</div>

													<div class="col-md-4 col-lg-4 col-xs-12 col-sm-12">
														<div class="form-group mb-3">
															<label>
																<?= $lang_en['City'] ?>

															</label>

															<input type="text" class="form-control" name="city_en" required="">
															<div class="invalid-feedback" id="city_en_feedback" style="display: none;">
																<?= $lang_en['prop_city'] ?>

															</div>
														</div>
													</div>

												</div>
											</div>
											<div class="tab-pane fade show" id="ar">
												<div class="row">

													<div class="row">

														<div class="col-md-3 col-lg-3 col-xs-12 col-sm-12">
															<div class="form-group mb-3">
																<label>
																	<?= $lang_ar['Property_Title'] ?>

																</label>
																<input type="text" class="form-control" name="title_ar" required="">
																<div class="invalid-feedback" id="title_ar_feedback" style="display: none;">
																	<?= $lang_ar['prop_title'] ?>

																</div>
															</div>
														</div>
														<div class="col-md-6 col-lg-6 col-xs-12 col-sm-12">
															<div class="form-group mb-3">
																<label>
																	<?= $lang_ar['Full_Address'] ?>

																</label>
																<input type="text" class="form-control" name="address_ar" required="">
																<div class="invalid-feedback" id="address_ar_feedback" style="display: none;">
																	<?= $lang_ar['prop_address'] ?>

																</div>
															</div>
														</div>
														<div class="col-md-4 col-lg-4 col-xs-12 col-sm-12">
															<div class="form-group mb-3">
																<label for="cname">
																	<?= $lang_ar['Property_Description'] ?>

																</label>
																<textarea class="form-control" rows="10" name="description_ar" style="resize: none;"></textarea>
															</div>
														</div>

														<div class="col-md-4 col-lg-4 col-xs-12 col-sm-12">
															<div class="form-group mb-3">
																<label for="cname">
																	<?= $lang_ar['Guest_Rules'] ?>

																</label>
																<textarea class="form-control" rows="10" name="guest_rules_ar" required="" style="resize: none;"></textarea>
																<div class="invalid-feedback" id="prop_guest_rules_ar_feedback" style="display: none;">
																	<?= $lang_ar['prop_guest_rules'] ?>

																</div>
															</div>
														</div>



														<div class="col-md-4 col-lg-4 col-xs-12 col-sm-12">
															<div class="form-group mb-3">
																<label>
																	<?= $lang_ar['Compound_Name'] ?>

																</label>
																<input type="text" class="form-control" name="compound_name_ar" required="">
																<div class="invalid-feedback" id="prop_compound_name_ar_feedback" style="display: none;">
																	<?= $lang_ar['prop_compound_name'] ?>

																</div>
															</div>
														</div>
														<div class="col-md-4 col-lg-4 col-xs-12 col-sm-12">
															<div class="form-group mb-3">
																<label>
																	<?= $lang_ar['Floor'] ?>
																</label>
																<input type="text" class="form-control" name="floor_ar" required="">
																<div class="invalid-feedback" id="floor_ar_feedback" style="display: none;">
																	<?= $lang_ar['prop_floor'] ?>

																</div>
															</div>
														</div>

														<div class="col-md-4 col-lg-4 col-xs-12 col-sm-12">
															<div class="form-group mb-3">
																<label>
																	<?= $lang_ar['City'] ?>

																</label>

																<input type="text" class="form-control" name="city_ar" required="">
																<div class="invalid-feedback" id="city_ar_feedback" style="display: none;">
																	<?= $lang_ar['prop_city'] ?>

																</div>
															</div>
														</div>

													</div>
												</div>
											</div>


											<div class="row">

												<div class="col-md-3 col-lg-3 col-xs-12 col-sm-12">
													<div class="form-group mb-3">
														<label id="prop_image">
															<?= $lang_en['Property_Image'] ?>


														</label>
														<input type="file" class="form-control" name="prop_img[]" required="" accept=".jpg, .jpeg, .png, .gif" multiple />

														<input type="hidden" name="type" value="add_property" />
														<div class="invalid-feedback" id="prop_img_feedback" style="display: none;">
															<?= $lang_en['prop_img'] ?>

														</div>

													</div>
												</div>

												<div class="col-md-3 col-lg-3 col-xs-12 col-sm-12">
													<div class="form-group mb-3">
														<label id="prop_video">
															<?= $lang_en['Property_video'] ?>


														</label>
														<input type="file" class="form-control" name="prop_video" id="video" accept="video/mp4,video/avi,video/mov,video/mkv">

														<input type="hidden" name="type" value="add_property" />
														<div class="invalid-feedback" id="prop_video_feedback" style="display: none;">
															<?= $lang_en['prop_video'] ?>

														</div>
													</div>
												</div>


												<div class="col-md-3 col-lg-3 col-xs-12 col-sm-12">
													<div class="form-group mb-3">
														<label id="prop_price">
															<?= $lang_en['Property_Price_Per_Night'] ?>

														</label>
														<input type="text" class="form-control numberonly" id="price" name="prop_price" required="">
														<div class="invalid-feedback" id="prop_price_feedback" style="display: none;">
															<?= $lang_en['prop_price'] ?>

														</div>
													</div>
												</div>


												<div class="col-md-3 col-lg-3 col-xs-12 col-sm-12">
													<div class="form-group mb-3">
														<label id="prop_security">
															<?= $lang_en['security_deposit'] ?>

														</label>
														<input type="text" class="form-control numberonly" id="price" name="prop_security" required="">
														<div class="invalid-feedback" id="security_deposit_feedback" style="display: none;">
															<?= $lang_en['prop_security'] ?>

														</div>
													</div>
												</div>

												<div class="col-md-3 col-lg-3 col-xs-12 col-sm-12">
													<div class="form-group mb-3">
														<label id="plable">
															<?= $lang_en['min_days'] ?>

														</label>
														<input type="text" class="form-control numberonly" id="min_day" name="min_day">
													</div>
												</div>

												<div class="col-md-3 col-lg-3 col-xs-12 col-sm-12">
													<div class="form-group mb-3">
														<label id="plable">
															<?= $lang_en['max_days'] ?>

														</label>
														<input type="text" class="form-control numberonly" id="max_day" name="max_day">
													</div>
												</div>



												<div class="col-md-3 col-lg-3 col-xs-12 col-sm-12">

													<div class="form-group mb-3">
														<label id="property-status" for="inputGroupSelect01"><?= $lang_en['Property_Status'] ?></label>
														<select class="form-control" name="status" id="inputGroupSelect01" required>
															<option value=""><?= $lang_en['Select_property_Status'] ?>...</option>
															<option value="1"><?= $lang_en['Publish'] ?></option>
															<option value="0"><?= $lang_en['Unpublish'] ?></option>
														</select>
														<div class="invalid-feedback" id="status_feedback" style="display: none;">
															<?= $lang_en['property_status'] ?>

														</div>
													</div>
												</div>


												<div class="col-md-3 col-lg-3 col-xs-12 col-sm-12">
													<div class="form-group mb-3">
														<label id="limitlable">
															<?= $lang_en['total_allowed_persons'] ?>

															?</label>
														<input type="text" class="form-control numberonly" id="plimit" name="plimit" required="">
														<div class="invalid-feedback" id="limit_feedback" style="display: none;">
															<?= $lang_en['property_limit'] ?>

														</div>
													</div>
												</div>



												<div class="col-md-6 col-lg-6 col-xs-12 col-sm-12">
													<div class="form-group mb-3">
														<label id="prop_facility">
															<?= $lang_en['Select_Property_Facility'] ?>

														</label>
														<select

															name="facility[]" id="product" class=" form-control" multiple required>

															<?php
															$zone = $rstate->query("select * from tbl_facility");
															while ($row = $zone->fetch_assoc()) {
																$title = json_decode($row['title'], true);

															?>
																<option value="<?php echo $row['id']; ?>"><?php echo $title[$lang_code]; ?></option>
															<?php
															}
															?>
														</select>
														<div class="invalid-feedback" id="facility_feedback" style="display: none;">
															<?= $lang_en['prop_facility'] ?>

														</div>
													</div>
												</div>

												<div class="col-md-4 col-lg-4 col-xs-12 col-sm-12">
													<div class="form-group mb-3">
														<label id="propmap">
															<?= $lang_en['google_map_url'] ?>

														</label>
														<input type="text" class="form-control" name="mapurl" required="">
														<div class="invalid-feedback" id="mapurl_feedback" style="display: none;">
															<?= $lang_en['prop_mapurl'] ?>

														</div>
													</div>
												</div>

												<div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
													<div class="row">
														<div class="col-md-3 col-lg-3 col-xs-12 col-sm-12">
															<div class="form-group mb-3">
																<label id="prop_beds">
																	<?= $lang_en['Total_Beds'] ?>

																</label>
																<input type="text" class="form-control numberonly" name="beds" required="">
																<div class="invalid-feedback" id="beds_feedback" style="display: none;">
																	<?= $lang_en['prop_beds'] ?>

																</div>
															</div>
														</div>

														<div class="col-md-3 col-lg-3 col-xs-12 col-sm-12">
															<div class="form-group mb-3">
																<label id="prop_bathroom">
																	<?= $lang_en['Total_Bathroom'] ?>

																</label>
																<input type="text" class="form-control numberonly" name="bathroom" required="">
																<div class="invalid-feedback" id="bathroom_feedback" style="display: none;">
																	<?= $lang_en['prop_bathroom'] ?>

																</div>
															</div>
														</div>

														<div class="col-md-3 col-lg-3 col-xs-12 col-sm-12">
															<div class="form-group mb-3">
																<label id="prop_sqft">
																	<?= $lang_en['Property_SQFT'] ?>

																	.</label>
																<input type="text" class="form-control numberonly" name="sqft" required="">
																<div class="invalid-feedback" id="sqft_feedback" style="display: none;">
																	<?= $lang_en['prop_sqft'] ?>

																</div>
															</div>
														</div>





														<div class="col-md-4 col-lg-4 col-xs-12 col-sm-12">
															<div class="form-group mb-3">
																<label id="prop_type">
																	<?= $lang_en['Select_Property_Type'] ?>

																</label>
																<select name="ptype" id="propt_type" class=" form-control" required>
																	<option value="" disabled selected>
																		<?= $lang_en['Select_Property_Type'] ?>

																	</option>
																	<?php
																	$zone = $rstate->query("select * from tbl_category");

																	while ($row = $zone->fetch_assoc()) {
																		$title = json_decode($row['title'], true);

																	?>
																		<option value="<?php echo $row['id']; ?>"><?php echo $title[$lang_code]; ?></option>
																	<?php
																	}
																	?>
																</select>
																<div class="invalid-feedback" id="prop_type_feedback" style="display: none;">
																	<?= $lang_en['prop_type'] ?>

																</div>
															</div>
														</div>



														<div class="col-md-4 col-lg-4 col-xs-12 col-sm-12">
															<div class="form-group mb-3">
																<label id="prop_governemnt">
																	<?= $lang_en['Select_Government'] ?>

																</label>
																<select name="pgov" id="government" class=" form-control" required>
																	<option value="" disabled selected>

																		<?= $lang_en['Select_Government'] ?>

																	</option>
																	<?php
																	$zone = $rstate->query("select * from tbl_government");
																	while ($row = $zone->fetch_assoc()) {
																		$title = json_decode($row['name'], true);

																	?>
																		<option value="<?php echo $row['id']; ?>"><?php echo $title[$lang_code]; ?></option>
																	<?php
																	}
																	?>
																</select>
																<div class="invalid-feedback" id="government_feedback" style="display: none;">
																	<?= $lang_en['prop_governemnt'] ?>

																</div>
															</div>
														</div>

														<div class="col-md-4 col-lg-4 col-xs-12 col-sm-12">
															<div class="form-group mb-3">
																<label id="prop_owner">
																	<?= $lang_en['Select_Owner'] ?>

																</label>
																<select
																	name="propowner" id="owner" class=" form-control" required>
																	<option value="" disabled selected>
																		<?= $lang_en['Select_Owner'] ?>
																	</option>
																	<?php
																	$zone = $rstate->query("select * from tbl_user");
																	while ($row = $zone->fetch_assoc()) {
																		$title = $row['name'];

																	?>
																		<option value="<?php echo $row['id']; ?>"><?php echo $title ?></option>
																	<?php
																	}
																	?>
																</select>
																<div class="invalid-feedback" id="owner_feedback" style="display: none;">
																	<?= $lang_en['prop_owner'] ?>

																</div>
															</div>
														</div>


													</div>
												</div>
											</div>
										</div>
										<div class="card-footer text-left">
											<button onclick="return validateForm()" type="submit" id="add-prop" class="btn btn-primary">
												<?= $lang_en['Add_Property'] ?>
											</button>
										</div>
								</form>
							<?php } ?>
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
	document.addEventListener('DOMContentLoaded', function() {

		const $selectOwner = $('#owner');
		$selectOwner.select2({
			placeholder: langDataEN.Select_Owner,
			allowClear: true
		});
		const $selectProp = $('#propt_type');
		$selectProp.select2({
			placeholder: langDataEN.Select_Property_Type,
			allowClear: true
		});
		const $selectgov = $('#government');
		$selectgov.select2({
			placeholder: langDataEN.Select_Government,
			allowClear: true
		});

		const $selectpro = $('#product');
		$selectpro.select2({
			allowClear: true,
			placeholder: langDataEN.Select_Property_Facility,
			mutiple: true
		});

	});

	function getCurrentLanguage() {
		// Get the active tab
		const activeTab = document.querySelector('.nav-link.active').getAttribute('href').substring(1);
		return activeTab === 'en' ? 'en' : 'ar';
	}
	const allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/jpg'];

	document.querySelector('input[name="prop_img[]"]').addEventListener('change', function() {
		for (const file of this.files) {
			if (!allowedTypes.includes(file.type)) {
				this.value = ''; // Clear invalid files
				break; // Stop further checks
			}
		}
	});

	function validateForm(edit = false) {
		// Clear previous feedback
		document.querySelectorAll('.invalid-feedback').forEach(function(feedback) {
			feedback.style.display = 'none';
		});

		const titleEn = document.querySelector('input[name="title_en"]').value;
		const titleAr = document.querySelector('input[name="title_ar"]').value;
		const addressEn = document.querySelector('input[name="address_en"]').value;
		const addressAr = document.querySelector('input[name="address_ar"]').value;

		const guestEn = document.querySelector('textarea[name="guest_rules_en"]').value;
		const guestAr = document.querySelector('textarea[name="guest_rules_ar"]').value;

		const compoundEn = document.querySelector('input[name="compound_name_en"]').value;
		const compoundAr = document.querySelector('input[name="compound_name_ar"]').value;

		const floorEn = document.querySelector('input[name="floor_en"]').value;
		const floorAr = document.querySelector('input[name="floor_ar"]').value;

		const cityEn = document.querySelector('input[name="city_en"]').value;
		const cityAr = document.querySelector('input[name="city_ar"]').value;


		const propImage = document.querySelector('input[name="prop_img[]"]');
		const files = propImage.files;
		const propVideo = document.querySelector('input[name="prop_video"]').value;
		const status = document.querySelector('select[name="status"]').value;
		const facility = document.querySelector('select[name="facility[]"]').value;
		const ptype = document.querySelector('select[name="ptype"]').value;
		const pgov = document.querySelector('select[name="pgov"]').value;
		const propowner = document.querySelector('select[name="propowner"]').value;
		const propSecurity = document.querySelector('input[name="prop_security"]').value;
		const plimit = document.querySelector('input[name="plimit"]').value;
		const mapurl = document.querySelector('input[name="mapurl"]').value;
		const sqft = document.querySelector('input[name="sqft"]').value;
		const bathroom = document.querySelector('input[name="bathroom"]').value;
		const beds = document.querySelector('input[name="beds"]').value;
		const prop_price = document.querySelector('input[name="prop_price"]').value;

		let isValid = true;
		let isArabicValid = true;
		let isEnglishValid = true;
		let alertMessage = '';
		let lang = getCurrentLanguage();

		if (!titleEn) {
			document.getElementById('title_en_feedback').style.display = 'block';

			isEnglishValid = false;

		}
		if (!titleAr) {
			document.getElementById('title_ar_feedback').style.display = 'block';
			isArabicValid = false;

		}

		if (!addressEn) {
			document.getElementById('address_en_feedback').style.display = 'block';
			isEnglishValid = false;

		}
		if (!addressAr) {
			document.getElementById('address_ar_feedback').style.display = 'block';
			isArabicValid = false;

		}
		if (!guestEn) {
			document.getElementById('prop_guest_rules_en_feedback').style.display = 'block';
			isEnglishValid = false;

		}
		if (!guestAr) {
			document.getElementById('prop_guest_rules_ar_feedback').style.display = 'block';
			isArabicValid = false;

		}
		if (!compoundEn) {
			document.getElementById('prop_compound_name_en_feedback').style.display = 'block';
			isEnglishValid = false;

		}
		if (!compoundAr) {
			document.getElementById('prop_compound_name_ar_feedback').style.display = 'block';
			isArabicValid = false;

		}
		if (!floorEn) {
			document.getElementById('floor_en_feedback').style.display = 'block';
			isEnglishValid = false;

		}
		if (!floorAr) {
			document.getElementById('floor_ar_feedback').style.display = 'block';
			isArabicValid = false;

		}
		if (!cityEn) {
			document.getElementById('city_en_feedback').style.display = 'block';
			isEnglishValid = false;

		}
		if (!cityAr) {
			document.getElementById('city_ar_feedback').style.display = 'block';
			isArabicValid = false;

		}
		if (!propImage || files.length < 3) {


			if (edit && files.length == 0) {
				isValid = true;

			} else {
				document.getElementById('prop_img_feedback').style.display = 'block';
				isValid = false;

			}
		}
		//		if (!propVideo) {
		//			document.getElementById('prop_video_feedback').style.display = 'block';
		//			isValid = false;
		//}
		if (!status) {
			document.getElementById('status_feedback').style.display = 'block';
			isValid = false;
		}
		if (!facility) {
			document.getElementById('facility_feedback').style.display = 'block';
			isValid = false;
		}
		if (!ptype) {
			document.getElementById('prop_type_feedback').style.display = 'block';
			isValid = false;
		}
		if (!pgov) {
			document.getElementById('government_feedback').style.display = 'block';
			isValid = false;
		}
		if (!propowner) {
			document.getElementById('owner_feedback').style.display = 'block';
			isValid = false;
		}

		if (!propSecurity) {
			document.getElementById('security_deposit_feedback').style.display = 'block';
			isValid = false;
		}
		if (!plimit) {
			document.getElementById('limit_feedback').style.display = 'block';
			isValid = false;
		}
		if (!mapurl) {
			document.getElementById('mapurl_feedback').style.display = 'block';
			isValid = false;
		}

		if (!sqft) {
			document.getElementById('sqft_feedback').style.display = 'block';
			isValid = false;
		}
		if (!bathroom) {
			document.getElementById('bathroom_feedback').style.display = 'block';
			isValid = false;
		}
		if (!beds) {
			document.getElementById('beds_feedback').style.display = 'block';
			isValid = false;
		}
		if (!prop_price) {
			document.getElementById('prop_price_feedback').style.display = 'block';
			isValid = false;
		}
		if (!isArabicValid && isEnglishValid) {
			// Show alert if there are required fields missing
			if (lang == "en") {
				alertMessage = langDataEN.alert_en;

			} else {
				alertMessage = langDataAR.alert_en;

			}
			isValid = false;
		}
		if (!isEnglishValid && isArabicValid) {
			// Show alert if there are required fields missing
			if (lang == "ar") {
				alertMessage = langDataAR.alert_ar;

			} else {
				alertMessage = langDataEN.alert_ar;

			}
			isValid = false;
		}
		if (isArabicValid && isEnglishValid) {
			alertMessage = '';
		}
		if (alertMessage) {
			document.getElementById('alert-message').innerHTML = alertMessage;
			document.getElementById('alert-container').style.display = 'block';

		} else {
			document.getElementById('alert-container').style.display = 'none';

		}
		if (!isValid) {
			return false;
		}

		return true; // Allow form submission
	}

	function changeLanguage(lang) {
		var langData = (lang === "ar") ? langDataAR : langDataEN;

		document.getElementById('prop_img_feedback').textContent = langData.prop_img;
		document.getElementById('prop_video_feedback').textContent = langData.prop_video;
		document.getElementById('status_feedback').textContent = langData.property_status;
		document.getElementById('facility_feedback').textContent = langData.prop_facility;
		document.getElementById('government_feedback').textContent = langData.prop_governemnt;
		document.getElementById('owner_feedback').textContent = langData.prop_owner;
		document.getElementById('prop_type_feedback').textContent = langData.prop_type;
		document.getElementById('security_deposit_feedback').textContent = langData.prop_security;
		document.getElementById('limit_feedback').textContent = langData.property_limit;
		document.getElementById('mapurl_feedback').textContent = langData.prop_mapurl;
		document.getElementById('sqft_feedback').textContent = langData.prop_sqft;
		document.getElementById('bathroom_feedback').textContent = langData.prop_barhroom;
		document.getElementById('beds_feedback').textContent = langData.prop_beds;
		document.getElementById('prop_price_feedback').textContent = langData.prop_price;

		document.getElementById('prop_image').textContent = langData.Property_Image;
		document.getElementById('prop_video').textContent = langData.Property_video;
		document.getElementById('property-status').textContent = langData.Property_Status;
		document.getElementById('prop_facility').textContent = langData.Select_Property_Facility;
		document.getElementById('prop_governemnt').textContent = langData.Select_Government;
		document.getElementById('prop_owner').textContent = langData.Select_Owner;
		document.getElementById('prop_type').textContent = langData.Select_Property_Type;
		document.getElementById('prop_security').textContent = langData.security_deposit;
		document.getElementById('limitlable').textContent = langData.total_allowed_persons;
		document.getElementById('propmap').textContent = langData.google_map_url;
		document.getElementById('prop_sqft').textContent = langData.Property_SQFT;
		document.getElementById('prop_bathroom').textContent = langData.Total_Bathroom;
		document.getElementById('prop_beds').textContent = langData.Total_Beds;
		document.getElementById('prop_price').textContent = langData.Property_Price_Per_Night;

		if (document.getElementById('add-prop')) {
			document.querySelector('button[type="submit"]').textContent = langData.Add_Property;

		} else {
			document.querySelector('button[type="submit"]').textContent = langData.Edit_Property;

		}

		const statusSelect = document.getElementById('inputGroupSelect01');
		statusSelect.querySelector('option[value=""]').textContent = langData.Select_property_Status;
		statusSelect.querySelector('option[value="1"]').textContent = langData.Publish;
		statusSelect.querySelector('option[value="0"]').textContent = langData.Unpublish;


		$('#propt_type').select2('destroy');
		$('#propt_type').select2({
			placeholder: langData.Select_Property_Type,
			allowClear: true
		});

		$('#government').select2('destroy');
		$('#government').select2({
			placeholder: langData.Select_Government,
			allowClear: true
		});

		$('#owner').select2('destroy');
		$('#owner').select2({
			placeholder: langData.Select_Owner,
			allowClear: true
		});

		$('#product').select2('destroy');
		$('#product').select2({
			placeholder: langData.Select_Property_Facility,
			allowClear: true,
			mutiple: true
		});

	}
</script>
<?php
require 'include/footer.php';
?>
</body>

</html>