<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $url = trim($_POST['url']);

    // Ganti https:// dengan http:// jika perlu
    if (stripos($url, 'https://') === 0) {
        $url = 'http://' . substr($url, 8);
    }

    // Cek apakah URL berakhir dengan .mp3
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
<html>

<head>
    <title>Tambah Link</title>
    <meta charset="UTF-8">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body,
        html {
            height: 100%;
        }

        .wrapper {
            min-height: 100%;
            display: flex;
            flex-direction: column;
        }

        .content {
            flex: 1;
        }

        .sticky-header {
            position: sticky;
            top: 0;
            z-index: 1030;
        }

        footer {
            background: #0d6efd;
            color: white;
        }
    </style>
</head>

<body class="bg-light">
    <div class="wrapper">

        <!-- Header -->
        <nav class="navbar navbar-expand-lg navbar-dark bg-primary sticky-header">
            <div class="container justify-content-center">
                <span class="navbar-brand mx-auto text-center w-100">Boombox Link Manager</span>
            </div>
        </nav>

        <!-- Error Alert -->
        <?php if ($showError): ?>
            <div id="errorAlert" class="alert alert-danger text-center m-0 rounded-0" role="alert">
                ❌ Link harus berakhir dengan <strong>.mp3</strong>!
            </div>
        <?php endif; ?>

        <!-- Content -->
        <div class="container content pt-5 pb-4">
            <h2 class="mb-4">Form Tambah Link</h2>

            <form method="POST">
                <div class="mb-3">
                    <label for="name" class="form-label">Nama</label>
                    <input type="text" name="name" id="name" class="form-control" maxlength="30"
                        pattern="[A-Za-z ]{1,30}" required autocomplete="off">
                </div>
                <div class="mb-3">
                    <label for="url" class="form-label">Link URL</label>
                    <input type="url" name="url" id="url" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-success">Simpan</button>
                <a href="index.php" class="btn btn-secondary">Kembali</a>
            </form>
        </div>

        <!-- Footer -->
        <footer class="text-center py-3 mt-auto">
            &copy; Boombox Link Manager | Joe Ramon
        </footer>
    </div>

    <!-- JavaScript -->
    <script>
        // Hanya izinkan huruf dan spasi di nama
        document.getElementById('name').addEventListener('input', function() {
            this.value = this.value.replace(/[^A-Za-z ]/g, '');
        });

        // Sembunyikan alert error setelah 2 detik
        const errorAlert = document.getElementById('errorAlert');
        if (errorAlert) {
            setTimeout(() => {
                errorAlert.style.display = 'none';
            }, 2000);
        }
    </script>
</body>

</html>