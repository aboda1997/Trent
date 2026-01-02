<?php
include 'include/reconfig.php';
include 'include/estate.php';
include_once 'include/load_language.php';
$lang_code = load_language_code();
$lang_ar = load_specific_langauage('ar');
$lang_en = load_specific_langauage('en');

?>
<!DOCTYPE html>
<html lang="<?= $lang_code["language_code"] ?>" dir="<?= $lang_code["dir"] ?>">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="icon" href="<?php echo $set['weblogo']; ?>" type="image/x-icon">
    <link rel="shortcut icon" href="<?php echo $set['weblogo']; ?>" type="image/x-icon">
    <title>Admin Panel -- <?php echo $set['webname']; ?></title>
    <!-- Google font-->
    <link rel="preconnect" href="https://fonts.googleapis.com/">
    <link rel="preconnect" href="https://fonts.gstatic.com/" crossorigin="">
    <link href="https://fonts.googleapis.com/css2?family=Rubik:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,300;1,400;1,500;1,600;1,700;1,800;1,900&amp;display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&amp;display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&amp;display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css" integrity="sha512-MV7K8+y+gLIBoVD59lQIYicR65iaqukzvf/nwasF0nqhPay5w/9lJmVM2hMDcnK1OnMGCdVK+iQrJ7lzPJQd1w==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <!-- ico-font-->


    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.20/summernote.min.css" integrity="sha512-m52YCZLrqQpQ+k+84rmWjrrkXAUrpl3HK0IO4/naRwp58pyr7rf5PO1DbI2/aFYwyeIH/8teS9HbLxVyGqDv/A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" type="text/css" href="assets/css/vendors/datatables.css">
    <link rel="stylesheet" type="text/css" href="assets/css/vendors/select2.css">
    <link rel="stylesheet" type="text/css" href="assets/bootstrap-tagsinput/bootstrap-tagsinput.css">
    <link rel="stylesheet" type="text/css" href="assets/css/vendors/feather-icon.css">
    <link rel="stylesheet" type="text/css" href="assets/css/vendors/scrollbar.css">

    <link rel="stylesheet" type="text/css" href="assets/css/vendors/slick.css">
    <link rel="stylesheet" type="text/css" href="assets/css/vendors/slick-theme.css">
    <link rel="stylesheet" type="text/css" href="assets/css/vendors/prism.css">
    <!-- Bootstrap css-->
    <link rel="stylesheet" type="text/css" href="assets/css/vendors/bootstrap.css">
    <!-- App css-->
    <link rel="stylesheet" type="text/css" href="assets/css/style_v1.css">

    <!-- Responsive css-->
    <link rel="stylesheet" type="text/css" href="assets/css/responsive_v1.css">

    <style>
        .hidden {
            display: none;
        }

        .page-wrapper .page-header {
            z-index: 1;

        }

        /* Media query for screen widths between 1200px and 1400px */
        @media (max-width: 1400px) and (min-width: 1200px) {

            [dir="rtl"] .page-body-wrapper .page-body,
            [dir="rtl"] .page-body-wrapper footer {
                margin-right: 85px !important;
                margin-left: 0px !important;
            }
        }

        /* General rule for right-to-left documents */
        [dir="rtl"] {
            text-align: right;
        }


        .nav-tabs .nav-link.active {

            background-color: #007bff;
            /* Change this to your preferred color */
            color: #fff;
            /* Change text color */
            font-weight: bold;
            /* Make text bold */
            border-color: #007bff;
            /* Match border color with background */
        }

        /* Align labels based on language direction */
        [dir="rtl"] .form-group {
            text-align: right;
        }

        [dir="ltr"] .form-group {
            text-align: left;
        }
    </style>

</head>

<body>
    <script src="assets/js/jquery-3.6.0.min.js"></script>
    <!-- Include Required Libraries -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css">
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/moment@2.29.1/moment.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/daterangepicker@3.1.0/daterangepicker.min.js"></script>

    <script>
        var langDataAR = <?php echo json_encode(load_specific_langauage('ar'), JSON_UNESCAPED_UNICODE); ?>;
        var langDataEN = <?php echo json_encode(load_specific_langauage('en'), JSON_UNESCAPED_UNICODE); ?>;

        function submitform(isValid) {

            $(document).on('submit', 'form', function(event) {
                // Disable all submit buttons to prevent multiple submissions
                $(':input[type="submit"]').prop('disabled', true);
                
                event.preventDefault(); // Prevent default form submission
                if (!isValid) {
                    return false;
                }
                // Create a FormData object from the submitted form
                var formData = new FormData(this);

                // Send the form data via AJAX
                $.ajax({
                    url: 'include/property.php',
                    method: 'POST',
                    async: false,
                    cache: false,
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        // Parse the JSON response
                        const resultData = JSON.parse(response);

                        // Display notification
                        $.notify('<i class="fas fa-bell"></i>' + resultData.title, {
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
                        if (resultData.action) {
                            setTimeout(function() {
                                window.location.href = resultData.action;
                            }, 2000);
                        }
                    },
                });

                // Prevent the default form submission behavior
                return false;
            });
        }
    </script>

    <script>
        $(document).ready(function() {
            // When "View Details" button is clicked
            $(".preview_d").click(function() {
                var orderId = $(this).data('id'); // Get the order ID from data-id attribute
                var modalBody = $(".modal-body.p_data"); // Target modal body

                // Show loading spinner (optional)
                modalBody.html('<div class="text-center"><i class="fa fa-spinner fa-spin"></i> Loading...</div>');

                // Load content via AJAX
                $.ajax({
                    url: 'order_product_data.php', // Path to your PHP file
                    type: 'Post',
                    data: {
                        pid: orderId
                    }, // Pass the order ID as a parameter
                    success: function(response) {
                        modalBody.html(response); // Insert the response into the modal
                    },
                    error: function(xhr, status, error) {
                        modalBody.html('<div class="alert alert-danger">Failed to load data.</div>');
                        console.error("AJAX Error:", error);
                    }
                });
            });

            // Optional: Clear modal content when closed
            $('#myModal').on('hidden.bs.modal', function() {
                $(".modal-body.p_data").html(''); // Empty the modal body
            });
        });
    </script>
    <!-- tap on top starts-->
    <div class="tap-top"><i data-feather="chevrons-up"></i></div>
    <!-- tap on tap ends-->
    <!-- Loader starts-->
    <div class="loader-wrapper">
        <div class="loader"></div>
    </div>