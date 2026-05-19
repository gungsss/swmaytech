<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title><?= $title ?? 'Aset Simulator' ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            -webkit-tap-highlight-color: transparent;
        }

        body {
            background-color: #f8fafc;
            overflow-x: hidden;
        }

        .breadcrumb {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
            color: #64748b;
            margin-bottom: 20px;
        }

        .breadcrumb a {
            color: #3b82f6;
            text-decoration: none;
        }

        .breadcrumb a:hover {
            text-decoration: underline;
        }

        .breadcrumb .separator {
            color: #cbd5e1;
        }

        .canvas-wrapper {
            position: relative;
            width: 100%;
            overflow: auto;
            background: #f1f5f9;
            border-radius: 12px;
            border: 2px dashed #cbd5e1;
            min-height: 300px;
            display: flex;
            justify-content: center;
            align-items: flex-start;
        }

        .canvas-wrapper.fullscreen {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            z-index: 9990;
            border-radius: 0;
            border: none;
            background: #0f172a;
            overflow: hidden;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .canvas-wrapper.fullscreen .main-canvas {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
        }

        .main-canvas {
            display: block;
            cursor: crosshair;
            max-width: 100%;
            width: 100%;
            height: auto;
            touch-action: none;
        }

        .fs-controls {
            position: absolute;
            top: 12px;
            right: 12px;
            display: flex;
            gap: 8px;
            z-index: 9992;
        }

        .fs-btn {
            background: rgba(255, 255, 255, 0.9);
            border: none;
            width: 40px;
            height: 40px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-size: 16px;
            color: #334155;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
            transition: all 0.2s;
        }

        .fs-btn:hover {
            background: white;
            transform: scale(1.05);
        }

        .canvas-wrapper.fullscreen .fs-btn {
            background: rgba(30, 41, 59, 0.9);
            color: #e2e8f0;
        }

        .step-indicator {
            display: flex;
            gap: 8px;
            margin-bottom: 20px;
            align-items: center;
        }

        .step-dot {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: #e2e8f0;
            color: #64748b;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            font-weight: bold;
            transition: all 0.3s;
            flex-shrink: 0;
        }

        .step-dot.active {
            background: #3b82f6;
            color: white;
        }

        .step-dot.done {
            background: #10b981;
            color: white;
        }

        .step-line {
            flex: 1;
            height: 2px;
            background: #e2e8f0;
            max-width: 60px;
        }

        .step-line.done {
            background: #10b981;
        }

        .upload-area {
            border: 2px dashed #cbd5e1;
            border-radius: 12px;
            padding: 60px 40px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s;
            background: white;
        }

        .upload-area:hover,
        .upload-area.dragover {
            border-color: #3b82f6;
            background: #eff6ff;
        }

        .tool-btn {
            padding: 8px 16px;
            border: 2px solid #e2e8f0;
            background: white;
            color: #475569;
            border-radius: 8px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.2s;
            white-space: nowrap;
        }

        .tool-btn:hover,
        .tool-btn.active {
            border-color: #3b82f6;
            color: #3b82f6;
            background: #eff6ff;
        }

        .notification {
            position: fixed;
            bottom: 20px;
            left: 50%;
            transform: translateX(-50%);
            background: #10b981;
            color: white;
            padding: 12px 24px;
            border-radius: 8px;
            font-weight: 600;
            z-index: 9999;
            opacity: 0;
            transition: opacity 0.3s;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            max-width: 90vw;
            text-align: center;
        }

        .notification.show {
            opacity: 1;
        }

        .notification.error {
            background: #ef4444;
        }

        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 9998;
            padding: 16px;
        }

        .modal-overlay.show {
            display: flex;
        }

        .modal-box {
            background: white;
            border-radius: 16px;
            padding: 32px;
            max-width: 500px;
            width: 100%;
            text-align: center;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
        }

        @media (max-width: 768px) {
            .tool-btn {
                padding: 6px 10px;
                font-size: 12px;
            }

            .upload-area {
                padding: 40px 20px;
            }

            .modal-box {
                padding: 24px 16px;
            }

            .fs-btn {
                width: 36px;
                height: 36px;
            }

            .canvas-wrapper {
                min-height: 250px;
            }

            .main-canvas {
                min-height: 250px;
            }
        }

        @media (max-width: 768px) and (orientation: portrait) {
            .canvas-wrapper.fullscreen {
                padding: 20px;
            }
        }

        .back-btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            color: #64748b;
            font-size: 14px;
            font-weight: 500;
            text-decoration: none;
            transition: color 0.2s;
            margin-bottom: 8px;
        }

        .back-btn:hover {
            color: #334155;
        }
    </style>
    <?= $this->renderSection('styles') ?>
</head>

