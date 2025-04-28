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
								<form
									onsubmit="return submitform(true)"

									method="post" enctype="multipart/form-data">

									<div class="card-body">
										<div id="alert-container" class="mb-3" style="display: none;">
											<div class="alert alert-danger" id="alert-message"></div>
										</div>
										<div class="tab-content">
											<!-- English Tab -->
											<div class="tab-pane fade show active" id="en">
												<div class="row">

													<div class="col-md-6 col-lg-6 col-xs-12 col-sm-12">
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
													<div class="col-md-6 col-lg-6 col-xs-12 col-sm-12">
														<div class="form-group mb-3">
															<label for="cname">
																<?= $lang_en['Property_Description'] ?>

															</label>
															<textarea class="form-control" rows="10" name="description_en" style="resize: none;"><?php echo trim(htmlspecialchars(preg_replace('/^[\pZ\pC]+|[\pZ\pC]+$/u', '', $description['en']), ENT_QUOTES, 'UTF-8')); ?></textarea>
														</div>
													</div>

													<div class="col-md-6 col-lg-6 col-xs-12 col-sm-12">
														<div class="form-group mb-3">
															<label for="cname">
																<?= $lang_en['Guest_Rules'] ?>

															</label>
															<textarea class="form-control" rows="10" name="guest_rules_en" required="" style="resize: none;"><?php echo ltrim(htmlspecialchars(preg_replace('/^[\pZ\pC]+|[\pZ\pC]+$/u', '', $guest_rules['en']), ENT_QUOTES, 'UTF-8')); ?>

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

																type="text" class="form-control" name="compound_name_en">
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

														<div class="col-md-6 col-lg-6 col-xs-12 col-sm-12">
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
														<div class="col-md-6 col-lg-6 col-xs-12 col-sm-12">
															<div class="form-group mb-3">
																<label for="cname">
																	<?= $lang_ar['Property_Description'] ?>

																</label>
																<textarea class="form-control" rows="10" name="description_ar" style="resize: none;"><?php echo ltrim(htmlspecialchars(preg_replace('/^[\pZ\pC]+|[\pZ\pC]+$/u', '', $description['ar']), ENT_QUOTES, 'UTF-8')); ?>

																</textarea>
															</div>
														</div>

														<div class="col-md-6 col-lg-6 col-xs-12 col-sm-12">
															<div class="form-group mb-3">
																<label for="cname">
																	<?= $lang_ar['Guest_Rules'] ?>

																</label>
																<textarea class="form-control" rows="10" name="guest_rules_ar" required="" style="resize: none;"><?php echo ltrim(htmlspecialchars(preg_replace('/^[\pZ\pC]+|[\pZ\pC]+$/u', '', $guest_rules['ar']), ENT_QUOTES, 'UTF-8')); ?>
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

																	type="text" class="form-control" name="compound_name_ar">
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

												<div class="col-md-4 col-lg-4 col-xs-12 col-sm-12">
													<div class="form-group mb-3">
														<label id="prop_image">
															<?= $lang_en['Property_Image'] ?>
														</label>
														<input type="file" class="form-control" id="prop_img_upload" name="prop_img[]" accept=".jpg, .jpeg, .png, .gif" multiple />
														<input type="hidden" name="type" value="edit_property" />
														<input type="hidden" name="id" value="<?php echo $_GET['id']; ?>" />

														<!-- Combined Images Slider -->
														<div id="images-slider-container" style="margin-top:15px; <?php echo empty($data['image']) ? 'display:none;' : '' ?>">
															<div class="slides-container" style="height:120px; position:relative; overflow:hidden;">
																<div id="slides-wrapper" style="display:flex; transition:transform 0.3s ease;">
																	<?php
																	if (!empty($data['image'])) {
																		$imagesArray = explode(',', $data['image']);
																		foreach ($imagesArray as $index => $image) {
																			$trimmedImage = trim($image);
																			echo '<div class="slide" style="min-width:100%; text-align:center;">';
																			echo '<img src="' . $trimmedImage . '" style="max-height:120px; max-width:100%; object-fit:contain;" />';
																			echo '</div>';
																		}
																	}
																	?>
																</div>
															</div>
															<div id="slider-nav" style="text-align:center; margin-top:10px; <?php echo (empty($data['image']) || (count($imagesArray) <= 1) ? 'display:none;' : '') ?>">
																<button type="button" class="btn btn-sm btn-success slider-prev" style="padding:2px 8px; margin-right:5px;">❮</button>
																<span id="slider-counter">1/<?php echo !empty($data['image']) ? count($imagesArray) : '0' ?></span>
																<button type="button" class="btn btn-sm btn-success slider-next" style="padding:2px 8px; margin-left:5px;">❯</button>
															</div>
														</div>

														<div class="invalid-feedback" id="prop_img_feedback" style="display: none;">
															<?= $lang_en['prop_img'] ?>
														</div>
													</div>

												</div>

												<div class="col-md-4 col-lg-4 col-xs-12 col-sm-12">
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


												<div class="col-md-4 col-lg-4 col-xs-12 col-sm-12">
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
												<div class="col-md-4 col-lg-4 col-xs-12 col-sm-12">

													<div class="form-group mb-3">
														<label id="property-period" for="inputGroupSelect02"><?= $lang_en['Property_Period'] ?></label>
														<select class="form-control" name="period" id="inputGroupSelect02" required>
															<option value=""><?= $lang_en['Select_property_Period'] ?>...</option>
															<option value="d"
																<?php if ($data['period'] == 'd') {
																	echo 'selected';
																} ?>><?= $lang_en['Daily'] ?></option>
															<option value="m"
																<?php if ($data['period'] == 'm') {
																	echo 'selected';
																} ?>><?= $lang_en['Monthly'] ?></option>
														</select>
														<div class="invalid-feedback" id="period_feedback" style="display: none;">
															<?= $lang_en['property_period'] ?>

														</div>
													</div>
												</div>

												<div class="col-md-4 col-lg-4 col-xs-12 col-sm-12">

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


												<div class="col-md-4 col-lg-4 col-xs-12 col-sm-12">

													<div class="form-group mb-3">
														<label id="property-featured" for="inputGroupSelect03"><?= $lang_en['Property_Featured'] ?></label>
														<select class="form-control" name="featured" id="inputGroupSelect03" required>
															<option value=""><?= $lang_en['Select_property_Featured'] ?>...</option>
															<option value="1"
																<?php if ($data['is_featured'] == 1) {
																	echo 'selected';
																} ?>><?= $lang_en['Yes'] ?></option>
															<option value="0"
																<?php if ($data['is_featured'] == 0) {
																	echo 'selected';
																} ?>><?= $lang_en['No'] ?></option>
														</select>
														<div class="invalid-feedback" id="featured_feedback" style="display: none;">
															<?= $lang_en['property_featured'] ?>

														</div>
													</div>
												</div>

												<div class="col-md-4 col-lg-4 col-xs-12 col-sm-12">
													<div class="form-group mb-3">
														<label id="prop_security">
															<?= $lang_en['security_deposit'] ?>

														</label>
														<input
															value="<?php echo $data['security_deposit']; ?>"
															type="text" class="form-control numberonly" id="price" name="prop_security">

													</div>
												</div>

												<div class="col-md-4 col-lg-4 col-xs-12 col-sm-12">
													<div class="form-group mb-3">
														<label id="min-day">
															<?= $lang_en['min_days'] ?>

														</label>
														<input
															value="<?php echo $data['min_days']; ?>"

															type="text" class="form-control numberonly" id="min_day" name="min_day">
													</div>
												</div>

												<div class="col-md-4 col-lg-4 col-xs-12 col-sm-12">
													<div class="form-group mb-3">
														<label id="max-day">
															<?= $lang_en['max_days'] ?>

														</label>
														<input
															value="<?php echo $data['max_days']; ?>"

															type="text" class="form-control numberonly" id="max_day" name="max_day">
													</div>
												</div>





												<div class="col-md-4 col-lg-4 col-xs-12 col-sm-12">
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



												<div class="col-md-4 col-lg-4 col-xs-12 col-sm-12">
													<div class="form-group mb-3">
														<label id="prop_facility">
															<?= $lang_en['Select_Property_Facility'] ?>

														</label>
														<select

															name="facility[]" id="product" class=" form-control" multiple required>

															<?php
															$zone = $rstate->query("select * from tbl_facility where status = 1");
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
															value="<?php echo $data['map_url']; ?>"

															type="text" class="form-control" name="mapurl" required="">
														<div class="invalid-feedback" id="mapurl_feedback" style="display: none;">
															<?= $lang_en['prop_mapurl'] ?>

														</div>
													</div>
												</div>

												<div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
													<div class="row">
														<div class="col-md-4 col-lg-4 col-xs-12 col-sm-12">
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

														<div class="col-md-4 col-lg-4 col-xs-12 col-sm-12">
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

														<div class="col-md-4 col-lg-4 col-xs-12 col-sm-12">
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
																	$zone = $rstate->query("select * from tbl_category where status =1");

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
																	$zone = $rstate->query("select * from tbl_government where status =1");
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
																	$zone = $rstate->query("select * from tbl_user where status = 1 and verified =1");
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

														<div class="col-md-4 col-lg-4 col-xs-12 col-sm-12">
															<div class="form-group mb-3">
																<label id="prop_policy">
																	<?= $lang_en['Select_Privacy_Policy'] ?>

																</label>
																<select
																	name="propPrivacy" id="privacy" class=" form-control" required>
																	<option value="" disabled selected>
																		<?= $lang_en['Select_Privacy_Policy'] ?>
																	</option>
																	<?php
																	$zone = $rstate->query("select * from tbl_cancellation_policy where status = 1");
																	while ($row = $zone->fetch_assoc()) {
																		$title = json_decode($row['title'], true);
																		$isSelected = in_array($row['id'],  explode(',', $data['cancellation_policy_id'])) ? 'selected' : '';

																	?>
																		<option value="<?php echo $row['id']; ?>"
																			<?php echo $isSelected; ?>><?php echo  $title[$lang_code]; ?></option>
																	<?php
																	}
																	?>
																</select>
																<div class="invalid-feedback" id="privacy_feedback" style="display: none;">
																	<?= $lang_en['prop_privacy'] ?>

																</div>
															</div>
														</div>
														<div class="col-md-4 col-lg-4 col-xs-12 col-sm-12">

															<div class="form-group mb-3">
																<label id="property-approval" for="inputGroupSelect04"><?= $lang_en['Property_Approval'] ?></label>
																<select id="approval-select" class="form-control" name="approved"  required>
																	<option value=""><?= $lang_en['Select_property_Approval'] ?>...</option>
																	<option value="1"
																		<?php if ($data['is_approved'] == 1) {
																			echo 'selected';
																		} ?>><?= $lang_en['Yes'] ?></option>
																	<option value="0"
																		<?php if ($data['is_approved'] == 0) {
																			echo 'selected';
																		} ?>><?= $lang_en['No'] ?></option>
																</select>
																<div class="invalid-feedback" id="approval_feedback" style="display: none;">
																	<?= $lang_en['property_approval'] ?>

																</div>
															</div>
														</div>
														<div class="col-md-4 col-lg-4 col-xs-12 col-sm-12"
														id="cancel-reason-container" style="display:none;"
														>
															<div class="form-group mb-3">
																<label id="cancel_reason">
																	<?= $lang_en['cancel_reason'] ?>

																</label>
																<input
																	value="<?php echo $data['cancel_reason']; ?>"

																	type="text" class="form-control " name="cancel_reason" >
																<div class="invalid-feedback" id="cancel_reason_feedback" style="display: none;">
																	<?= $lang_en['prop_cancel_reason'] ?>

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
								<form
									onsubmit="return submitform(true)"

									method="post" enctype="multipart/form-data">

									<div class="card-body">
										<div id="alert-container" class="mb-3" style="display: none;">
											<div class="alert alert-danger" id="alert-message"></div>
										</div>
										<div class="tab-content">
											<!-- English Tab -->
											<div class="tab-pane fade show active" id="en">
												<div class="row">

													<div class="col-md-6 col-lg-6 col-xs-12 col-sm-12">
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
													<div class="col-md-6 col-lg-6 col-xs-12 col-sm-12">
														<div class="form-group mb-3">
															<label for="cname">
																<?= $lang_en['Property_Description'] ?>

															</label>
															<textarea class="form-control" rows="10" name="description_en" style="resize: none;"></textarea>
														</div>
													</div>

													<div class="col-md-6 col-lg-6 col-xs-12 col-sm-12">
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
															<input type="text" class="form-control" name="compound_name_en">
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

														<div class="col-md-6 col-lg-6 col-xs-12 col-sm-12">
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
														<div class="col-md-6 col-lg-6 col-xs-12 col-sm-12">
															<div class="form-group mb-3">
																<label for="cname">
																	<?= $lang_ar['Property_Description'] ?>

																</label>
																<textarea class="form-control" rows="10" name="description_ar" style="resize: none;"></textarea>
															</div>
														</div>

														<div class="col-md-6 col-lg-6 col-xs-12 col-sm-12">
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
																<input type="text" class="form-control" name="compound_name_ar">
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

												<div class="col-md-4 col-lg-4 col-xs-12 col-sm-12">
													<div class="form-group mb-3" id="add-property-images" style="<?= !isset($_GET['id']) ? '' : 'display:none;' ?>">
														<label id="prop_image">
															<?= $lang_en['Property_Image'] ?>
														</label>
														<input type="file" class="form-control" id="prop_img_upload_add" name="prop_img[]" required accept=".jpg, .jpeg, .png, .gif" multiple />
														<input type="hidden" name="type" value="add_property" />

														<div id="upload-preview-container" style="margin-top:15px; display:none;">
															<div class="slides-container" style="height:120px; position:relative; overflow:hidden;">
																<div id="upload-slides-wrapper" style="display:flex; transition:transform 0.3s ease;"></div>
															</div>
															<div id="upload-nav" style="text-align:center; margin-top:10px; display:none;">
																<button type="button" class="btn btn-sm btn-success upload-prev" style="padding:2px 8px; margin-right:5px;">❮</button>
																<span id="upload-counter">1/0</span>
																<button type="button" class="btn btn-sm btn-success upload-next" style="padding:2px 8px; margin-left:5px;">❯</button>
															</div>
														</div>

														<div class="invalid-feedback" id="prop_img_feedback" style="display: none;">
															<?= $lang_en['prop_img'] ?>
														</div>
													</div>

												</div>


												<div class="col-md-4 col-lg-4 col-xs-12 col-sm-12">
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


												<div class="col-md-4 col-lg-4 col-xs-12 col-sm-12">
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
												<div class="col-md-4 col-lg-4 col-xs-12 col-sm-12">

													<div class="form-group mb-3">
														<label id="property-period" for="inputGroupSelect02"><?= $lang_en['Property_Period'] ?></label>
														<select class="form-control" name="period" id="inputGroupSelect02" required>
															<option value=""><?= $lang_en['Select_property_Period'] ?>...</option>
															<option value="d"><?= $lang_en['Daily'] ?></option>
															<option value="m"><?= $lang_en['Monthly'] ?></option>
														</select>
														<div class="invalid-feedback" id="period_feedback" style="display: none;">
															<?= $lang_en['property_period'] ?>

														</div>
													</div>
												</div>


												<div class="col-md-4 col-lg-4 col-xs-12 col-sm-12">

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

												<div class="col-md-4 col-lg-4 col-xs-12 col-sm-12">

													<div class="form-group mb-3">
														<label id="property-featured" for="inputGroupSelect03"><?= $lang_en['Property_Featured'] ?></label>
														<select class="form-control" name="featured" id="inputGroupSelect03" required>
															<option value=""><?= $lang_en['Select_property_Featured'] ?>...</option>
															<option value="1"><?= $lang_en['Yes'] ?></option>
															<option value="0"><?= $lang_en['No'] ?></option>
														</select>
														<div class="invalid-feedback" id="featured_feedback" style="display: none;">
															<?= $lang_en['property_featured'] ?>

														</div>
													</div>
												</div>



												<div class="col-md-4 col-lg-4 col-xs-12 col-sm-12">
													<div class="form-group mb-3">
														<label id="prop_security">
															<?= $lang_en['security_deposit'] ?>

														</label>
														<input type="text" class="form-control numberonly" id="price" name="prop_security">

													</div>
												</div>

												<div class="col-md-4 col-lg-4 col-xs-12 col-sm-12">
													<div class="form-group mb-3">
														<label id="min-day">
															<?= $lang_en['min_days'] ?>

														</label>
														<input type="text" class="form-control numberonly" id="min_day" name="min_day">
													</div>
												</div>

												<div class="col-md-4 col-lg-4 col-xs-12 col-sm-12">
													<div class="form-group mb-3">
														<label id="max-day">
															<?= $lang_en['max_days'] ?>

														</label>
														<input type="text" class="form-control numberonly" id="max_day" name="max_day">
													</div>
												</div>




												<div class="col-md-4 col-lg-4 col-xs-12 col-sm-12">
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



												<div class="col-md-4 col-lg-4 col-xs-12 col-sm-12">
													<div class="form-group mb-3">
														<label id="prop_facility">
															<?= $lang_en['Select_Property_Facility'] ?>

														</label>
														<select

															name="facility[]" id="product" class=" form-control" multiple required>

															<?php
															$zone = $rstate->query("select * from tbl_facility where status =1 ");
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
														<div class="col-md-4 col-lg-4 col-xs-12 col-sm-12">
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

														<div class="col-md-4 col-lg-4 col-xs-12 col-sm-12">
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

														<div class="col-md-4 col-lg-4 col-xs-12 col-sm-12">
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
																	$zone = $rstate->query("select * from tbl_category where status =1");

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
																	$zone = $rstate->query("select * from tbl_government where status =1");
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
																	$zone = $rstate->query("select * from tbl_user where status =1 and verified=1");
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

														<div class="col-md-4 col-lg-4 col-xs-12 col-sm-12">
															<div class="form-group mb-3">
																<label id="prop_policy">
																	<?= $lang_en['Select_Privacy_Policy'] ?>

																</label>
																<select
																	name="propPrivacy" id="privacy" class=" form-control" required>
																	<option value="" disabled selected>
																		<?= $lang_en['Select_Privacy_Policy'] ?>
																	</option>
																	<?php
																	$zone = $rstate->query("select * from tbl_cancellation_policy where status =1");
																	while ($row = $zone->fetch_assoc()) {
																		$title = json_decode($row['title'], true);

																	?>
																		<option value="<?php echo $row['id']; ?>"><?php echo $title[$lang_code]; ?></option>
																	<?php
																	}
																	?>
																</select>
																<div class="invalid-feedback" id="privacy_feedback" style="display: none;">
																	<?= $lang_en['prop_privacy'] ?>

																</div>
															</div>
														</div>
														<div class="col-md-4 col-lg-4 col-xs-12 col-sm-12">

															<div class="form-group mb-3">
																<label id="property-approval" for="inputGroupSelect04"><?= $lang_en['Property_Approval'] ?></label>
																<select id="approval-select" class="form-control" name="approved"  required>
																	<option value=""><?= $lang_en['Select_property_Approval'] ?>...</option>
																	<option value="1"><?= $lang_en['Yes'] ?></option>
																	<option value="0"><?= $lang_en['No'] ?></option>
																</select>
																<div class="invalid-feedback" id="approval_feedback" style="display: none;">
																	<?= $lang_en['property_approval'] ?>

																</div>
															</div>
														</div>
														<div class="col-md-4 col-lg-4 col-xs-12 col-sm-12" 
														id="cancel-reason-container" style="display:none;"
														>
															<div class="form-group mb-3">
																<label id="cancel_reason">
																	<?= $lang_en['cancel_reason'] ?>

																</label>
																<input

																	type="text" class="form-control " name="cancel_reason" >
																<div class="invalid-feedback" id="cancel_reason_feedback" style="display: none;">
																	<?= $lang_en['prop_cancel_reason'] ?>

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

		const $selectPrivacy = $('#privacy');
		$selectPrivacy.select2({
			placeholder: langDataEN.Select_Privacy_Policy,
			allowClear: true
		});

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
		const period = document.querySelector('select[name="period"]').value;
		const featured = document.querySelector('select[name="featured"]').value;
		const approved = document.querySelector('select[name="approved"]').value;
		const facility = document.querySelector('select[name="facility[]"]').value;
		const ptype = document.querySelector('select[name="ptype"]').value;
		const pgov = document.querySelector('select[name="pgov"]').value;
		const propowner = document.querySelector('select[name="propowner"]').value;
		const propPrivacy = document.querySelector('select[name="propPrivacy"]').value;
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
		/*if (!compoundEn) {
			document.getElementById('prop_compound_name_en_feedback').style.display = 'block';
			isEnglishValid = false;

		}
		if (!compoundAr) {
			document.getElementById('prop_compound_name_ar_feedback').style.display = 'block';
			isArabicValid = false;

		}*/
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
		if (!period) {
			document.getElementById('period_feedback').style.display = 'block';
			isValid = false;
		}
		if (!featured) {
			document.getElementById('featured_feedback').style.display = 'block';
			isValid = false;
			
		}
		if (!approved) {
			document.getElementById('approval_feedback').style.display = 'block';
			isValid = false;
		}
		if(approved == '0'){
			const cancel_reason = document.querySelector('input[name="cancel_reason"]').value;
			if(!cancel_reason){
				document.getElementById('cancel_reason_feedback').style.display = 'block';
				isValid = false;
			}

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
		if (!propPrivacy) {
			document.getElementById('privacy_feedback').style.display = 'block';
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
		const approved = document.querySelector('select[name="approved"]').value;
		if(approved == '0'){
			document.getElementById('cancel_reason_feedback').textContent = langData.prop_cancel_reason;
			document.getElementById('cancel_reason').textContent = langData.cancel_reason;
		}
		document.getElementById('prop_img_feedback').textContent = langData.prop_img;
		document.getElementById('prop_video_feedback').textContent = langData.prop_video;
		document.getElementById('status_feedback').textContent = langData.property_status;
		document.getElementById('period_feedback').textContent = langData.property_period;
		document.getElementById('featured_feedback').textContent = langData.property_featured;
		document.getElementById('facility_feedback').textContent = langData.prop_facility;
		document.getElementById('government_feedback').textContent = langData.prop_governemnt;
		document.getElementById('owner_feedback').textContent = langData.prop_owner;
		document.getElementById('privacy_feedback').textContent = langData.prop_privacy;
		document.getElementById('prop_type_feedback').textContent = langData.prop_type;
		document.getElementById('limit_feedback').textContent = langData.property_limit;
		document.getElementById('mapurl_feedback').textContent = langData.prop_mapurl;
		document.getElementById('sqft_feedback').textContent = langData.prop_sqft;
		document.getElementById('bathroom_feedback').textContent = langData.prop_barhroom;
		document.getElementById('beds_feedback').textContent = langData.prop_beds;
		document.getElementById('prop_price_feedback').textContent = langData.prop_price;

		document.getElementById('prop_image').textContent = langData.Property_Image;
		document.getElementById('prop_video').textContent = langData.Property_video;
		document.getElementById('min-day').textContent = langData.min_days;
		document.getElementById('max-day').textContent = langData.max_days;
		document.getElementById('property-status').textContent = langData.Property_Status;
		document.getElementById('property-period').textContent = langData.Property_Period;
		document.getElementById('property-featured').textContent = langData.Property_Featured;
		document.getElementById('property-approval').textContent = langData.Property_Approval;
		document.getElementById('prop_facility').textContent = langData.Select_Property_Facility;
		document.getElementById('prop_governemnt').textContent = langData.Select_Government;
		document.getElementById('prop_owner').textContent = langData.Select_Owner;
		document.getElementById('prop_policy').textContent = langData.Select_Privacy_Policy;
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
		
		const approvedSelect = document.getElementById('approval-select');
		approvedSelect.querySelector('option[value=""]').textContent = langData.Select_property_Approval;
		approvedSelect.querySelector('option[value="1"]').textContent = langData.Yes;
		approvedSelect.querySelector('option[value="0"]').textContent = langData.No;

		const statusSelect = document.getElementById('inputGroupSelect01');
		statusSelect.querySelector('option[value=""]').textContent = langData.Select_property_Status;
		statusSelect.querySelector('option[value="1"]').textContent = langData.Publish;
		statusSelect.querySelector('option[value="0"]').textContent = langData.Unpublish;

		const periodSelect = document.getElementById('inputGroupSelect02');
		periodSelect.querySelector('option[value=""]').textContent = langData.Select_property_Period;
		periodSelect.querySelector('option[value="d"]').textContent = langData.Daily;
		periodSelect.querySelector('option[value="m"]').textContent = langData.Monthly;

		const featuredSelect = document.getElementById('inputGroupSelect03');
		featuredSelect.querySelector('option[value=""]').textContent = langData.Select_property_Featured;
		featuredSelect.querySelector('option[value="1"]').textContent = langData.Yes;
		featuredSelect.querySelector('option[value="0"]').textContent = langData.No;


	

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

		$('#privacy').select2('destroy');
		$('#privacy').select2({
			placeholder: langData.Select_Privacy_Policy,
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


<script>
	// Unified Image Slider Controller
	class ImageSlider {
		constructor(options) {
			this.slidesWrapper = document.getElementById(options.slidesWrapperId);
			this.navContainer = document.getElementById(options.navContainerId);
			this.counterElement = document.getElementById(options.counterId);
			this.prevButton = document.querySelector(options.prevButtonSelector);
			this.nextButton = document.querySelector(options.nextButtonSelector);
			this.currentSlide = 0;

			this.initialize();
		}

		initialize() {
			if (this.prevButton && this.nextButton) {
				this.prevButton.addEventListener('click', () => this.prevSlide());
				this.nextButton.addEventListener('click', () => this.nextSlide());
			}
		}

		updateSlides(files) {
			this.slidesWrapper.innerHTML = '';
			this.currentSlide = 0;

			if (files && files.length > 0) {
				Array.from(files).forEach((file, index) => {
					if (file.type.match('image.*')) {
						const reader = new FileReader();

						reader.onload = (e) => {
							const slide = document.createElement('div');
							slide.className = 'slide';
							slide.style.minWidth = '100%';
							slide.style.textAlign = 'center';

							const img = document.createElement('img');
							img.src = e.target.result;
							img.style.maxHeight = '120px';
							img.style.maxWidth = '100%';
							img.style.objectFit = 'contain';

							slide.appendChild(img);
							this.slidesWrapper.appendChild(slide);

							this.updateControls();
						};

						reader.readAsDataURL(file);
					}
				});
			}
		}

		prevSlide() {
			const slides = this.getSlides();
			this.currentSlide = (this.currentSlide - 1 + slides.length) % slides.length;
			this.updateSlider();
		}

		nextSlide() {
			const slides = this.getSlides();
			this.currentSlide = (this.currentSlide + 1) % slides.length;
			this.updateSlider();
		}

		getSlides() {
			return this.slidesWrapper.querySelectorAll('.slide');
		}

		updateSlider() {
			const slides = this.getSlides();
			if (slides.length > 0) {
				this.slidesWrapper.style.transform = `translateX(-${this.currentSlide * 100}%)`;
				this.counterElement.textContent = `${this.currentSlide + 1}/${slides.length}`;
			}
		}

		updateControls() {
			const slides = this.getSlides();
			if (slides.length > 1) {
				this.navContainer.style.display = 'block';
				this.counterElement.textContent = `1/${slides.length}`;
			} else {
				this.navContainer.style.display = 'none';
			}
			this.slidesWrapper.style.transform = 'translateX(0)';
		}
	}

	// Initialize sliders based on which form is active
	document.addEventListener('DOMContentLoaded', function() {
		// Edit Property Slider
		const editSlider = new ImageSlider({
			slidesWrapperId: 'slides-wrapper',
			navContainerId: 'slider-nav',
			counterId: 'slider-counter',
			prevButtonSelector: '.slider-prev',
			nextButtonSelector: '.slider-next'
		});

		// Upload Property Slider
		const uploadSlider = new ImageSlider({
			slidesWrapperId: 'upload-slides-wrapper',
			navContainerId: 'upload-nav',
			counterId: 'upload-counter',
			prevButtonSelector: '.upload-prev',
			nextButtonSelector: '.upload-next'
		});

		// Handle edit property image uploads
		document.getElementById('prop_img_upload_edit')?.addEventListener('change', function(e) {
			const container = document.getElementById('images-slider-container');
			container.style.display = 'block';
			editSlider.updateSlides(this.files);
		});

		// Handle add property image uploads
		document.getElementById('prop_img_upload_add')?.addEventListener('change', function(e) {
			const container = document.getElementById('upload-preview-container');
			container.style.display = 'block';
			uploadSlider.updateSlides(this.files);
		});

		// Initialize with existing images for edit case
		if (document.querySelectorAll('#slides-wrapper .slide').length > 0) {
			editSlider.updateControls();
		}
	});
</script>
<script>
document.addEventListener('DOMContentLoaded', function() {
  const approvalSelect = document.getElementById('approval-select');
  const cancelReasonContainer = document.getElementById('cancel-reason-container');

  approvalSelect.addEventListener('change', function() {
    if (this.value === '0') {
      cancelReasonContainer.style.display = 'block';
    } else {
      cancelReasonContainer.style.display = 'none';
    }
  });

  // Trigger change event in case there's a selected value on page load
  approvalSelect.dispatchEvent(new Event('change'));
});
</script>
<style>
	.slides-container {
		border: 1px solid #ddd;
		border-radius: 4px;
		background: #f8f9fa;
	}

	.btn-success {
		background-color: #28a745;
		border-color: #28a745;
	}
</style>

<?php
require 'include/footer.php';
?>
</body>

</html>