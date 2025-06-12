<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $url = trim($_POST['url']);

    // Ganti https:// dengan http:// jika perlu
    if (stripos($url, 'https://') === 0) {
        $url = 'http://' . substr($url, 8);
    }

    // Validasi .mp3
    if (strtolower(substr($url, -4)) !== '.mp3') {
        header('Location: add.php?error=1');
        exit;
    }

    $links = file_exists('links.json') ? json_decode(file_get_contents('links.json'), true) : [];
    $links[] = ['name' => $name, 'url' => $url];
    file_put_contents('links.json', json_encode($links, JSON_PRETTY_PRINT));
    header('Location: index.php?success=1');
    exit;
}

$showError = isset($_GET['error']) && $_GET['error'] == 1;
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <title>Boomboxin | Tambah Music</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body,
        html {
            height: 100%;
            font-family: 'Inter', sans-serif;
            background-color: #121212;
            color: #fff;
            padding-bottom: 56px;
            /* Space for sticky footer */
        }

        ::placeholder {
            color: #ccc !important;
            opacity: 1;
        }

        .navbar {
            background-color: #1db954 !important;
        }

        .navbar-brand {
            font-weight: 600;
            font-size: 1.5rem;
            color: #000 !important;
        }

        h2 {
            font-weight: 600;
        }

        .btn-success {
            background-color: #1db954;
            border-color: #1db954;
            font-weight: 600;
        }

        .btn-success:hover {
            background-color: #17a74a;
            border-color: #17a74a;
        }

        .btn-secondary {
            font-weight: 600;
        }

        .form-control {
            background-color: #222;
            border: none;
            color: #fff;
            font-weight: 500;
        }

        .form-control:focus {
            background-color: #2a2a2a;
            color: #fff;
            border: 1px solid #1db954;
        }

        footer {
            background-color: #000;
            color: #aaa;
            font-weight: 600;
        }

        .sticky-footer {
            position: fixed;
            bottom: 0;
            left: 0;
            width: 100%;
            z-index: 1030;
        }

        .popup-alert-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.6);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 1055;
        }

        .popup-alert {
            background-color: #e74c3c;
            color: #fff;
            padding: 1rem 2rem;
            border-radius: 10px;
            font-weight: 600;
            font-size: 1.1rem;
            text-align: center;
            max-width: 320px;
            opacity: 0;
            transform: translateY(-20px);
            animation: fadeInUp 0.3s ease forwards;
        }

        @keyframes fadeInUp {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .fadeOut {
            animation: fadeOutDown 0.4s ease forwards;
        }

        @keyframes fadeOutDown {
            to {
                opacity: 0;
                transform: translateY(20px);
            }
        }

        @media (max-width: 576px) {
            .btn {
                width: 100%;
                margin-bottom: 0.5rem;
            }

            .btn+.btn {
                margin-left: 0 !important;
            }
        }
    </style>
</head>

<body>
    <div class="wrapper d-flex flex-column min-vh-100">
        <nav class="navbar navbar-expand-lg sticky-top">
            <div class="container justify-content-center">
                <span class="navbar-brand mx-auto">BOOMBOXIN</span>
            </div>
        </nav>

        <!-- Alert Error -->
        <div id="popupAlertOverlay" class="popup-alert-overlay" style="display: <?= $showError ? 'flex' : 'none' ?>;">
            <div id="popupAlert" class="popup-alert">❌ Link harus berakhir dengan <strong>.mp3</strong>!</div>
        </div>

        <div class="container content pt-5 pb-4 flex-grow-1 px-3">
            <h2 class="mb-4">Tambah Music</h2>
            <form method="POST">
                <div class="mb-3">
                    <input type="text" name="name" id="name" class="form-control" maxlength="30" required
                        autocomplete="off" placeholder="Nama" oninput="sanitizeInput(this)">
                </div>
                <div class="mb-3">
                    <input type="url" name="url" id="url" class="form-control" required placeholder="URL">
                </div>
                <div class="d-flex flex-wrap gap-2">
                    <button type="submit" class="btn btn-success">Simpan</button>
                    <a href="index.php" class="btn btn-secondary">Kembali</a>
                </div>
            </form>
        </div>

        <footer class="text-center py-3 sticky-footer">
            &copy; BOOMBOXIN | Joe Ramon
        </footer>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function showPopupAlert(message) {
            const overlay = document.getElementById('popupAlertOverlay');
            const alertBox = document.getElementById('popupAlert');

            alertBox.innerHTML = message;
            overlay.style.display = 'flex';
            alertBox.classList.remove('fadeOut');
            alertBox.style.animation = 'fadeInUp 0.3s ease forwards';

            setTimeout(() => {
                alertBox.classList.add('fadeOut');
                setTimeout(() => {
                    overlay.style.display = 'none';
                }, 400);
            }, 2000);
        }

        <?php if ($showError): ?>
            window.onload = () => {
                showPopupAlert("❌ Link harus berakhir dengan <strong>.mp3</strong>!");
            };
        <?php endif; ?>

        function sanitizeInput(input) {
            // Hanya huruf dan spasi
            let sanitized = input.value.replace(/[^a-zA-Z\s]/g, '');
            // Maksimal 30 karakter
            if (sanitized.length > 30) {
                sanitized = sanitized.substring(0, 30);
            }
            input.value = sanitized;
        }
    </script>
</body>

</html>