<body class="text-slate-800">
    <nav class="bg-white shadow-sm border-b border-slate-200 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center gap-4">
                    <a href="/" class="flex items-center gap-2">
                        <i class="fas fa-bolt text-blue-500 text-2xl"></i>
                        <span class="font-bold text-xl text-slate-800 hidden sm:inline">AsetSimulator</span>
                    </a>
                    <?php if (session()->get('logged_in')): ?>
                        <div class="hidden md:flex items-center gap-1 text-sm text-slate-400 ml-4">
                            <span>/</span>
                            <span class="text-slate-600"><?= session()->get('role') === 'admin' ? 'Admin' : 'User' ?></span>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="flex items-center gap-3">
                    <?php if (session()->get('logged_in')): ?>
                        <span class="text-sm text-slate-600 hidden sm:inline">
                            <i class="fas fa-user-circle mr-1"></i>
                            <?= session()->get('nama') ?>
                        </span>
                        <a href="/logout" class="text-red-500 hover:text-red-700 text-sm font-medium px-3 py-2 rounded-lg hover:bg-red-50 transition">
                            <i class="fas fa-sign-out-alt sm:mr-1"></i><span class="hidden sm:inline">Logout</span>
                        </a>
                    <?php else: ?>
                        <a href="/login" class="text-blue-500 hover:text-blue-700 text-sm font-medium px-3 py-2">Login</a>
                        <a href="/register" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition">Register</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <?php if (session()->get('logged_in')): ?>
        <div class="md:hidden fixed bottom-0 left-0 right-0 bg-white border-t border-slate-200 z-50 flex justify-around py-2">
            <?php if (session()->get('role') === 'admin'): ?>
                <a href="/admin/dashboard" class="flex flex-col items-center gap-1 text-xs <?= current_url() == base_url('admin/dashboard') ? 'text-blue-500' : 'text-slate-400' ?>">
                    <i class="fas fa-home text-lg"></i>
                    <span>Dashboard</span>
                </a>
                <a href="/admin/kategori" class="flex flex-col items-center gap-1 text-xs <?= current_url() == base_url('admin/kategori') ? 'text-blue-500' : 'text-slate-400' ?>">
                    <i class="fas fa-folder text-lg"></i>
                    <span>Kategori</span>
                </a>
                <a href="/admin/soal" class="flex flex-col items-center gap-1 text-xs <?= current_url() == base_url('admin/soal') ? 'text-blue-500' : 'text-slate-400' ?>">
                    <i class="fas fa-question-circle text-lg"></i>
                    <span>Soal</span>
                </a>
            <?php else: ?>
                <a href="/user/kategori" class="flex flex-col items-center gap-1 text-xs <?= current_url() == base_url('user/kategori') ? 'text-blue-500' : 'text-slate-400' ?>">
                    <i class="fas fa-folder text-lg"></i>
                    <span>Kategori</span>
                </a>
                <a href="/user/riwayat" class="flex flex-col items-center gap-1 text-xs <?= current_url() == base_url('user/riwayat') ? 'text-blue-500' : 'text-slate-400' ?>">
                    <i class="fas fa-history text-lg"></i>
                    <span>Riwayat</span>
                </a>
            <?php endif; ?>
        </div>
        <div class="md:hidden h-16"></div>
    <?php endif; ?>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8">
        <?php if (session()->getFlashdata('success')): ?>
            <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg mb-6">
                <?= session()->getFlashdata('success') ?>
            </div>
        <?php endif; ?>
        <?php if (session()->getFlashdata('error')): ?>
            <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-6">
                <?= session()->getFlashdata('error') ?>
            </div>
        <?php endif; ?>

        <?= $this->renderSection('content') ?>
    </main>

    <div class="notification" id="notification"></div>

    <div class="modal-overlay" id="modalOverlay">
        <div class="modal-box">
            <h3 class="text-xl font-bold text-slate-800 mb-2" id="modalTitle">Hasil</h3>
            <div class="text-slate-600 mb-6" id="modalText">...</div>
            <button onclick="closeModal()" class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-2 rounded-lg font-medium transition">Tutup</button>
        </div>
    </div>

    <script>
        function showNotification(text, isError = false) {
            const notif = document.getElementById('notification');
            notif.textContent = text;
            notif.className = 'notification show' + (isError ? ' error' : '');
            setTimeout(() => notif.classList.remove('show'), 3000);
        }

        function showModal(title, html, onClose = null) {
            document.getElementById('modalTitle').textContent = title;
            document.getElementById('modalText').innerHTML = html;
            document.getElementById('modalOverlay').classList.add('show');
            
            // Store the onClose callback for use when modal is closed
            window.modalOnCloseCallback = onClose;
        }

        function closeModal() {
            // Execute callback if exists before closing
            if (typeof window.modalOnCloseCallback === 'function') {
                window.modalOnCloseCallback();
            }
            window.modalOnCloseCallback = null;
            document.getElementById('modalOverlay').classList.remove('show');
        }

        function toggleFullscreen(wrapperId) {
            const wrapper = document.getElementById(wrapperId);
            if (!wrapper) return;

            const canvas = wrapper.querySelector('.main-canvas');

            if (wrapper.classList.contains('fullscreen')) {
                wrapper.classList.remove('fullscreen');
                document.body.style.overflow = '';
                // Reset zoom mode
                document.documentElement.style.zoom = '';
                document.body.style.overflowX = '';
                // Reset canvas to original size
                if (canvas && canvas.dataset.originalWidth && canvas.dataset.originalHeight) {
                    canvas.style.width = '';
                    canvas.style.height = '';
                    canvas.style.transform = '';
                }
            } else {
                wrapper.classList.add('fullscreen');
                // Disable browser zoom on mobile
                document.documentElement.style.zoom = '1';
                document.body.style.overflowX = 'hidden';

                // Scale canvas to fill entire screen
                if (canvas) {
                    // Store original dimensions if not already stored
                    if (!canvas.dataset.originalWidth) {
                        canvas.dataset.originalWidth = canvas.width;
                        canvas.dataset.originalHeight = canvas.height;
                    }

                    // Calculate available space using visual viewport if available, fallback to client dimensions
                    let availableWidth, availableHeight;
                    if (window.visualViewport) {
                        availableWidth = window.visualViewport.width;
                        availableHeight = window.visualViewport.height;
                    } else {
                        availableWidth = window.innerWidth;
                        availableHeight = window.innerHeight;
                    }

                    // Get canvas aspect ratio
                    const canvasWidth = parseInt(canvas.dataset.originalWidth);
                    const canvasHeight = parseInt(canvas.dataset.originalHeight);
                    const aspectRatio = canvasWidth / canvasHeight;

                    // Calculate scaled dimensions to fill screen
                    let scaledWidth, scaledHeight;

                    if (availableWidth / availableHeight > aspectRatio) {
                        // Height is the constraint - fill height
                        scaledHeight = availableHeight;
                        scaledWidth = scaledHeight * aspectRatio;
                    } else {
                        // Width is the constraint - fill width
                        scaledWidth = availableWidth;
                        scaledHeight = scaledWidth / aspectRatio;
                    }

                    canvas.style.width = scaledWidth + 'px';
                    canvas.style.height = scaledHeight + 'px';
                }
            }

            // Trigger resize event for canvas redraw
            setTimeout(() => {
                window.dispatchEvent(new Event('resize'));
            }, 100);
        }

        // Handle visual viewport changes (for mobile zoom)
        if (window.visualViewport) {
            window.visualViewport.addEventListener('resize', function() {
                const fullscreenWrapper = document.querySelector('.canvas-wrapper.fullscreen');
                if (fullscreenWrapper && fullscreenWrapper.classList.contains('fullscreen')) {
                    const canvas = fullscreenWrapper.querySelector('.main-canvas');
                    if (canvas && canvas.dataset.originalWidth) {
                        const availableWidth = window.visualViewport.width;
                        const availableHeight = window.visualViewport.height;

                        const canvasWidth = parseInt(canvas.dataset.originalWidth);
                        const canvasHeight = parseInt(canvas.dataset.originalHeight);
                        const aspectRatio = canvasWidth / canvasHeight;

                        let scaledWidth, scaledHeight;

                        if (availableWidth / availableHeight > aspectRatio) {
                            scaledHeight = availableHeight;
                            scaledWidth = scaledHeight * aspectRatio;
                        } else {
                            scaledWidth = availableWidth;
                            scaledHeight = scaledWidth / aspectRatio;
                        }

                        canvas.style.width = scaledWidth + 'px';
                        canvas.style.height = scaledHeight + 'px';
                    }
                }
            });

            window.visualViewport.addEventListener('scroll', function() {
                const fullscreenWrapper = document.querySelector('.canvas-wrapper.fullscreen');
                if (fullscreenWrapper) {
                    const canvas = fullscreenWrapper.querySelector('.main-canvas');
                    if (canvas) {
                        // Offset wrapper to account for visual viewport offset (address bar)
                        const offsetY = window.visualViewport.offsetTop;
                        const offsetX = window.visualViewport.offsetLeft;
                        wrapper.style.transform = `translate(-${offsetX}px, -${offsetY}px)`;
                    }
                }
            });
        }

        // Handle window resize when in fullscreen (for desktop)
        window.addEventListener('resize', function() {
            if (window.visualViewport) return; // Skip if visualViewport is handling it

            const fullscreenWrapper = document.querySelector('.canvas-wrapper.fullscreen');
            if (fullscreenWrapper) {
                const canvas = fullscreenWrapper.querySelector('.main-canvas');
                if (canvas && canvas.dataset.originalWidth) {
                    const availableWidth = window.innerWidth;
                    const availableHeight = window.innerHeight;

                    const canvasWidth = parseInt(canvas.dataset.originalWidth);
                    const canvasHeight = parseInt(canvas.dataset.originalHeight);
                    const aspectRatio = canvasWidth / canvasHeight;

                    let scaledWidth, scaledHeight;

                    if (availableWidth / availableHeight > aspectRatio) {
                        scaledHeight = availableHeight;
                        scaledWidth = scaledHeight * aspectRatio;
                    } else {
                        scaledWidth = availableWidth;
                        scaledHeight = scaledWidth / aspectRatio;
                    }

                    canvas.style.width = scaledWidth + 'px';
                    canvas.style.height = scaledHeight + 'px';
                }
            }
        });
    </script>
    <?= $this->renderSection('scripts') ?>
</body>

</html>