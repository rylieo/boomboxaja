<?php
$links = file_exists('links.json') ? json_decode(file_get_contents('links.json'), true) : [];
$showSuccessAlert = isset($_GET['success']) && $_GET['success'] == 1;
?>
<!DOCTYPE html>
<html>

<head>
    <title>Daftar Link</title>
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

        .link-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 1rem;
        }

        @media (max-width: 576px) {
            .link-row {
                flex-direction: column;
                align-items: flex-start;
            }
        }

        #copyAlert {
            display: none;
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

        <!-- Alert -->
        <?php if ($showSuccessAlert): ?>
            <div id="successAlert" class="alert alert-success text-center m-0 rounded-0" role="alert">
                ✅ Link berhasil ditambahkan!
            </div>
        <?php endif; ?>
        <div id="copyAlert" class="alert alert-success text-center m-0 rounded-0" role="alert">
            ✅ Link berhasil disalin!
        </div>

        <!-- Content -->
        <div class="container content pt-5 pb-4">
            <h2 class="mb-4">Daftar Link</h2>
            <a href="add.php" class="btn btn-primary mb-4">Tambah Link</a>
            <div class="list-group">
                <?php foreach ($links as $index => $link): ?>
                    <div class="list-group-item">
                        <div class="link-row">
                            <div><strong><?= ($index + 1) ?>. <?= strtolower(htmlspecialchars($link['name'])) ?></strong></div>
                            <div>
                                <button class="btn btn-outline-secondary btn-sm copy-btn" data-url="<?= htmlspecialchars($link['url']) ?>">
                                    Copy Link
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Footer -->
        <footer class="text-center py-3 mt-auto">
            &copy; Boombox Link Manager | Joe Ramon
        </footer>

    </div>

    <!-- Script -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const alertBox = document.getElementById('copyAlert');
        document.querySelectorAll('.copy-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                const url = btn.getAttribute('data-url');
                navigator.clipboard.writeText(url).then(() => {
                    alertBox.style.display = 'block';
                    setTimeout(() => {
                        alertBox.style.display = 'none';
                    }, 2000);
                });
            });
        });

        const successAlert = document.getElementById('successAlert');
        if (successAlert) {
            setTimeout(() => {
                successAlert.style.display = 'none';
            }, 2000);
        }
    </script>
</body>

</html>