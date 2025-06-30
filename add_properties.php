<?php
require 'include/main_head.php';

$per = $_SESSION['permissions'];
$lang_code = load_language_code()["language_code"];

if (isset($_GET['id'])) {
	if (!in_array('Update_Property', $per)) {



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
	if (!in_array('Create_Property', $per)) {


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
								$data = $rstate->query("select * from tbl_property where id=" . $_GET['id'] . " and is_deleted = 0")->fetch_assoc();
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
													<!-- Edit Property Images Section -->
													<div class="form-group mb-3" id="edit-property-images" style="<?= isset($_GET['id']) ? '' : 'display:none;' ?>">
														<label id="prop_image">
															<?= $lang_en['Property_Image'] ?>
														</label>
														<input type="file" class="form-control" id="prop_img_upload_edit" name="prop_img[]" accept=".jpg, .jpeg, .png, .gif" multiple />
														<input type="hidden" name="type" value="edit_property" />
														<input type="hidden" name="id" value="<?php echo $_GET['id']; ?>" />
														<input type="hidden" name="default_image" id="default_image">
														<!-- Hidden input to track remaining existing images -->
														<input type="hidden" id="existing_images" name="existing_images" value="<?= htmlspecialchars($data['image'] ?? '') ?>">
														<!-- Hidden input to track new image URLs -->

														<!-- Edit Property Preview Container -->
														<?php $property_images = !empty($data['image']) ? explode(',', $data['image']) : []; ?>
														<div id="images-slider-container" style="margin-top:15px; <?= empty($property_images) ? 'display:none;' : '' ?>">
															<div class="slides-container" style="height:120px; position:relative; overflow:hidden;">
																<div id="slides-wrapper" class="slides-wrapper" style="display:flex; transition:transform 0.3s ease;">
																	<?php if (!empty($property_images)): ?>
																		<?php foreach ($property_images as $index => $image): ?>
																			<div class="slide existing-image" data-index="<?= $index ?>" style="min-width:150px; padding:5px; position:relative;" data-image-path="<?= htmlspecialchars($image) ?>">
																				<img src="<?= $image ?>" class="img-thumbnail" style="width:100%; height:100px; object-fit:cover; cursor:pointer;">
																				<button type="button" class="btn btn-danger btn-xs remove-image" style="position:absolute; top:5px; right:5px; padding:0 5px;">×</button>
																			</div>
																		<?php endforeach; ?>
																	<?php endif; ?>
																</div>
															</div>
															<div id="slider-nav" style="text-align:center; margin-top:10px; <?= count($property_images) > 1 ? '' : 'display:none;' ?>">
																<button type="button" class="btn btn-sm btn-success slider-prev" style="padding:2px 8px; margin-right:5px;">❮</button>
																<span id="slider-counter">1/<?= count($property_images) ?></span>
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

														<!-- Video preview container -->
														<div id="video-preview-container" class="mt-2" style="<?= empty($data['video']) ? 'display:none;' : '' ?>">
															<div class="d-flex flex-column">
																<!-- Video Preview (Click to Open Modal) -->
																<video id="video-preview" controls width="100%" style="cursor: pointer; border-radius: 5px; max-width: 300px;">
																	<?php if (!empty($data['video'])): ?>
																		<source src="<?= $data['video'] ?>" type="video/mp4">
																	<?php endif; ?>
																	Your browser does not support the video tag.
																</video>

																<!-- Clear Button (Below Video) -->
																<button type="button" id="clear-video" class="btn btn-danger btn-sm mt-2 align-self-start">
																	<i class="fas fa-trash"></i> Clear Video
																</button>
															</div>
															<input type="hidden" name="existing_video" id="existing-video" value="<?= !empty($data['video']) ? $data['video'] : '' ?>">
														</div>

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

														<div class="col-md-4 col-lg-4 col-xs-12 col-sm-12"
															id="cancel-reason-container" style="display:none;">
															<div class="form-group mb-3">
																<label id="cancel_reason">
																	<?= $lang_en['cancel_reason'] ?>

																</label>
																<input
																	value="<?php echo $data['cancel_reason']; ?>"

																	type="text" class="form-control " name="cancel_reason">
																<div class="invalid-feedback" id="cancel_reason_feedback" style="display: none;">
																	<?= $lang_en['prop_cancel_reason'] ?>

																</div>
															</div>
														</div>
														<div class="col-md-4 col-lg-4 col-xs-12 col-sm-12"
															>
															<div class="form-group mb-3">
																<label id="visibility">
																	<?= $lang_en['visibility'] ?>

																</label>
																<input
																	value="<?php echo $data['visibility']; ?>"

																	type="text" class="form-control numberonly" name="visibility">
																
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

											<!-- Show Approve button if not approved (status = 0) -->
											<button type="submit" onclick="handleApprove()" id='approve_property' name="approve_property" class="btn btn-success ml-2">
												<?= $lang_en['Approve'] ?>
											</button>
											<!-- Show Reject button if already approved (status = 1) -->
											<button type="submit" onclick="return handleReject();" id='reject_property' name="reject_property" class="btn btn-danger ml-2">
												<?= $lang_en['Reject'] ?>
											</button>
											<input type="hidden" name="approved" id="approvedInput" value="">

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
														<input type="hidden" id="existing_images" name="existing_images" value="<?= htmlspecialchars($data['image'] ?? '') ?>">
														<input type="hidden" name="default_image" id="default_image">

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

														<!-- Video preview container -->
														<div id="video-preview-container" class="mt-2" style="<?= empty($data['video']) ? 'display:none;' : '' ?>">
															<div class="d-flex flex-column">
																<!-- Video Preview (Click to Open Modal) -->
																<video id="video-preview" controls width="100%" style="cursor: pointer; border-radius: 5px; max-width: 300px;">
																	<?php if (!empty($data['video'])): ?>
																		<source src="<?= $data['video'] ?>" type="video/mp4">
																	<?php endif; ?>
																	Your browser does not support the video tag.
																</video>

																<!-- Clear Button (Below Video) -->
																<button type="button" id="clear-video" class="btn btn-danger btn-sm mt-2 align-self-start">
																	<i class="fas fa-trash"></i> Clear Video
																</button>
															</div>
															<input type="hidden" name="existing_video" id="existing-video" value="<?= !empty($data['video']) ? $data['video'] : '' ?>">
														</div>

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

<!-- Large Centered Modal for Video Preview -->
<div class="modal fade" id="videoModal" tabindex="-1" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered modal-lg"> <!-- modal-lg for large size, centered -->
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Video Preview</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body text-center">
				<video id="modal-video-preview" controls style="max-width: 100%; max-height: 70vh;">
					Your browser does not support the video tag.
				</video>
			</div>
		</div>
	</div>
</div>
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

		const existingImagesInput = document.querySelector('input[name="existing_images"]');
		const existingImagesValue = existingImagesInput.value;

		// Split the comma-separated string into an array
		const remainingImages = existingImagesValue ? existingImagesValue.split(',') : [];

		// Get the count of remaining images
		const remainingImagesCount = remainingImages.length;
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
		if (!propImage || (files.length + remainingImagesCount) < 3) {

			document.getElementById('prop_img_feedback').style.display = 'block';
			isValid = false;

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
		document.getElementById('cancel_reason_feedback').textContent = langData.prop_cancel_reason;
		document.getElementById('cancel_reason').textContent = langData.cancel_reason;
		document.getElementById('visibility').textContent = langData.visibility;

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
			document.getElementById('approve_property').textContent = langData.Approve;
			document.getElementById('reject_property').textContent = langData.Reject;

		}


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
	class ImageSlider {
		constructor(options) {
			this.slidesWrapper = document.getElementById(options.slidesWrapperId);
			this.navContainer = document.getElementById(options.navContainerId);
			this.counterElement = document.getElementById(options.counterId);
			this.prevButton = document.querySelector(options.prevButtonSelector);
			this.nextButton = document.querySelector(options.nextButtonSelector);
			this.currentSlide = 0;
			this.images = []; // Store image data
			this.fileInput = options.fileInput || null;
			this.slideWidth = options.slideWidth || 150;
			this.previewModal = null; // Reference to the preview modal
			this.defaultImageIndex = 0; // Track the default image index

			this.initialize();
		}

		initialize() {
			if (this.prevButton && this.nextButton) {
				this.prevButton.addEventListener('click', () => this.prevSlide());
				this.nextButton.addEventListener('click', () => this.nextSlide());
			}
		}

		updateSlides(files, reset = false) {
			if (!files || files.length === 0) return;

			if (reset) {
				this.images = []; // Clear existing images if reset is true
				this.defaultImageIndex = 0;
			}

			const newImages = [];

			// Filter out duplicates and non-images
			Array.from(files).forEach(file => {
				if (!file.type.match('image.*')) return;

				// Check for duplicates by name and size
				const isDuplicate = this.images.some(
					img => img.file.name === file.name && img.file.size === file.size
				);

				if (!isDuplicate) {
					newImages.push(file);
				}
			});

			if (newImages.length === 0) return;

			// Process new images
			let loadedCount = 0;
			newImages.forEach(file => {
				const reader = new FileReader();
				reader.onload = (e) => {
					this.images.push({
						file: file,
						url: e.target.result
					});
					loadedCount++;

					if (loadedCount === newImages.length) {
						this.renderSlides();
					}
				};
				reader.readAsDataURL(file);
			});
		}

		renderSlides() {
			this.slidesWrapper.innerHTML = '';
			this.currentSlide = 0;

			this.images.forEach((image, index) => {
				const slide = document.createElement('div');
				slide.className = 'slide';
				slide.dataset.index = index;
				slide.style.minWidth = `${this.slideWidth}px`;
				slide.style.padding = '5px';
				slide.style.position = 'relative';

				// Add blue border wrapper if this is the default image
				if (index === this.defaultImageIndex) {
					slide.style.border = '3px solid #007bff';
					slide.style.borderRadius = '5px';
				}

				const imgWrapper = document.createElement('div');
				imgWrapper.style.width = '100%';
				imgWrapper.style.height = '100px';
				imgWrapper.style.position = 'relative';
				imgWrapper.style.overflow = 'hidden';

				const img = document.createElement('img');
				img.src = image.url;
				img.className = 'img-thumbnail';
				img.style.width = '100%';
				img.style.height = '100%';
				img.style.objectFit = 'cover';
				img.style.cursor = 'pointer';
				img.addEventListener('click', () => {
					// Show preview when clicked in slider view
					this.showImagePreview(image.url, index);
				});

				const removeBtn = document.createElement('button');
				removeBtn.type = 'button';
				removeBtn.className = 'btn btn-danger btn-xs remove-image';
				removeBtn.innerHTML = '×';
				removeBtn.style.position = 'absolute';
				removeBtn.style.top = '5px';
				removeBtn.style.right = '5px';
				removeBtn.style.padding = '0 5px';
				removeBtn.addEventListener('click', (e) => {
					e.stopPropagation();
					this.removeImage(index);
				});

				imgWrapper.appendChild(img);
				slide.appendChild(imgWrapper);
				slide.appendChild(removeBtn);
				this.slidesWrapper.appendChild(slide);
			});

			this.updateControls();
			this.updateFileInput();
		}

		setDefaultImage(index) {
			if (index < 0 || index >= this.images.length) return;

			// Get the image that will become default
			const imageToMakeDefault = this.images[index];
		
			// Remove the image from its current position
			this.images.splice(index, 1);

			// Add it to the beginning of the array
			this.images.unshift(imageToMakeDefault);

			// Update the default image index (now it's 0)
			this.defaultImageIndex = 0;

			this.renderSlides();

		}

		handleDefaultImageChange() {
			// This can be overridden in child classes if needed
			console.log('Default image changed to index:', this.defaultImageIndex);
		}

		removeImage(index) {
			// Adjust defaultImageIndex if we're removing the current default
			if (index < this.defaultImageIndex) {
				this.defaultImageIndex--;
			} else if (index === this.defaultImageIndex) {
				// If we're removing the default, set the first image as default
				this.defaultImageIndex = 0;
			}

			this.images.splice(index, 1);
			this.renderSlides();

			if (this.images.length === 0) {
				this.slidesWrapper.innerHTML = '';
				if (this.fileInput) this.fileInput.value = '';
				this.defaultImageIndex = 0;
			}
		}

		updateFileInput() {
			if (!this.fileInput) return;

			const dataTransfer = new DataTransfer();
			this.images.forEach(img => dataTransfer.items.add(img.file));
			this.fileInput.files = dataTransfer.files;
		}

		showImagePreview(imageUrl, imageIndex) {
			if (!this.previewModal) {
				this.createPreviewModal();
			}

			const modalImg = this.previewModal.querySelector('img');
			modalImg.src = imageUrl;

			// Store the current image index in the modal
			this.previewModal.dataset.currentIndex = imageIndex;

			this.showModal();
		}

		createPreviewModal() {
			this.previewModal = document.createElement('div');
			this.previewModal.id = 'imagePreviewModal';
			this.previewModal.className = 'modal fade';
			this.previewModal.tabIndex = '-1';
			this.previewModal.role = 'dialog';
			this.previewModal.innerHTML = `
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Image Preview</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body text-center">
                        <img src="" style="max-width: 100%; max-height: 80vh; cursor: pointer;">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary set-default-btn">Set as Default</button>
                    </div>
                </div>
            </div>
        `;
			document.body.appendChild(this.previewModal);

			// Add close event listener
			const closeBtn = this.previewModal.querySelector('.close');
			closeBtn.addEventListener('click', () => this.hideModal());



			// Add click handler for the "Set as Default" button
			const setDefaultBtn = this.previewModal.querySelector('.set-default-btn');
			setDefaultBtn.addEventListener('click', () => {
				const currentIndex = parseInt(this.previewModal.dataset.currentIndex);
				this.setDefaultImage(currentIndex);
				this.hideModal();
			});
		}

		showModal() {
			if (typeof jQuery !== 'undefined' && jQuery.fn.modal) {
				jQuery(this.previewModal).modal('show');
			} else {
				this.previewModal.style.display = 'block';
				// Add backdrop
				const backdrop = document.createElement('div');
				backdrop.className = 'modal-backdrop fade show';
				document.body.appendChild(backdrop);
				this.previewModal.backdrop = backdrop;
			}
		}

		hideModal() {
			if (typeof jQuery !== 'undefined' && jQuery.fn.modal) {
				jQuery(this.previewModal).modal('hide');
			} else {
				this.previewModal.style.display = 'none';
				if (this.previewModal.backdrop) {
					document.body.removeChild(this.previewModal.backdrop);
				}
			}
		}

		prevSlide() {
			const slides = this.getSlides();
			if (slides.length === 0) return;

			this.currentSlide = (this.currentSlide - 1 + slides.length) % slides.length;
			this.updateSlider();
		}

		nextSlide() {
			const slides = this.getSlides();
			if (slides.length === 0) return;

			this.currentSlide = (this.currentSlide + 1) % slides.length;
			this.updateSlider();
		}

		getSlides() {
			return this.slidesWrapper.querySelectorAll('.slide');
		}

		updateSlider() {
			const slides = this.getSlides();
			if (slides.length > 0) {
				const translateX = -this.currentSlide * this.slideWidth;
				this.slidesWrapper.style.transform = `translateX(${translateX}px)`;
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

	// Initialize sliders
	document.addEventListener('DOMContentLoaded', function() {
		// Edit Property Slider
		const editSlider = new ImageSlider({
			slidesWrapperId: 'slides-wrapper',
			navContainerId: 'slider-nav',
			counterId: 'slider-counter',
			prevButtonSelector: '.slider-prev',
			nextButtonSelector: '.slider-next',
			fileInput: document.getElementById('prop_img_upload_edit'),
			slideWidth: 150
		});

		// Upload Property Slider
		const uploadSlider = new ImageSlider({
			slidesWrapperId: 'upload-slides-wrapper',
			navContainerId: 'upload-nav',
			counterId: 'upload-counter',
			prevButtonSelector: '.upload-prev',
			nextButtonSelector: '.upload-next',
			fileInput: document.getElementById('prop_img_upload_add'),
			slideWidth: 150
		});

		// Handle edit property image uploads
		document.getElementById('prop_img_upload_edit')?.addEventListener('change', function(e) {
			const container = document.getElementById('images-slider-container');
			if (container) container.style.display = 'block';
			editSlider.updateSlides(this.files);
		});

		// Handle add property image uploads
		document.getElementById('prop_img_upload_add')?.addEventListener('change', function(e) {
			const container = document.getElementById('upload-preview-container');
			if (container) container.style.display = 'block';
			uploadSlider.updateSlides(this.files);
		});

		// Initialize with existing images for edit case
		if (document.querySelectorAll('#slides-wrapper .slide').length > 0) {
			editSlider.updateControls();
		}
	});
	class PropertyImageSlider extends ImageSlider {
		constructor(options) {
			super(options);
			this.existingImagesInput = document.getElementById('existing_images');
			this.existingImages = this.existingImagesInput.value ? this.existingImagesInput.value.split(',') : [];
			this.newUploads = []; // Stores File objects of new uploads
			this.defaultImageInput = document.getElementById('default_image'); // Hidden input for default image

			// Initialize with existing images
			if (this.existingImages.length > 0) {
				this.images = this.existingImages.map((url, index) => ({
					url: url,
					isNew: false,
					file: null // No file object for existing images
				}));
				this.renderSlides();

				// Initialize default image if not set
				if (this.defaultImageInput.value && this.defaultImageInput.value !== '') {
					const defaultIndex = this.images.findIndex(img => img.url === this.defaultImageInput.value);
					if (defaultIndex !== -1) {
						this.defaultImageIndex = defaultIndex;
					}
				}
			}
		}

		handleDefaultImageChange() {
			if (this.images.length > 0 && this.defaultImageInput) {
				const defaultImage = this.images[this.defaultImageIndex];
				// Set the default image URL in the hidden input
				this.defaultImageInput.value = defaultImage.url;

				// Optional: You can make an API call here to update the default image on the server
				// this.updateDefaultImageOnServer(defaultImage.url);
			}
		}

		setDefaultImage(index) {
			if (index < 0 || index >= this.images.length) return;

			// Get the image that will become default
			const imageToMakeDefault = this.images[index];
			
			// Remove the image from its current position
			this.images.splice(index, 1);

			// Add it to the beginning of the array
			this.images.unshift(imageToMakeDefault);

			// Update the default image index (now it's 0)
			this.defaultImageIndex = 0;

			this.renderSlides();
			this.handleDefaultImageChange();
			this.updateHiddenInputs();
		}

		updateSlides(files) {
			if (!files || files.length === 0) return;

			// Filter out duplicates
			const newFiles = Array.from(files).filter(file => {
				if (!file.type.match('image.*')) return false;

				// Check against new uploads
				const isNewDuplicate = this.newUploads.some(
					f => f.name === file.name && f.size === file.size
				);

				// Check against existing images (by filename)
				const existingFilename = file.name.toLowerCase();
				const isExistingDuplicate = this.existingImages.some(
					url => url.toLowerCase().includes(existingFilename)
				);

				return !isNewDuplicate && !isExistingDuplicate;
			});

			if (newFiles.length === 0) return;

			// Store File objects for form submission
			this.newUploads = [...this.newUploads, ...newFiles];
			this.updateFileInput();

			// Process images for preview
			let loadedCount = 0;
			newFiles.forEach(file => {
				const reader = new FileReader();
				reader.onload = (e) => {
					const newImage = {
						url: e.target.result,
						isNew: true,
						file: file
					};

					// Add new images to the beginning
					this.images.unshift(newImage);
					loadedCount++;

					if (loadedCount === newFiles.length) {
						// If this is the first image being added, set it as default
						if (this.images.length === newFiles.length) {
							this.defaultImageIndex = 0;
							this.handleDefaultImageChange();
						}

						this.renderSlides();
						this.updateHiddenInputs();

						// Show container if hidden
						const container = document.getElementById('images-slider-container');
						if (container) container.style.display = 'block';
					}
				};
				reader.readAsDataURL(file);
			});
		}

		updateHiddenInputs() {
			// Update existing images input with remaining paths
			const remainingExisting = this.images
				.filter(img => !img.isNew)
				.map(img => img.url);
			this.existingImagesInput.value = remainingExisting.join(',');
		}

		renderSlides() {
			this.slidesWrapper.innerHTML = '';
			this.currentSlide = 0;

			this.images.forEach((image, index) => {
				const slide = document.createElement('div');
				slide.className = image.isNew ? 'slide new-image' : 'slide existing-image';
				slide.dataset.index = index;
				slide.style.minWidth = `${this.slideWidth}px`;
				slide.style.padding = '5px';
				slide.style.position = 'relative';

				// Add blue border wrapper if this is the default image
				if (index === this.defaultImageIndex) {
					slide.style.border = '3px solid #007bff';
					slide.style.borderRadius = '5px';
				}

				const imgWrapper = document.createElement('div');
				imgWrapper.style.width = '100%';
				imgWrapper.style.height = '100px';
				imgWrapper.style.position = 'relative';
				imgWrapper.style.overflow = 'hidden';

				const img = document.createElement('img');
				img.src = image.url;
				img.className = 'img-thumbnail';
				img.style.width = '100%';
				img.style.height = '100%';
				img.style.objectFit = 'cover';
				img.style.cursor = 'pointer';
				img.addEventListener('click', () => {
					this.showImagePreview(image.url, index);
				});

				const removeBtn = document.createElement('button');
				removeBtn.type = 'button';
				removeBtn.className = 'btn btn-danger btn-xs remove-image';
				removeBtn.innerHTML = '×';
				removeBtn.style.position = 'absolute';
				removeBtn.style.top = '5px';
				removeBtn.style.right = '5px';
				removeBtn.style.padding = '0 5px';
				removeBtn.addEventListener('click', (e) => {
					e.stopPropagation();
					this.removeImage(index);
				});

				imgWrapper.appendChild(img);
				slide.appendChild(imgWrapper);
				slide.appendChild(removeBtn);
				this.slidesWrapper.appendChild(slide);
			});

			this.updateControls();
			this.updateFileInput();
		}

		updateFileInput() {
			// Update the actual file input with File objects
			const dataTransfer = new DataTransfer();
			this.newUploads.forEach(file => dataTransfer.items.add(file));
			this.fileInput.files = dataTransfer.files;
		}

		removeImage(index) {
			const imageToRemove = this.images[index];

			// Adjust defaultImageIndex if needed
			if (index < this.defaultImageIndex) {
				this.defaultImageIndex--;
			} else if (index === this.defaultImageIndex) {
				// If removing the default image, set first image as default
				this.defaultImageIndex = 0;
				this.handleDefaultImageChange();
			}

			if (imageToRemove.isNew) {
				// Remove from new uploads
				this.newUploads = this.newUploads.filter(file => file !== imageToRemove.file);
			} else {
				// Remove from existing images
				this.existingImages = this.existingImages.filter(url => url !== imageToRemove.url);
			}

			// Remove from display
			this.images.splice(index, 1);

			this.updateHiddenInputs();
			this.updateFileInput();
			this.renderSlides();

			// Hide container if no images left
			if (this.images.length === 0) {
				document.getElementById('images-slider-container').style.display = 'none';
				this.defaultImageIndex = 0;
				this.defaultImageInput.value = ''; // Clear default image when no images left
			}
		}
	}

	document.addEventListener('DOMContentLoaded', function() {
		const editSlider = new PropertyImageSlider({
			slidesWrapperId: 'slides-wrapper',
			navContainerId: 'slider-nav',
			counterId: 'slider-counter',
			prevButtonSelector: '.slider-prev',
			nextButtonSelector: '.slider-next',
			fileInput: document.getElementById('prop_img_upload_edit'),
			slideWidth: 150
		});

		// Handle file uploads
		document.getElementById('prop_img_upload_edit')?.addEventListener('change', function(e) {
			editSlider.updateSlides(this.files);
		});
	});
</script>
<script>
	function handleApprove() {
		// 1. First validate the form
		if (validateForm(true)) {
			// 2. If validation passes, set approval value
			document.getElementById('approvedInput').value = '1';
			// 3. Submit the form
			document.querySelector('form').submit();
			return true;
		}
		// If validation fails, block submission
		return false;
	}

	function handleReject() {
		// First show the cancel reason if it's not visible
		const cancelReasonContainer = document.getElementById('cancel-reason-container');
		if (cancelReasonContainer.style.display === 'none') {
			cancelReasonContainer.style.display = 'block';
			return false; // Don't submit yet
		}

		// Validate the form including the cancel reason
		if (validateForm(true)) {
			// Check if cancel reason is filled
			const cancelReason = document.querySelector('input[name="cancel_reason"]').value;
			if (!cancelReason.trim()) {
				document.getElementById('cancel_reason_feedback').style.display = 'block';
				return false;
			}

			// Set approval value to 0 or whatever indicates rejection
			document.getElementById('approvedInput').value = '0';

			// Submit the form
			document.querySelector('form').submit();
			return true;
		}
		return false;
	}
</script>
<script>
	document.addEventListener('DOMContentLoaded', function() {
		// Map Arabic input names to their English counterparts
		const fieldMap = {
			'title_ar': 'title_en',
			'address_ar': 'address_en',
			'description_ar': 'description_en',
			'guest_rules_ar': 'guest_rules_en',
			'compound_name_ar': 'compound_name_en',
			'floor_ar': 'floor_en',
			'city_ar': 'city_en'
		};

		// Set up event listeners for all Arabic fields
		Object.keys(fieldMap).forEach(arField => {
			const inputElement = document.querySelector(`input[name="${arField}"], textarea[name="${arField}"]`);

			if (inputElement) {
				let debounceTimer;

				inputElement.addEventListener('input', function() {
					clearTimeout(debounceTimer);

					// Only translate if there's content and the field is in the Arabic tab
					if (this.value.trim() !== '' && this.closest('.tab-pane').id === 'ar') {
						debounceTimer = setTimeout(() => {
							translateField(this.value, fieldMap[arField]);
						}, 800); // 800ms delay after typing stops
					}
				});
			}
		});

		// Single translation function for all fields
		async function translateField(text, targetFieldName) {
			try {
				const targetElement = document.querySelector(`input[name="${targetFieldName}"], textarea[name="${targetFieldName}"]`);
				if (!targetElement) return;

				// Show translating state
				const originalPlaceholder = targetElement.placeholder;
				const originalValue = targetElement.value;
				targetElement.placeholder = "Translating...";
				targetElement.value = "";

				// Encode the text for URL
				const encodedText = encodeURIComponent(text);

				// Call your translation API
				const response = await fetch(`user_api/translate-proxy.php?sl=ar&dl=en&text=${encodedText}`

				);

				if (!response.ok) {
					targetElement.placeholder = originalPlaceholder;
					targetElement.value = originalValue;

				}

				const translatedText = await response.json();

				// Only update if the target field is still empty
				if (targetElement.value === "" || targetElement.value === originalValue) {
					targetElement.value = translatedText['data']['destination-text'];
				}
				targetElement.placeholder = originalPlaceholder;

			} catch (error) {
				console.error('Translation error:', error);
				targetElement.placeholder = "Translation failed - try again";
				targetElement.value = originalValue;
			}
		}

		// Tab switching functionality for Bootstrap
		document.querySelectorAll('[data-bs-toggle="tab"]').forEach(tab => {
			tab.addEventListener('click', function(e) {
				e.preventDefault();
				const target = this.getAttribute('href');
				document.querySelectorAll('.tab-pane').forEach(pane => {
					pane.classList.remove('show', 'active');
				});
				document.querySelector(target).classList.add('show', 'active');
			});
		});
	});
</script>


<script>
	$(document).ready(function() {
		// Handle video file selection
		$('#video').change(function(e) {
			const file = e.target.files[0];
			if (file) {
				const videoURL = URL.createObjectURL(file);
				updateVideoPreview(videoURL);
				$('#existing-video').val(''); // Clear existing video reference
				$('#video-preview-container').show();
			}
		});

		// Clear video handler (button below video)
		$('#clear-video').click(function() {
			$('#video').val(''); // Clear file input
			$('#video-preview').html(''); // Remove video source
			$('#existing-video').val(''); // Clear hidden field
			$('#video-preview-container').hide(); // Hide preview
		});

		// Video click handler for modal preview
		$('#video-preview').click(function() {
			const videoSrc = $(this).find('source').attr('src');
			if (videoSrc) {
				$('#modal-video-preview').html('<source src="' + videoSrc + '" type="video/mp4">');
				$('#modal-video-preview')[0].load();
				$('#videoModal').modal('show');
			}
		});

		// Function to update video preview
		function updateVideoPreview(videoURL) {
			$('#video-preview').html('<source src="' + videoURL + '" type="video/mp4">');
			$('#video-preview')[0].load();
		}

		// Initialize with existing video if available
		<?php if (!empty($data['video'])): ?>
			$('#video-preview-container').show();
		<?php endif; ?>
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