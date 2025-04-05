<?php
$links = file_exists('links.json') ? json_decode(file_get_contents('links.json'), true) : [];
$showSuccessAlert = isset($_GET['success']) && $_GET['success'] == 1;
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Boombox Link Manager</title>
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
            flex-wrap: wrap;
        }

        .audio-player {
            margin-top: 0.5rem;
            width: 100%;
        }

        @media (max-width: 576px) {
            .link-row {
                flex-direction: column;
                align-items: flex-start;
            }

            .audio-player {
                width: 100%;
            }
        }

        #copyAlert {
            display: none;
        }
    </style>
</head>

<body class="bg-light">
    <div class="wrapper">

        <!-- Navbar -->
        <nav class="navbar navbar-expand-lg navbar-dark bg-primary sticky-header">
            <div class="container justify-content-center">
                <span class="navbar-brand mx-auto w-100 text-center">BOOMBOXIN</span>
            </div>
        </nav>

        <!-- Alerts -->
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

            <!-- Baris tombol tambah dan pencarian -->
            <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
                <input type="text" id="searchInput" class="form-control w-50" placeholder="Cari berdasarkan nama...">
                <a href="add.php" class="btn btn-primary">Tambah Link</a>
            </div>

            <div class="list-group" id="linkList">
                <?php $i = 1; ?>
                <?php foreach ($links as $index => $link): ?>
                    <div class="list-group-item link-item">
                        <div class="link-row">
                            <div class="flex-grow-1">
                                <strong class="link-name"><?= $i++ ?>. <?= strtolower(htmlspecialchars($link['name'])) ?></strong>
                                <audio class="audio-player mt-2" controls>
                                    <source src="<?= htmlspecialchars($link['url']) ?>" type="audio/mpeg">
                                    Browser Anda tidak mendukung audio tag.
                                </audio>
                            </div>
                            <div>
                                <button class="btn btn-outline-secondary btn-sm copy-btn" data-url="<?= htmlspecialchars($link['url']) ?>">Copy</button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Footer -->
        <footer class="text-center py-3 mt-auto">
            &copy; BOOMBOXIN | Joe Ramon
        </footer>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Copy button
        const alertBox = document.getElementById('copyAlert');
        document.querySelectorAll('.copy-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                const url = btn.getAttribute('data-url');
                navigator.clipboard.writeText(url).then(() => {
                    alertBox.style.display = 'block';
                    setTimeout(() => alertBox.style.display = 'none', 2000);
                });
            });
        });

        // Success alert
        const successAlert = document.getElementById('successAlert');
        if (successAlert) {
            setTimeout(() => successAlert.style.display = 'none', 2000);
        }

        // Filter/pencarian
        document.getElementById('searchInput').addEventListener('input', function() {
            const keyword = this.value.toLowerCase();
            const items = document.querySelectorAll('.link-item');

            items.forEach(item => {
                const name = item.querySelector('.link-name').textContent.toLowerCase();
                item.style.display = name.includes(keyword) ? 'block' : 'none';
            });
        });
    </script>
</body>

</html>