<?php

$lang = load_language();
$per = $_SESSION['permissions'];
if (isset($_SESSION['restatename'])) {
} else {
?>
  <script>
    window.location.href = "/";
  </script>
<?php
}
?>
<div class="sidebar-wrapper">
  <div>
    <div class="logo-wrapper"><a href="dashboard.php"><img class="img-fluid for-light" src="<?php echo $set['weblogo']; ?>" alt=""><img class="img-fluid for-dark" src="assets/images/logo/logo-dark.png" alt=""></a>
      <div class="back-btn"><i class="fa fa-angle-left"></i></div>
      <div class="toggle-sidebar"><i class="status_toggle middle sidebar-toggle" data-feather="align-left"> </i></div>
    </div>
    <div class="logo-icon-wrapper"><a href="dashboard.php"><img class="img-fluid for-light" src="<?php echo $set['weblogo']; ?>" alt=""><img class="img-fluid for-dark" src="assets/images/logo/logo-icon-dark.png" alt=""></a></div>
    <nav class="sidebar-main">
      <div class="left-arrow" id="left-arrow"><i data-feather="arrow-left"></i></div>
      <div id="sidebar-menu">
        <ul class="sidebar-links" id="simple-bar">
          <li class="back-btn"><a href="dashboard.php"><img class="img-fluid for-light" src="<?php echo $set['weblogo']; ?>" alt=""><img class="img-fluid for-dark" src="assets/images/logo/logo-icon-dark.png" alt=""></a>
            <div class="mobile-back text-end"><span>
                <?= $lang['Back'] ?>
              </span><i class="fa fa-angle-right ps-2" aria-hidden="true"></i></div>
          </li>

          <li class="sidebar-list"> <a class="sidebar-link sidebar-title link-nav" href="dashboard.php"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <g>
                  <g>
                    <path d="M9.07861 16.1355H14.8936" stroke="#130F26" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                    <path fill-rule="evenodd" clip-rule="evenodd" d="M2.3999 13.713C2.3999 8.082 3.0139 8.475 6.3189 5.41C7.7649 4.246 10.0149 2 11.9579 2C13.8999 2 16.1949 4.235 17.6539 5.41C20.9589 8.475 21.5719 8.082 21.5719 13.713C21.5719 22 19.6129 22 11.9859 22C4.3589 22 2.3999 22 2.3999 13.713Z" stroke="#130F26" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                  </g>
                </g>
              </svg><span> <?= $lang['Dashboard'] ?></span></a></li>
          <?php
          if (in_array('Update_Category', $per) || in_array('Delete_Category', $per) || in_array('Read_Category', $per) || in_array('Create_Category', $per)) {
          ?>
            <li class="sidebar-list"><a class="sidebar-link sidebar-title" href="javascript:void(0)"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <g>
                    <g>
                      <path d="M15.596 15.6963H8.37598" stroke="#130F26" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                      <path d="M15.596 11.9365H8.37598" stroke="#130F26" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                      <path d="M11.1312 8.17725H8.37622" stroke="#130F26" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                      <path fill-rule="evenodd" clip-rule="evenodd" d="M3.61011 12C3.61011 18.937 5.70811 21.25 12.0011 21.25C18.2951 21.25 20.3921 18.937 20.3921 12C20.3921 5.063 18.2951 2.75 12.0011 2.75C5.70811 2.75 3.61011 5.063 3.61011 12Z" stroke="#130F26" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                    </g>
                  </g>
                </svg><span>
                  <?= $lang['Category'] ?>

                </span></a>
              <ul class="sidebar-submenu">
                <?php
                if (in_array('Create_Category', $per)) {
                ?>
                  <li><a href="add_category.php">
                      <?= $lang['Add_Category'] ?>
                    </a></li>
                <?php
                }
                ?>
                <?php
                if (in_array('Read_Category', $per)) {
                ?>
                  <li><a href="list_category.php">
                      <?= $lang['List_Category'] ?>

                    </a></li>
                <?php
                }
                ?>
              </ul>
            </li>
          <?php
          }
          ?>

          <?php
          if (in_array('Update_Coupon', $per) || in_array('Delete_Coupon', $per) || in_array('Read_Coupon', $per) || in_array('Create_Coupon', $per)) {
          ?>
            <li class="sidebar-list"><a class="sidebar-link sidebar-title" href="javascript:void(0)"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <g>
                    <g>
                      <path fill-rule="evenodd" clip-rule="evenodd" d="M2.75024 12C2.75024 5.063 5.06324 2.75 12.0002 2.75C18.9372 2.75 21.2502 5.063 21.2502 12C21.2502 18.937 18.9372 21.25 12.0002 21.25C5.06324 21.25 2.75024 18.937 2.75024 12Z" stroke="#130F26" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                      <path d="M9.42993 14.5697L14.5699 9.42969" stroke="#130F26" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                      <path d="M14.4955 14.5H14.5045" stroke="#130F26" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                      <path d="M9.4955 9.5H9.5045" stroke="#130F26" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                    </g>
                  </g>
                </svg><span>Coupon</span></a>
              <ul class="sidebar-submenu">
                <?php
                if (in_array('Create_Coupon', $per)) {
                ?>
                  <li><a href="add_coupon.php">Add Coupon</a></li>
                <?php
                }
                ?>
                <?php
                if (in_array('Read_Coupon', $per)) {
                ?>
                  <li><a href="list_coupon.php">List Coupon</a></li>
                <?php
                }
                ?>
              </ul>
            </li>
          <?php
          }
          ?>

          <?php
          if (in_array('Update_Payout', $per) || in_array('Delete_Payout', $per) || in_array('Read_Payout', $per) || in_array('Create_Payout', $per)) {
          ?>
            <li class="sidebar-list">
              <a class="sidebar-link sidebar-title" href="javascript:void(0)">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <!-- Simple money bill with downward arrow -->
                  <rect x="4" y="6" width="16" height="12" rx="2" stroke="#130F26" stroke-width="1.5" />
                  <path d="M8 10H16" stroke="#130F26" stroke-width="1.5" stroke-linecap="round" />
                  <path d="M12 14L12 18M12 18L9 15M12 18L15 15" stroke="#130F26" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
                <span>Payouts</span>
              </a>
              <ul class="sidebar-submenu">
                <?php
                if (in_array('Read_Payout', $per)) {
                ?>
                  <li><a href="completed_payout.php">Completed Payouts</a></li>
                  <li><a href="pending_payout.php">Pending Payouts</a></li>
                  <li><a href="rejected_payout.php">Rejected Payouts</a></li>
                <?php
                }
                ?>
              </ul>
            </li>
          <?php
          }
          ?>
          <?php
          if (in_array('Update_Payout_Method', $per) || in_array('Delete_Payout_Method', $per) || in_array('Read_Payout_Method', $per) || in_array('Create_Payout_Method', $per)) {
          ?>
            <li class="sidebar-list">
              <a class="sidebar-link sidebar-title" href="javascript:void(0)">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <!-- Credit card with multiple payment method symbols -->
                  <rect x="4" y="6" width="16" height="12" rx="2" stroke="#130F26" stroke-width="1.5" />
                  <path d="M4 10H20" stroke="#130F26" stroke-width="1.5" stroke-linecap="round" />

                </svg>
                <span>Payout Methods</span>
              </a>
              <ul class="sidebar-submenu">
                <?php
                if (in_array('Create_Payout_Method', $per)) {
                ?>
                  <li><a href="add_payout_method.php">Add Payout Method</a></li>
                <?php
                }
                ?>
                <?php
                if (in_array('Read_Payout_Method', $per)) {
                ?>
                  <li><a href="list_payout_method.php">List Payout Methods</a></li>
                <?php
                }
                ?>
              </ul>
            </li>
          <?php
          }
          ?>
          <?php
          if (in_array('Update_Property', $per) || in_array('Delete_Property', $per) || in_array('Read_Property', $per) || in_array('Create_Property', $per)) {
          ?>
            <li class="sidebar-list"> <a class="sidebar-link sidebar-title" href="javascript:void(0)"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <g>
                    <g>
                      <path fill-rule="evenodd" clip-rule="evenodd" d="M2.75024 12C2.75024 5.063 5.06324 2.75 12.0002 2.75C18.9372 2.75 21.2502 5.063 21.2502 12C21.2502 18.937 18.9372 21.25 12.0002 21.25C5.06324 21.25 2.75024 18.937 2.75024 12Z" stroke="#130F26" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                      <path d="M15.2045 13.8999H15.2135" stroke="#130F26" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                      <path d="M12.2045 9.8999H12.2135" stroke="#130F26" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                      <path d="M9.19557 13.8999H9.20457" stroke="#130F26" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                    </g>
                  </g>
                </svg><span>Properties </span></a>
              <ul class="sidebar-submenu">
                <?php
                if (in_array('Create_Property', $per)) {
                ?>
                  <li><a href="add_properties.php">Add Properties</a></li>
                <?php
                }
                ?>
                <?php

                if (in_array('Read_Property', $per)) {
                ?>
                  <li><a href="list_properties.php">List Properties</a></li>
                <?php
                }
                ?>
                <?php

                if (in_array('Read_Property', $per)) {
                ?>
                  <li><a href="pending_properties.php">Pending Properties</a></li>
                <?php
                }
                ?>
              </ul>
            </li>
          <?php
          }
          ?>


          <?php
          if (in_array('Update_Facility', $per) || in_array('Delete_Facility', $per) || in_array('Read_Facility', $per) || in_array('Create_Facility', $per)) {
          ?>
            <li class="sidebar-list"> <a class="sidebar-link sidebar-title" href="javascript:void(0)"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <g>
                    <g>
                      <path d="M11.0791 13.8496H7.4314" stroke="#130F26" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                      <path d="M15.4774 12.1712H15.3752" stroke="#130F26" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                      <path d="M17.2081 15.5833H17.1059" stroke="#130F26" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                      <path d="M8.51392 2.21606C8.5206 2.93015 9.1058 3.50295 9.81989 3.49626H10.828C11.9306 3.48767 12.8328 4.37169 12.8481 5.47432V6.48148" stroke="#130F26" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                      <path fill-rule="evenodd" clip-rule="evenodd" d="M21.8121 13.8953C21.8121 8.33539 19.4255 6.48145 12.2646 6.48145C5.10271 6.48145 2.71606 8.33539 2.71606 13.8953C2.71606 19.4562 5.10271 21.3092 12.2646 21.3092C19.4255 21.3092 21.8121 19.4562 21.8121 13.8953Z" stroke="#130F26" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                    </g>
                  </g>
                </svg><span>Facility </span></a>
              <ul class="sidebar-submenu">
                <?php

                if (in_array('Create_Facility', $per)) {
                ?>
                  <li><a href="add_facility.php">Add Facility</a></li>
                <?php
                }
                ?>
                <?php

                if (in_array('Read_Facility', $per)) {
                ?>
                  <li><a href="list_facility.php">List Facility</a></li>
                <?php
                }
                ?>
              </ul>
            </li>
          <?php
          }
          ?>
          <?php
          if (in_array('Update_FAQ', $per) || in_array('Delete_FAQ', $per) || in_array('Read_FAQ', $per) || in_array('Create_FAQ', $per)) {
          ?>
            <li class="sidebar-list"><a class="sidebar-link sidebar-title" href="javascript:void(0)"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <g>
                    <g>
                      <path d="M8.44019 12L10.8142 14.373L15.5602 9.62695" stroke="#130F26" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                      <path fill-rule="evenodd" clip-rule="evenodd" d="M2.74976 12C2.74976 18.937 5.06276 21.25 11.9998 21.25C18.9368 21.25 21.2498 18.937 21.2498 12C21.2498 5.063 18.9368 2.75 11.9998 2.75C5.06276 2.75 2.74976 5.063 2.74976 12Z" stroke="#130F26" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                    </g>
                  </g>
                </svg><span>FAQ</span></a>
              <ul class="sidebar-submenu">
                <?php
                if (in_array('Create_FAQ', $per)) {
                ?>
                  <li><a href="add_faq.php">Add FAQ</a></li>
                <?php
                }
                ?>
                <?php
                if (in_array('Read_FAQ', $per)) {
                ?>
                  <li><a href="list_faq.php">List FAQ</a></li>
                <?php
                }
                ?>
              </ul>
            </li>
          <?php
          }
          ?>
          <?php
          if (in_array('Update_Slider', $per) || in_array('Delete_Slider', $per) || in_array('Read_Slider', $per) || in_array('Create_Slider', $per)) {
          ?>
            <li class="sidebar-list"> <a class="sidebar-link sidebar-title" href="javascript:void(0)">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <rect x="4" y="7" width="16" height="10" rx="1" stroke="#130F26" stroke-width="1.5" />
                  <circle cx="9" cy="12" r="1" fill="#130F26" />
                  <circle cx="15" cy="12" r="1" fill="#130F26" />
                </svg>
                <span>Slider </span></a>
              <ul class="sidebar-submenu">
                <?php
                if (in_array('Create_Slider', $per)) {
                ?>
                  <li><a href="add_slider.php">Add Slider</a></li>
                <?php
                }
                ?>
                <?php
                if (in_array('Read_Slider', $per)) {
                ?>
                  <li><a href="list_slider.php">List Slider</a></li>
                <?php
                }
                ?>
              </ul>
            </li>
          <?php
          }
          ?>
          <?php
          if (in_array('Update_Booking', $per) || in_array('Delete_Booking', $per) || in_array('Read_Booking', $per) || in_array('Create_Booking', $per)) {
          ?>

            <li class="sidebar-list"> <a class="sidebar-link sidebar-title" href="javascript:void(0)"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <g>
                    <g>
                      <path fill-rule="evenodd" clip-rule="evenodd" d="M2.74976 12.7755C2.74976 5.81947 5.06876 3.50146 12.0238 3.50146C18.9798 3.50146 21.2988 5.81947 21.2988 12.7755C21.2988 19.7315 18.9798 22.0495 12.0238 22.0495C5.06876 22.0495 2.74976 19.7315 2.74976 12.7755Z" stroke="#130F26" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                      <path d="M3.02515 9.32397H21.0331" stroke="#130F26" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                      <path d="M16.4284 13.261H16.4374" stroke="#130F26" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                      <path d="M12.0289 13.261H12.0379" stroke="#130F26" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                      <path d="M7.62148 13.261H7.63048" stroke="#130F26" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                      <path d="M16.4284 17.113H16.4374" stroke="#130F26" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                      <path d="M12.0289 17.113H12.0379" stroke="#130F26" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                      <path d="M7.62148 17.113H7.63048" stroke="#130F26" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"> </path>
                    </g>
                  </g>
                </svg><span>Booking </span></a>
              <ul class="sidebar-submenu">
                <?php
                if (in_array('Read_Booking', $per)) {
                ?>
                  <li><a href="pending.php">Pending Booking</a></li>
                  <li><a href="approved.php">Approved Booking</a></li>
                  <li><a href="check_in.php">Check In Booking</a></li>
                  <li><a href="completed.php">Completed Booking</a></li>
                  <li><a href="cancelled.php">Cancelled Booking</a></li>
                  <li><a href="rating_list.php">Rating List</a></li>
                  <li><a href="temporal_booking.php">On Hold Booking </a></li>
                <?php
                }
                ?>
              </ul>
            </li>
          <?php
          }
          ?>
          <?php
          if (in_array('Update_Report', $per) || in_array('Delete_Report', $per) || in_array('Read_Report', $per) || in_array('Create_Report', $per)) {
          ?>

            <li class="sidebar-list"> <a class="sidebar-link sidebar-title" href="javascript:void(0)">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <path d="M8 3V5" stroke="#130F26" stroke-width="1.5" stroke-linecap="round" />
                  <path d="M16 3V5" stroke="#130F26" stroke-width="1.5" stroke-linecap="round" />
                  <path d="M3 8H21" stroke="#130F26" stroke-width="1.5" stroke-linecap="round" />
                  <path d="M19 8V17C19 18.1046 18.1046 19 17 19H7C5.89543 19 5 18.1046 5 17V8" stroke="#130F26" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                  <path d="M9 12H15" stroke="#130F26" stroke-width="1.5" stroke-linecap="round" />
                  <path d="M9 16H15" stroke="#130F26" stroke-width="1.5" stroke-linecap="round" />
                </svg>
                <span>Reports</span>
              </a>
              <ul class="sidebar-submenu">
                <?php
                if (in_array('Read_Report', $per)) {
                ?>
                  <li><a href="earning_report.php">Earning Report</a></li>
                  <li><a href="active_user_report.php">Most Active Users Report</a></li>
                  <li><a href="active_properties_report.php">Most Active Properties Report</a></li>

                <?php
                }
                ?>
              </ul>
            </li>
          <?php
          }
          ?>
          <?php
          if (in_array('Update_Cancel_Reason', $per) || in_array('Delete_Cancel_Reason', $per) || in_array('Read_Cancel_Reason', $per) || in_array('Create_Cancel_Reason', $per)) {
          ?>
            <li class="sidebar-list">
              <a class="sidebar-link sidebar-title" href="javascript:void(0)">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <!-- Crown icon for owner -->
                  <path d="M5 16L3 9L7 11L12 4L17 11L21 9L19 16H5Z" stroke="#130F26" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                  <path d="M5 16H19V19C19 20.1046 18.1046 21 17 21H7C5.89543 21 5 20.1046 5 19V16Z" stroke="#130F26" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
                <span>Owner Reasons</span>
              </a>
              <ul class="sidebar-submenu">
                <?php
                if (in_array('Create_Cancel_Reason', $per)) {
                ?>
                  <li><a href="add_cancel_reason.php">Add Cancel Reason</a></li>
                <?php
                }
                ?>
                <?php
                if (in_array('Read_Cancel_Reason', $per)) {
                ?>
                  <li><a href="list_cancel_reason.php">List Cancel Reason</a></li>
                <?php
                }
                ?>
              </ul>
            </li>

            <li class="sidebar-list">
              <a class="sidebar-link sidebar-title" href="javascript:void(0)">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <!-- User icon for user reasons -->
                  <circle cx="12" cy="7" r="4" stroke="#130F26" stroke-width="1.5" />
                  <path d="M5 21V19C5 16.7909 6.79086 15 9 15H15C17.2091 15 19 16.7909 19 19V21" stroke="#130F26" stroke-width="1.5" stroke-linecap="round" />
                </svg>
                <span>User Reasons</span>
              </a>
              <ul class="sidebar-submenu">
                <?php
                if (in_array('Create_Cancel_Reason', $per)) {
                ?>
                  <li><a href="add_user_cancel_reason.php">Add Cancel Reason</a></li>
                <?php
                }
                ?>
                <?php
                if (in_array('Read_Cancel_Reason', $per)) {
                ?>
                  <li><a href="list_user_cancel_reason.php">List Cancel Reason</a></li>
                <?php
                }
                ?>
              </ul>
            </li>
          <?php
          }
          ?>
          <?php
          if (in_array('Update_Cancellation_Policy', $per) || in_array('Delete_Cancellation_Policy', $per) || in_array('Read_Cancellation_Policy', $per) || in_array('Create_Cancellation_Policy', $per)) {
          ?>
            <li class="sidebar-list"> <a class="sidebar-link sidebar-title" href="javascript:void(0)"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <g>
                    <g>
                      <!-- Circle (policy boundary) -->
                      <path d="M12 22C17.5228 22 22 17.5228 22 12C22 6.47715 17.5228 2 12 2C6.47715 2 2 6.47715 2 12C2 17.5228 6.47715 22 12 22Z" stroke="#130F26" stroke-width="1.5" />
                      <!-- Diagonal strike (cancellation) -->
                      <path d="M6 6L18 18" stroke="#FF3B30" stroke-width="2" stroke-linecap="round" />
                      <!-- Optional: Small dotted lines (like your original) -->
                      <path d="M10 12H10.01" stroke="#130F26" stroke-width="2" stroke-linecap="round" />
                      <path d="M14 12H14.01" stroke="#130F26" stroke-width="2" stroke-linecap="round" />
                    </g>
                  </g>
                </svg><span>Cancellation Policies </span></a>
              <ul class="sidebar-submenu">
                <?php
                if (in_array('Create_Cancellation_Policy', $per)) {
                ?>
                  <li><a href="add_policies.php">Add Policies</a></li>
                <?php
                }
                ?>
                <?php
                if (in_array('Read_Cancellation_Policy', $per)) {
                ?>
                  <li><a href="list_policies.php">List Policies</a></li>
                <?php
                }
                ?>
              </ul>
            </li>
          <?php
          }
          ?>
          <?php
          if (in_array('Update_Chat', $per) || in_array('Delete_Chat', $per) || in_array('Read_Chat', $per) || in_array('Create_Chat', $per)) {
          ?>
            <li class="sidebar-list">
              <a class="sidebar-link sidebar-title" href="javascript:void(0)">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <!-- Outlined chat bubble -->
                  <path d="M20 2H4c-1.1 0-2 .9-2 2v18l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm0 14H6l-2 2V4h16v12z"
                    fill="none"
                    stroke="#130F26"
                    stroke-width="1" />

                  <!-- Outlined message lines -->
                  <path d="M7 9h10v1H7z"
                    fill="none"
                    stroke="#130F26"
                    stroke-width="1"
                    stroke-linecap="round" />
                  <path d="M7 13h7v1H7z"
                    fill="none"
                    stroke="#130F26"
                    stroke-width="1"
                    stroke-linecap="round" />
                </svg>
                <span>Chat</span>
              </a>
              <ul class="sidebar-submenu">
                <li><a href="pending_chat.php">Pending Chat</a></li>
              </ul>
            </li>
          <?php
          }
          ?>

          <?php
          if (
            in_array('Update_Admin_User', $per) ||
            in_array('Delete_Admin_User', $per) ||
            in_array('Read_Admin_User', $per) ||
            in_array('Create_Admin_User', $per)
          ):
          ?>
            <li class="sidebar-list">
              <a class="sidebar-link sidebar-title" href="javascript:void(0)">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <!-- User silhouette -->
                  <path d="M12 12C14.2091 12 16 10.2091 16 8C16 5.79086 14.2091 4 12 4C9.79086 4 8 5.79086 8 8C8 10.2091 9.79086 12 12 12Z" stroke="#130F26" stroke-width="1.5" />
                  <path d="M20 18C20 15.7909 16.4183 14 12 14C7.58172 14 4 15.7909 4 18" stroke="#130F26" stroke-width="1.5" stroke-linecap="round" />
                  <!-- Admin badge/shield -->
                  <path d="M18 9V8C18 6.89543 17.1046 6 16 6H8C6.89543 6 6 6.89543 6 8V9" stroke="#FF3B30" stroke-width="1.5" />
                  <circle cx="12" cy="10" r="1" fill="#FF3B30" />
                </svg>
                <span>Admin Users</span>
              </a>
              <ul class="sidebar-submenu">
                <?php if (in_array('Create_Admin_User', $per)): ?>
                  <li><a href="add_admin_user.php">Add New User</a></li>
                <?php endif; ?>

                <?php if (in_array('Read_Admin_User', $per)): ?>
                  <li><a href="list_admin_user.php">List Admin Users</a></li>
                <?php endif; ?>
              </ul>
            </li>
          <?php endif; ?>
          <?php
          if (in_array('Update_User_List', $per) || in_array('Delete_User_List', $per) || in_array('Read_User_List', $per) || in_array('Create_User_List', $per)) {
          ?>
            <li class="sidebar-list"> <a class="sidebar-link sidebar-title link-nav" href="userlist.php"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <g>
                    <g>
                      <path fill-rule="evenodd" clip-rule="evenodd" d="M9.55851 21.4562C5.88651 21.4562 2.74951 20.9012 2.74951 18.6772C2.74951 16.4532 5.86651 14.4492 9.55851 14.4492C13.2305 14.4492 16.3665 16.4342 16.3665 18.6572C16.3665 20.8802 13.2505 21.4562 9.55851 21.4562Z" stroke="#130F26" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                      <path fill-rule="evenodd" clip-rule="evenodd" d="M9.55849 11.2776C11.9685 11.2776 13.9225 9.32356 13.9225 6.91356C13.9225 4.50356 11.9685 2.54956 9.55849 2.54956C7.14849 2.54956 5.19449 4.50356 5.19449 6.91356C5.18549 9.31556 7.12649 11.2696 9.52749 11.2776H9.55849Z" stroke="#130F26" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                      <path d="M16.8013 10.0789C18.2043 9.70388 19.2383 8.42488 19.2383 6.90288C19.2393 5.31488 18.1123 3.98888 16.6143 3.68188" stroke="#130F26" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                      <path d="M17.4608 13.6536C19.4488 13.6536 21.1468 15.0016 21.1468 16.2046C21.1468 16.9136 20.5618 17.6416 19.6718 17.8506" stroke="#130F26" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                    </g>
                  </g>
                </svg><span>User List</span></a></li>
          <?php
          }
          ?>

          <li class="sidebar-list"> <a class="sidebar-link sidebar-title link-nav" href="profile.php"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <g>
                  <g>
                    <path fill-rule="evenodd" clip-rule="evenodd" d="M9.92234 21.8083C6.10834 21.8083 2.85034 21.2313 2.85034 18.9213C2.85034 16.6113 6.08734 14.5103 9.92234 14.5103C13.7363 14.5103 16.9943 16.5913 16.9943 18.9003C16.9943 21.2093 13.7573 21.8083 9.92234 21.8083Z" stroke="#130F26" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                    <path fill-rule="evenodd" clip-rule="evenodd" d="M9.92231 11.2159C12.4253 11.2159 14.4553 9.1859 14.4553 6.6829C14.4553 4.1789 12.4253 2.1499 9.92231 2.1499C7.41931 2.1499 5.38931 4.1789 5.38931 6.6829C5.38031 9.1769 7.39631 11.2069 9.89031 11.2159H9.92231Z" stroke="#130F26" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                  </g>
                </g>
              </svg><span>Account</span></a></li>
          <?php
          if (in_array('Update_Setting', $per) || in_array('Read_Setting', $per)) {
          ?>
            <li class="sidebar-list"> <a class="sidebar-link sidebar-title" href="javascript:void(0)"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <g>
                    <g>
                      <path d="M11.1437 17.8829H4.67114" stroke="#130F26" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                      <path fill-rule="evenodd" clip-rule="evenodd" d="M15.205 17.8839C15.205 19.9257 15.8859 20.6057 17.9267 20.6057C19.9676 20.6057 20.6485 19.9257 20.6485 17.8839C20.6485 15.8421 19.9676 15.1621 17.9267 15.1621C15.8859 15.1621 15.205 15.8421 15.205 17.8839Z" stroke="#130F26" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                      <path d="M14.1765 7.39439H20.6481" stroke="#130F26" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                      <path fill-rule="evenodd" clip-rule="evenodd" d="M10.1153 7.39293C10.1153 5.35204 9.43436 4.67114 7.39346 4.67114C5.35167 4.67114 4.67078 5.35204 4.67078 7.39293C4.67078 9.43472 5.35167 10.1147 7.39346 10.1147C9.43436 10.1147 10.1153 9.43472 10.1153 7.39293Z" stroke="#130F26" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                    </g>
                  </g>
                </svg><span>Setting</span></a>
              <ul class="sidebar-submenu">
                <?php if ((in_array('Read_Setting', $per) && in_array('Delete_Setting', $per))): ?>

                  <li><a href="setting.php">Edit Setting</a></li>
                <?php endif; ?>

                <li><a href="add_privacy_policy.php">Edit Privacy Policy</a></li>
                <li><a href="add_terms_and_conditions.php">Edit Guest Terms And Conditions</a></li>
                <li><a href="add_host_terms_and_conditions.php">Edit Host Terms And Conditions</a></li>
                <li><a href="add_confidence_booking.php">Edit Confidence Booking </a></li>
                <li><a href="add_guidelines.php">Edit Content Guidelines</a></li>
                <li><a href="add_listing_guidelines.php">Edit Listing Guidelines</a></li>
                <li><a href="add_guest_cancellation_policies.php">Edit Guest Cancellation Policies</a></li>
                <li><a href="add_host_cancellation_policies.php">Edit Host Cancellation Policies</a></li>
              </ul>
            </li>
          <?php
          }
          ?>

          <li class="sidebar-list">
            <a class="sidebar-link sidebar-title" href="javascript:void(0)">
              <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <!-- Thin stroke version (1px width) -->
                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"
                  fill="none"
                  stroke="#130F26"
                  stroke-width="1" /> <!-- Reduced from 1.5 to 1 -->
              </svg>
              <span>WhatsApp</span>
            </a>
            <ul class="sidebar-submenu">
              <li><a href="add_whatsapp_qr.php">Add WhatsApp QR</a></li>
              <li><a href="campings.php">Campings</a></li>
              <li><a href="users.php">Users</a></li>
              <li><a href="owners.php">Owners</a></li>
            </ul>
          </li>
          <?php
          if (in_array('Update_Wallet', $per) || in_array('Delete_Wallet', $per) || in_array('Read_Wallet', $per) || in_array('Create_Wallet', $per)) {
          ?>
            <li class="sidebar-list"><a class="sidebar-link sidebar-title" href="javascript:void(0)">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <path d="M19 7H5C3.89543 7 3 7.89543 3 9V19C3 20.1046 3.89543 21 5 21H19C20.1046 21 21 20.1046 21 19V9C21 7.89543 20.1046 7 19 7Z" stroke="#130F26" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                  <path d="M16 7V5C16 4.46957 15.7893 3.96086 15.4142 3.58579C15.0391 3.21071 14.5304 3 14 3H10C9.46957 3 8.96086 3.21071 8.58579 3.58579C8.21071 3.96086 8 4.46957 8 5V7" stroke="#130F26" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                  <path d="M16 15H16.01" stroke="#130F26" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
                <span>Wallet</span>
              </a>
              <ul class="sidebar-submenu">
                <?php
                if (in_array('Create_Wallet', $per)) {
                ?>
                  <li><a href="add_money.php">Add Money</a></li>
                <?php
                }
                ?>
                <?php
                if (in_array('Read_Wallet', $per)) {
                ?>
                  <li><a href="wallet_history.php">Wallet History</a></li>
                <?php
                }
                ?>
              </ul>
            </li>
          <?php
          }
          ?>
          <?php
          if (in_array('Update_Why_Choose_Us', $per) || in_array('Delete_Why_Choose_Us', $per) || in_array('Read_Why_Choose_Us', $per) || in_array('Create_Why_Choose_Us', $per)) {
          ?>
            <li class="sidebar-list"><a class="sidebar-link sidebar-title" href="javascript:void(0)">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <path d="M9 12l2 2 4-4" stroke="#130F26" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                  <circle cx="12" cy="12" r="9" stroke="#130F26" stroke-width="1.5"></circle>
                </svg>
                <span>Why Choose Us</span></a>
              <ul class="sidebar-submenu">
                <?php
                if (in_array('Create_Why_Choose_Us', $per)) {
                ?>
                  <li><a href="add_why_choose_us.php">Add Why Choose Us</a></li>
                <?php
                }
                ?>
                <?php
                if (in_array('Read_Why_Choose_Us', $per)) {
                ?>
                  <li><a href="list_why_choose_us.php">List Why Choose Us</a></li>
                <?php
                }
                ?>
              </ul>
            </li>
          <?php
          }
          ?>

          <li class="sidebar-list"> <a class="sidebar-link sidebar-title link-nav" href="logout.php"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="logout" style="transform: rotate(90deg);">
                <g>
                  <g>
                    <path d="M11.879 14.791V2.75" stroke="#130F26" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                    <path d="M14.795 11.8643L11.879 14.7923L8.96301 11.8643" stroke="#130F26" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                    <path d="M16.3702 7.25879C19.9492 7.58879 21.2502 8.92879 21.2502 14.2588C21.2502 21.3588 18.9392 21.3588 12.0002 21.3588C5.05924 21.3588 2.75024 21.3588 2.75024 14.2588C2.75024 8.92879 4.05024 7.58879 7.63024 7.25879" stroke="#130F26" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                  </g>
                </g>
              </svg><span>Log out</span></a></li>
        </ul>

      </div>
      <div class="right-arrow" id="right-arrow"><i data-feather="arrow-right"></i></div>
    </nav>
  </div>
</div>