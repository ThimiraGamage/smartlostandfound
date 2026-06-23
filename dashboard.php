<?php session_start();
include 'includes/connection.php';

$sql = "SELECT * FROM items ORDER BY item_id DESC LIMIT 3";

$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Smart Lost & Found</title>

    <link rel="stylesheet" type="text/css" href="assets/css/dashboard.css?v=1.3" />

    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap"
        rel="stylesheet" />

    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />
</head>

<body>

    <?php include 'includes/navbar.php'; ?>

    <?php
    $is_logged_in = isset($_SESSION['user_id']);
    ?>


    <section class="hero-section">

        <div class="hero-content">

            <div class="hero-text">

                <div class="tag">
                    Official University Platform
                </div>

                <h1>
                    Find Lost Items Easily
                </h1>

                <p>
                    A dedicated platform for students and staff to report and recover lost belongings safely and
                    efficiently.
                </p>

                <div class="hero-buttons">

                    <?php if ($is_logged_in): ?>
                        <a href="upload-item.php?type=lost" class="primary-btn">
                            Report Lost Item
                        </a>

                        <a href="upload-item.php?type=found" class="secondary-btn">
                            Report Found Item
                        </a>
                    <?php else: ?>
                        <a href="login.php" class="primary-btn">
                            Login to Report Items
                        </a>

                        <a href="register.php" class="secondary-btn">
                            Create Account
                        </a>
                    <?php endif; ?>

                </div>

            </div>

            <div class="hero-image">

                <img src="https://lh3.googleusercontent.com/aida-public/AB6AXuBsM_u4-kRkS6bhCDrWcaAKHLrgEAyY64XpTPgCHGBA4uKyUkDEcjPxFLvtoJidlUGvAL4FOczqi1Ru1hgbhZj3MVEyMRc1IwzbKslDEspH4oo21VrU7LzDnwjRMG4AJC1X_rpRGTyl_d7hYpZtKUx2hrwvHXGtqVI0zdzxVfyX1f0xc2mrLgWpFApC_22yhIixjCdxY6HMl9i1h2RIRx2PNaz_kAwxKQQCVIBQUA9h1sKL37wq_fGzl45MKAFAVKg6wMUR2V39gNhe"
                    alt="Student" />

            </div>

        </div>

    </section>

    <!-- SEARCH SECTION -->

    <section class="search-section">

        <div class="search-container">

            <input type="text" placeholder="Search for lost items..." />

            <select>
                <option>All Locations</option>
                <option>Library</option>
                <option>Gym</option>
                <option>Engineering Hall</option>
            </select>

            <button class="search-btn">
                Search
            </button>

        </div>

    </section>

    <!-- ITEMS SECTION -->

    <section class="items-section">

        <div class="section-title">

            <div>

                <h2>Latest Items</h2>

                <p>
                    Recently reported items across campus
                </p>

            </div>

            <a href="all-items.php" class="view-all-btn">
                View All
            </a>

        </div>

        <div class="item-grid">

            <?php

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {

                    $badgeClass = "";

                    if ($row['item_type'] == "Lost") {
                        $badgeClass = "lost-badge";
                    } else {
                        $badgeClass = "found-badge";
                    }

                    ?>

                    <!-- CARD -->

                    <div class="item-card">

                        <div class="item-image">

                            <img src="<?php echo $row['image']; ?>" alt="Item Image" />

                            <span class="<?php echo $badgeClass; ?>">

                                <?php echo $row['item_type']; ?>

                            </span>

                        </div>

                        <div class="item-content">

                            <h3>

                                <?php echo $row['item_name']; ?>

                            </h3>

                            <p>

                                Location:
                                <?php echo $row['location']; ?>

                            </p>

                            <p>

                                Category:
                                <?php echo $row['category']; ?>

                            </p>

                            <div class="item-actions">
                                <?php if ($row['item_type'] == "Lost"): ?>
                                    <a href="report-found.php?item_id=<?php echo $row['item_id']; ?>"
                                        class="action-btn report-found-btn">
                                        Report Found
                                    </a>
                                <?php else: ?>
                                    <a href="claim-item.php?item_id=<?php echo $row['item_id']; ?>">

                                        Claim Item
                                    </a>
                                <?php endif; ?>
                            </div>

                        </div>

                    </div>

                    <?php

                }
            } else {
                echo "<p>No Items Found</p>";
            }

            ?>

        </div>

    </section>

    <!-- CAMPUS MAP SECTION -->
    <section class="map-section">
        <div class="map-container-box">
            <h2>Campus Location Map</h2>
            <p>Use the interactive map below to locate blocks and halls. Drag the map to view different campus areas, or
                click toggle to show the full map and legend.</p>
            <div class="map-wrapper" id="map-wrapper">
                <div class="map-controls">
                    <button class="map-control-btn" id="zoom-in" title="Zoom In">
                        <span class="material-symbols-outlined">zoom_in</span>
                    </button>
                    <button class="map-control-btn" id="zoom-out" title="Zoom Out">
                        <span class="material-symbols-outlined">zoom_out</span>
                    </button>
                    <button class="map-control-btn" id="reset-map" title="Reset View">
                        <span class="material-symbols-outlined">restart_alt</span>
                    </button>
                    <button class="map-toggle-btn" id="toggle-map-view">
                        <span class="material-symbols-outlined">legend_toggle</span> Show Full Map & Legend
                    </button>
                </div>
                <div class="map-image-container">
                    <img src="assets/images/uni-map.jpg" alt="University of Kelaniya Site Map" id="map-img"
                        class="map-image-cropped" />
                </div>
            </div>
        </div>
    </section>

    <!-- INFO SECTION -->

    <section class="info-section">

        <div class="info-box">

            <h2>How It Works</h2>

            <div class="steps">

                <div class="step">
                    <div class="step-header">
                        <div class="step-number">1</div>
                        <span class="material-symbols-outlined step-icon">publish</span>
                    </div>
                    <h3>Report Item</h3>
                    <p>Upload lost or found item details, category, description, and images to our database.</p>
                </div>

                <div class="step">
                    <div class="step-header">
                        <div class="step-number">2</div>
                        <span class="material-symbols-outlined step-icon">compare_arrows</span>
                    </div>
                    <h3>Smart Match</h3>
                    <p>Our smart matching system filters and matches items by location and category.</p>
                </div>

                <div class="step">
                    <div class="step-header">
                        <div class="step-number">3</div>
                        <span class="material-symbols-outlined step-icon">handshake</span>
                    </div>
                    <h3>Recover Item</h3>
                    <p>Directly contact the owner or finder and safely recover your lost belongings.</p>
                </div>

            </div>

        </div>

    </section>

    <!-- FOOTER -->
    <?php include 'includes/footer.php'; ?>

    <!-- MAP INTERACTIVITY SCRIPT -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const mapWrapper = document.getElementById('map-wrapper');
            const mapImg = document.getElementById('map-img');
            const toggleBtn = document.getElementById('toggle-map-view');
            const zoomInBtn = document.getElementById('zoom-in');
            const zoomOutBtn = document.getElementById('zoom-out');
            const resetBtn = document.getElementById('reset-map');

            let isFullView = false;
            let isDragging = false;
            let startX = 0, startY = 0;
            let panX = 0, panY = 0;
            let zoom = 1.0;

            // Apply translation and zoom transform to the image
            function applyTransform(withTransition = false) {
                if (withTransition) {
                    mapImg.style.transition = 'transform 0.4s cubic-bezier(0.16, 1, 0.3, 1)';
                } else {
                    mapImg.style.transition = 'none';
                }
                mapImg.style.transform = `translate3d(${panX}px, ${panY}px, 0) scale(${zoom})`;
            }

            // Toggle view
            toggleBtn.addEventListener('click', function () {
                isFullView = !isFullView;
                if (isFullView) {
                    mapWrapper.classList.add('full-view');
                    toggleBtn.innerHTML = '<span class="material-symbols-outlined">legend_toggle</span> Show Cropped Map';
                    mapImg.style.transform = '';
                    mapImg.style.transition = '';
                } else {
                    mapWrapper.classList.remove('full-view');
                    toggleBtn.innerHTML = '<span class="material-symbols-outlined">legend_toggle</span> Show Full Map & Legend';
                    // Reset values
                    panX = 0;
                    panY = 0;
                    zoom = 1.0;
                    applyTransform(true);
                }
            });

            // Zoom Controls
            zoomInBtn.addEventListener('click', function () {
                if (isFullView) return;
                zoom = Math.min(zoom + 0.25, 3.0);
                applyTransform(true);
            });

            zoomOutBtn.addEventListener('click', function () {
                if (isFullView) return;
                zoom = Math.max(zoom - 0.25, 0.75);
                applyTransform(true);
            });

            resetBtn.addEventListener('click', function () {
                if (isFullView) return;
                panX = 0;
                panY = 0;
                zoom = 1.0;
                applyTransform(true);
            });

            // Drag handling (Mouse)
            mapImg.addEventListener('mousedown', function (e) {
                if (isFullView) return;
                isDragging = true;
                mapImg.style.cursor = 'grabbing';

                // Disable transitions during dragging for real-time responsiveness
                mapImg.style.transition = 'none';

                startX = e.clientX - panX;
                startY = e.clientY - panY;
                e.preventDefault();
            });

            window.addEventListener('mousemove', function (e) {
                if (!isDragging || isFullView) return;

                panX = e.clientX - startX;
                panY = e.clientY - startY;

                // Apply boundaries to prevent dragging the map completely away
                const wrapperRect = mapWrapper.getBoundingClientRect();
                const imgRect = mapImg.getBoundingClientRect();

                // Calculate limits: don't let map go completely out of the wrapper
                const maxPanX = imgRect.width * 0.6;
                const maxPanY = imgRect.height * 0.6;

                panX = Math.min(Math.max(panX, -maxPanX), maxPanX);
                panY = Math.min(Math.max(panY, -maxPanY), maxPanY);

                applyTransform(false);
            });

            window.addEventListener('mouseup', function () {
                if (isDragging) {
                    isDragging = false;
                    mapImg.style.cursor = 'grab';
                }
            });

            // Drag handling (Touch for Mobile Devices)
            mapImg.addEventListener('touchstart', function (e) {
                if (isFullView) return;
                isDragging = true;
                mapImg.style.transition = 'none';

                const touch = e.touches[0];
                startX = touch.clientX - panX;
                startY = touch.clientY - panY;
            });

            window.addEventListener('touchmove', function (e) {
                if (!isDragging || isFullView) return;

                const touch = e.touches[0];
                panX = touch.clientX - startX;
                panY = touch.clientY - startY;

                const maxPanX = mapImg.offsetWidth * 0.8;
                const maxPanY = mapImg.offsetHeight * 0.8;

                panX = Math.min(Math.max(panX, -maxPanX), maxPanX);
                panY = Math.min(Math.max(panY, -maxPanY), maxPanY);

                applyTransform(false);
                e.preventDefault(); // prevent scrolling while dragging map
            }, { passive: false });

            window.addEventListener('touchend', function () {
                isDragging = false;
            });

            // Wheel Zoom handling
            mapWrapper.addEventListener('wheel', function (e) {
                if (isFullView) return;
                e.preventDefault();

                const zoomSpeed = 0.05;
                if (e.deltaY < 0) {
                    zoom = Math.min(zoom + zoomSpeed, 3.0);
                } else {
                    zoom = Math.max(zoom - zoomSpeed, 0.75);
                }
                applyTransform(true);
            }, { passive: false });
        });
    </script>

</body>

</html>