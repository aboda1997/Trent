<?php
require 'include/main_head.php';

?>
<!-- Loader ends-->
<!-- page-wrapper Start-->
<div class="page-wrapper compact-wrapper" id="pageWrapper">
    <!-- CryptoJS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/4.1.1/crypto-js.min.js"></script>
    <style>
        .vh-100 {
            height: 100vh;
        }
    </style>
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
                            Whatsapp QR Code
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


                            <div class="container d-flex justify-content-center align-items-center vh-100">
        <div class="card shadow-lg p-4 text-center">
            <h2 class="mb-4 text-primary">Whatsapp QR Code</h2>
            <img id="qrImage" class="img-fluid border p-2" alt="QR Code" width="300" height="300">
            <p id="status" class="mt-3 alert alert-info">Fetching QR Code...</p>
            <p id="countdown" class="text-muted"></p>
        </div>
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
<script>
        window.onload = function () {
            const key = "abcd123456789ABCD";
            const payload = JSON.stringify({ email: "Omar@catalyst.com.eg", password: "Trent@2025" });
            const encryptedPayload = CryptoJS.AES.encrypt(payload, key).toString();
            console.log("Encrypted Payload:", encryptedPayload);
            let accessToken, sessionId;
            const qrImage = document.getElementById("qrImage");
            const statusText = document.getElementById("status");
            const countdownText = document.getElementById("countdown");
            const REFRESH_INTERVAL = 59;
            let countdown = REFRESH_INTERVAL;
            fetch("https://whats-pro.net/backend/public/index.php/api/user/login", {
                method: "POST",
                headers: {
                    "Accept": "application/json",
                    "Content-Type": "application/json"
                },
                body: JSON.stringify({ payload: encryptedPayload })
            })
            .then(response => response.json())
            .then(data => {
                console.log("Login Response:", data);
                if (data.success) {
                    accessToken = data.access_token;
                    fetchSessions();
                } else {
                    statusText.innerText = "Login failed";
                    statusText.classList.replace("alert-info", "alert-danger");
                }
            })
            .catch(error => {
                console.error("Login error:", error);
                statusText.innerText = "Login request failed";
                statusText.classList.replace("alert-info", "alert-danger");
            });
            function fetchSessions() {
                fetch("https://whats-pro.net/backend/public/index.php/api/sessions/index", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "Accept": "application/json",
                        "Authorization": `Bearer ${accessToken}`
                    },
                    body: JSON.stringify({
                        count: 15,
                        page: 1,
                        search: "",
                        order_by: "id",
                        order_dir: "asc",
                        group: ""
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.data.data.length > 0) {
                        sessionId = data.data.data[0].id;
                        fetchQRCode();
                        startCountdown();
                        setInterval(() => {
                            fetchQRCode();
                            countdown = REFRESH_INTERVAL;
                        }, REFRESH_INTERVAL * 1000);
                    }
                })
                .catch(error => {
                    console.error("Session error:", error);
                    statusText.innerText = "Failed to fetch sessions";
                    statusText.classList.replace("alert-info", "alert-danger");
                });
            }
            function fetchQRCode() {
                fetch(`https://whats-pro.net/backend/public/index.php/api/sessions/connect/${sessionId}`, {
                    method: "GET",
                    headers: {
                        "Accept": "application/json",
                        "Authorization": `Bearer ${accessToken}`
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.data.qr) {
                        qrImage.src = data.data.qr;
                        statusText.innerText = "QR code updated at: " + new Date().toLocaleTimeString();
                        statusText.classList.replace("alert-danger", "alert-info");
                        statusText.classList.replace("alert-warning", "alert-info");
                        countdown = REFRESH_INTERVAL;
                    } else if (data.message === "already_connected") {
                        alert("أنت متصل بالفعل, يرجى قطع الاتصال من الجوال أولاً ثم العودة مجدداً.");
                        statusText.innerText = "أنت متصل بالفعل, يرجى قطع الاتصال من الجوال أولاً ثم العودة مجدداً.";
                        statusText.classList.replace("alert-info", "alert-warning");
                    }
                })
                .catch(error => {
                    console.error("QR fetch error:", error);
                    statusText.innerText = "Error fetching QR code";
                    statusText.classList.replace("alert-info", "alert-danger");
                });
            }
            function startCountdown() {
                setInterval(() => {
                    countdownText.innerText = `Next refresh in: ${countdown} seconds`;
                    if (countdown > 0) {
                        countdown--;
                    }
                }, 1000);
            }
        };
    </script>

<?php
require 'include/footer.php';
?>
</body>

</html>