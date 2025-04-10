<?php
require 'include/main_head.php';
$why_us_per = ['Create', 'Update', 'Read', 'Delete'];
$lang_code = load_language_code()["language_code"];

if ($_SESSION['restatename'] == 'Staff' && !in_array('Read', $why_us_per)) {



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
                                <?= $lang['Why_Choose_List_Management'] ?>

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
                                <div class="table-responsive">
                                    <table class="display" id="basic-1">
                                        <thead>
                                            <tr>
                                                <th>
                                                    <?= $lang['Sr_No'] ?>

                                                    .</th>
                                                <th>
                                                    <?= $lang['Why_Chooser_Title'] ?>

                                                </th>
                                                <th>
                                                    <?= $lang['Why_Choose_Image'] ?>

                                                </th>
                                                <th>
                                                    <?= $lang['Why_Choose_bg'] ?>

                                                </th>

                                                <th>
                                                    <?= $lang['Why_Choose_is_Header'] ?>

                                                </th>
                                                <?php
                                                if ($_SESSION['restatename'] == 'Staff') {
                                                    if (in_array('Update', $why_us_per)) {
                                                ?>
                                                        <th>
                                                            <?= $lang['Action'] ?>

                                                        </th>
                                                    <?php
                                                    }
                                                } else {
                                                    ?>
                                                    <th>
                                                        <?= $lang['Action'] ?>

                                                    </th>
                                                <?php } ?>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $city = $rstate->query("select * from tbl_why_choose_us");
                                            $i = 0;
                                            while ($row = $city->fetch_assoc()) {
                                                $title = json_decode($row['title'], true);
                                                $description = json_decode($row['description'], true);

                                                $i = $i + 1;
                                            ?>
                                                <tr>
                                                    <td>
                                                        <?php echo $i; ?>
                                                    </td>

                                                    <td class="align-middle">
                                                        <?php echo $title[$lang_code]; ?>
                                                    </td>

                                                    <td class="align-middle">
                                                        <img src="<?php echo $row['img']; ?>" width="70" height="80" />
                                                    </td>
                                                    <td class="align-middle">
                                                        <?php echo $row['background_color']; ?>
                                                    </td>

                                                    
                                                    <td class="align-middle">
                                                        <span class="badge status-toggle <?php echo $row['is_header'] ? 'badge-success' : 'badge-danger'; ?>"
                                                            data-id="<?php echo $row['id']; ?>"
                                                            data-status="<?php echo $row['is_header']; ?>"
                                                            >
                                                            <?php echo $row['is_header']  ? "Yes" : "No"; ?>
                                                        </span>
                                                    </td>
                                                    <?php
                                                    if ($_SESSION['restatename'] == 'Staff') {
                                                        if (in_array('Update', $why_us_per)) {
                                                    ?>

                                                            <td style="white-space: nowrap; width: 15%;">
                                                                <div class="tabledit-toolbar btn-toolbar" style="text-align: left;">
                                                                    <div class="btn-group btn-group-sm" style="float: none;">

                                                                        <!-- Update Button -->
                                                                        <a href="add_why_choose_us.php?id=<?php echo $row['id']; ?>" class="tabledit-edit-button" style="float: none; margin: 5px;">
                                                                            <svg width="30" height="30" viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                                <rect width="30" height="30" rx="15" fill="#79F9B4" />
                                                                                <path d="M22.5168 9.34109L20.6589 7.48324C20.0011 6.83703 18.951 6.837 18.2933 7.49476L16.7355 9.06416L20.9359 13.2645L22.5052 11.7067C23.163 11.0489 23.163 9.99885 22.5168 9.34109ZM15.5123 10.2873L8 17.8342V22H12.1658L19.7127 14.4877L15.5123 10.2873Z" fill="#25314C" />
                                                                            </svg>
                                                                        </a>

                                                                        
                                                                    <button type="submit" class="tabledit-delete-button"
                                                                        onclick="deleteWhyChooseUs(<?php echo $row['id']; ?>)"

                                                                        style="background: none; border: none; padding: 0; cursor: pointer;">
                                                                        <svg width="30" height="30" viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                            <rect width="30" height="30" rx="15" fill="#FF6B6B" />
                                                                            <path d="M10 10L20 20M20 10L10 20" stroke="#FFFFFF" stroke-width="2" />
                                                                        </svg>
                                                                    </button>

                                                                    </div>
                                                                </div>
                                                            </td>
                                                        <?php
                                                        }
                                                    } else {
                                                        ?>
                                                        <td style="white-space: nowrap; width: 15%;">
                                                            <div class="tabledit-toolbar btn-toolbar" style="text-align: left;">
                                                                <div class="btn-group btn-group-sm" style="float: none;">

                                                                    <!-- Update Button -->
                                                                    <a href="add_why_choose_us.php?id=<?php echo $row['id']; ?>" class="tabledit-edit-button" style="float: none; margin: 5px;">
                                                                        <svg width="30" height="30" viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                            <rect width="30" height="30" rx="15" fill="#79F9B4" />
                                                                            <path d="M22.5168 9.34109L20.6589 7.48324C20.0011 6.83703 18.951 6.837 18.2933 7.49476L16.7355 9.06416L20.9359 13.2645L22.5052 11.7067C23.163 11.0489 23.163 9.99885 22.5168 9.34109ZM15.5123 10.2873L8 17.8342V22H12.1658L19.7127 14.4877L15.5123 10.2873Z" fill="#25314C" />
                                                                        </svg>
                                                                    </a>

                                                                    <!-- Delete Button -->

                                                                    <button type="submit" class="tabledit-delete-button"
                                                                        onclick="deleteWhyChooseUs(<?php echo $row['id']; ?>)"

                                                                        style="background: none; border: none; padding: 0; cursor: pointer;">
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
                                            <?php
                                            }
                                            ?>

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
<script>
    async function deleteWhyChooseUs(id) {
        if (!confirm('<?= $lang['Delete_Confirmation'] ?>?')) return;

        try {
            const response = await fetch('include/property.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `id=${id}&type=delete_why_choose_us`
            });

            let res =JSON.parse( await response.text()); // Parse the JSON response

            if (res.ResponseCode === "200" && res.Result === "true") {


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
                alert("'Error deleting data.");
            }
        } catch (error) {
            debugger;

            alert("'Error deleting data.");
        }
    }
</script>
<?php
require 'include/footer.php';
?>
</body>

</html>