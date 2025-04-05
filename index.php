<?php
$links = file_exists('links.json') ? json_decode(file_get_contents('links.json'), true) : [];
$showSuccessAlert = isset($_GET['success']) && $_GET['success'] == 1;
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Home | Boomboxin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body,
        html {
            height: 100%;
            font-family: 'Inter', sans-serif;
            background-color: #121212;
            color: #fff;
            font-weight: 600;
        }

        .navbar {
            background-color: #1db954 !important;
        }

        .navbar-brand {
            font-weight: 600;
            font-size: 1.5rem;
        }

        .container h2 {
            color: #fff;
            font-weight: 600;
        }

        .btn-primary {
            background-color: #1db954;
            border-color: #1db954;
            font-weight: 600;
        }

        .btn-primary:hover {
            background-color: #17a74a;
            border-color: #17a74a;
        }

        .list-group-item {
            background-color: #181818;
            border: 1px solid #282828;
            border-radius: 10px;
            margin-bottom: 1rem;
            transition: transform 0.2s ease;
            cursor: pointer;
        }

        .list-group-item:hover {
            transform: scale(1.01);
            background-color: #202020;
        }

        .link-name {
            font-size: 1.1rem;
            color: #fff;
            font-weight: 600;
        }

        .copy-btn {
            color: #fff;
            border-color: #fff;
            font-weight: 600;
        }

        .copy-btn:hover {
            background-color: #1db954;
            border-color: #1db954;
            color: #000;
        }

        .form-control {
            background-color: #222;
            border: none;
            color: #fff;
            font-weight: 600;
        }

        .form-control:focus {
            background-color: #2a2a2a;
            color: #fff;
            border: 1px solid #1db954;
        }

        /* Tambahan: agar placeholder terlihat */
        .form-control::placeholder {
            color: #aaa;
            opacity: 1;
            font-weight: 600;
        }

        footer {
            background-color: #000;
            color: #aaa;
            font-weight: 600;
        }

        #playerContainer {
            display: none;
            background-color: #181818;
            padding: 10px 20px;
            border-top: 1px solid #282828;
            align-items: center;
            justify-content: space-between;
            margin-top: 20px;
        }

        #nowPlaying {
            color: #fff;
            font-weight: 600;
        }

        audio {
            width: 300px;
        }

        @media (max-width: 576px) {
            audio {
                width: 100%;
            }
        }

        /* Popup Alert */
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
            background-color: #1db954;
            color: #000;
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
    </style>
</head>

<body>
    <div class="wrapper d-flex flex-column min-vh-100">
        <nav class="navbar navbar-expand-lg sticky-top">
            <div class="container justify-content-center">
                <span class="navbar-brand mx-auto">BOOMBOXIN</span>
            </div>
        </nav>

        <!-- Popup Alert -->
        <div id="popupAlertOverlay" class="popup-alert-overlay">
            <div id="popupAlert" class="popup-alert">Popup Message</div>
        </div>

        <div class="container content pt-5 pb-4 flex-grow-1">
            <h2 class="mb-4">Daftar Music</h2>
            <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
                <input type="text" id="searchInput" class="form-control w-50" placeholder="Cari...">
                <a href="add.php" class="btn btn-primary">+ Tambah Lagu</a>
            </div>

            <div class="list-group" id="linkList">
                <?php $i = 1; ?>
                <?php foreach ($links as $index => $link): ?>
                    <div class="list-group-item link-item p-3 play-song" data-url="<?= htmlspecialchars($link['url']) ?>">
                        <div class="link-row d-flex justify-content-between flex-wrap align-items-center">
                            <div class="flex-grow-1">
                                <strong class="link-name"><?= $i++ ?>. <?= strtolower(htmlspecialchars($link['name'])) ?></strong>
                                <input type="text" class="copy-input" id="copyInput<?= $index ?>" value="<?= htmlspecialchars($link['url']) ?>" readonly hidden>
                            </div>
                            <div class="mt-2">
                                <button class="btn btn-outline-light btn-sm copy-btn" data-input="copyInput<?= $index ?>">Copy</button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Pemutar Lagu - sekarang di atas footer -->
        <div id="playerContainer" class="d-flex px-4 py-3">
            <div id="nowPlaying" class="me-3">🎶 Memutar: -</div>
            <audio id="audioPlayer" controls></audio>
        </div>

        <footer class="text-center py-3">
            &copy; BOOMBOXIN | Joe Ramon
        </footer>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function showPopupAlert(message) {
            const overlay = document.getElementById('popupAlertOverlay');
            const alertBox = document.getElementById('popupAlert');

            alertBox.textContent = message;
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

        document.querySelectorAll('.copy-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.stopPropagation();
                const inputId = btn.getAttribute('data-input');
                const input = document.getElementById(inputId);
                const textToCopy = input.value;

                if (navigator.clipboard) {
                    navigator.clipboard.writeText(textToCopy).then(() => {
                        showPopupAlert("✅ Link berhasil disalin!");
                    });
                } else {
                    input.select();
                    document.execCommand('copy');
                    showPopupAlert("✅ Link berhasil disalin!");
                }
            });
        });

        document.getElementById('searchInput').addEventListener('input', function() {
            const keyword = this.value.toLowerCase();
            const items = document.querySelectorAll('.link-item');

            items.forEach(item => {
                const name = item.querySelector('.link-name').textContent.toLowerCase();
                item.style.display = name.includes(keyword) ? 'block' : 'none';
            });
        });

        document.querySelectorAll('.play-song').forEach(item => {
            item.addEventListener('click', async () => {
                const url = item.getAttribute('data-url');
                const audio = document.getElementById('audioPlayer');
                const container = document.getElementById('playerContainer');
                const nowPlaying = document.getElementById('nowPlaying');

                audio.src = url;
                container.style.display = 'flex';
                nowPlaying.textContent = '🎶 Memutar: ' + item.querySelector('.link-name').textContent.trim();

                try {
                    await audio.play();
                } catch (error) {
                    console.error('Gagal memutar lagu:', error);
                    showPopupAlert("🚫 Gagal memutar lagu.");
                }
            });
        });
    </script>

    <?php if ($showSuccessAlert): ?>
        <script>
            window.onload = () => {
                showPopupAlert("✅ Link berhasil ditambahkan!");
            };
        </script>
    <?php endif; ?>
</body>

</html>