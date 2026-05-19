<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<<nav class="breadcrumb">
    <a href="/<?= session()->get('role') ?>/dashboard"><i class="fas fa-home"></i> Dashboard</a>
    <span class="separator">/</span>
    <a href="/admin/soal">Soal</a>
    <span class="separator">/</span>
    <span class="text-slate-500"><?= isset($soal) ? 'Edit Soal' : 'Tambah Soal' ?></span>
    </nav>

    <div class="mb-6">
        <a href="/admin/soal" class="back-btn">
            <i class="fas fa-arrow-left"></i> Kembali ke Daftar Soal
        </a>
        <h1 class="text-2xl sm:text-3xl font-bold text-slate-800 mt-2"><?= isset($soal) ? 'Edit Soal' : 'Tambah Soal Baru' ?></h1>
        <p class="text-slate-500 mt-1"><?= isset($soal) ? 'Ubah gambar, titik, dan jalur jawaban' : 'Upload gambar rangkaian dan tentukan titik koneksi' ?></p>
    </div>

    <!-- Step 1: Upload Gambar -->
    <div id="step1" class="mb-8">
        <div class="step-indicator">
            <div class="step-dot active" id="dot1">1</div>
            <div class="step-line" id="line1"></div>
            <div class="step-dot" id="dot2">2</div>
            <div class="step-line" id="line2"></div>
            <div class="step-dot" id="dot3">3</div>
        </div>

        <div class="upload-area" id="uploadArea">
            <input type="file" id="gambarInput" accept="image/*" style="display:none">
            <div style="font-size: 4rem; margin-bottom: 15px;">🖼️</div>
            <div style="font-weight: 600; margin-bottom: 8px; font-size: 1.1rem;">Upload Gambar Rangkaian</div>
            <div style="color: #64748b; font-size: 14px; line-height: 1.6;">
                Upload gambar rangkaian elektronika lengkap<br>
                Format: JPG, PNG, GIF
            </div>
            <?php if (isset($soal)): ?>
                <div class="mt-4 text-sm text-blue-600">
                    <i class="fas fa-info-circle mr-1"></i>
                    Gambar saat ini: <strong><?= $soal['gambar'] ?></strong>
                </div>
                <div class="mt-3 text-sm text-slate-500">
                    Upload gambar baru untuk mengganti, atau langsung lanjut ke step berikutnya
                </div>
            <?php endif; ?>
        </div>

        <div class="flex justify-end mt-4 gap-2">
            <a href="/admin/soal" class="text-slate-500 hover:text-slate-700 text-sm font-medium px-4 py-2">
                Batal
            </a>
            <?php if (isset($soal)): ?>
                <button onclick="goToStep(2)" class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-2 rounded-lg text-sm font-medium transition">
                    Lewati & Lanjut →
                </button>
            <?php endif; ?>
        </div>
    </div>

    <!-- Step 2: Tentukan Titik -->
    <div id="step2" class="hidden mb-8">
        <div class="step-indicator">
            <div class="step-dot done" id="dot1s2">✓</div>
            <div class="step-line done" id="line1s2"></div>
            <div class="step-dot active" id="dot2s2">2</div>
            <div class="step-line" id="line2s2"></div>
            <div class="step-dot" id="dot3s2">3</div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-4 mb-4">
            <div class="flex flex-wrap gap-2 items-center">
                <button class="tool-btn active" onclick="setTool('addPoint')" id="btnAddPoint">
                    <i class="fas fa-plus-circle mr-1"></i>Tambah Titik
                </button>
                <button class="tool-btn" onclick="setTool('deletePoint')" id="btnDeletePoint">
                    <i class="fas fa-trash mr-1"></i>Hapus Titik
                </button>
                <button class="tool-btn" onclick="setTool('editLabel')" id="btnEditLabel">
                    <i class="fas fa-tag mr-1"></i>Edit Label
                </button>
                <div class="ml-auto flex gap-2">
                    <button onclick="goToStep(1)" class="text-slate-500 hover:text-slate-700 text-sm font-medium px-3 py-2">
                        ← Kembali
                    </button>
                    <button onclick="goToStep(3)" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition">
                        Lanjut →
                    </button>
                </div>
            </div>
            <div class="flex flex-wrap items-center gap-4 mt-3 pt-3 border-t border-slate-200">
                <div class="flex items-center gap-2">
                    <label class="text-sm text-slate-600">Ukuran Titik:</label>
                    <input type="range" id="pointSizeSlider" min="1" max="48" value="24"
                        class="w-24 h-2 bg-slate-200 rounded-lg appearance-none cursor-pointer"
                        onchange="updatePointSize(this.value)" oninput="updatePointSize(this.value)">
                    <span id="pointSizeValue" class="text-sm font-medium text-slate-700 w-12">24px</span>
                </div>
                <div class="text-xs text-slate-400">
                    <i class="fas fa-info-circle mr-1"></i>
                    Ukuran ini berlaku untuk semua titik dalam soal ini
                </div>
            </div>
            <p class="text-sm text-slate-500 mt-2" id="toolInfo">Klik di gambar untuk menambah titik koneksi</p>
        </div>

        <div class="canvas-wrapper" id="canvasWrapper2">
            <div class="fs-controls">
                <button class="fs-btn" onclick="toggleFullscreen('canvasWrapper2')" title="Fullscreen">
                    <i class="fas fa-expand"></i>
                </button>
            </div>
            <canvas id="canvasStep2" class="main-canvas"></canvas>
        </div>

        <div id="labelEditor" class="hidden mt-4 bg-white rounded-xl shadow-sm border border-slate-200 p-4">
            <label class="block text-sm font-medium text-slate-700 mb-2">Label Titik (misal: R, Y, W, G, +, -):</label>
            <div class="flex gap-2">
                <input type="text" id="pointLabelInput" class="flex-1 px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none"
                    placeholder="Masukkan label..." maxlength="10">
                <button onclick="savePointLabel()" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg font-medium">Simpan</button>
                <button onclick="cancelEditLabel()" class="bg-slate-200 hover:bg-slate-300 text-slate-700 px-4 py-2 rounded-lg font-medium">Batal</button>
            </div>
        </div>
    </div>

    <!-- Step 3: Gambar Jalur + Info Soal -->
    <div id="step3" class="hidden mb-8">
        <div class="step-indicator">
            <div class="step-dot done" id="dot1s3">✓</div>
            <div class="step-line done" id="line1s3"></div>
            <div class="step-dot done" id="dot2s3">✓</div>
            <div class="step-line done" id="line2s3"></div>
            <div class="step-dot active" id="dot3s3">3</div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-4 mb-4">
            <div class="flex flex-wrap gap-2 items-center">
                <button class="tool-btn active" onclick="setTool('drawPath')" id="btnDrawPath">
                    <i class="fas fa-pen mr-1"></i>Gambar Jalur
                </button>
                <button class="tool-btn" onclick="setTool('deletePath')" id="btnDeletePath">
                    <i class="fas fa-eraser mr-1"></i>Hapus Jalur
                </button>
                <div class="ml-auto flex gap-2">
                    <button onclick="goToStep(2)" class="text-slate-500 hover:text-slate-700 text-sm font-medium px-3 py-2">
                        ← Kembali
                    </button>
                </div>
            </div>
            <p class="text-sm text-slate-500 mt-2" id="pathInfo">Klik titik A lalu klik titik B untuk membuat jalur jawaban</p>
        </div>

        <div class="canvas-wrapper" id="canvasWrapper3">
            <div class="fs-controls">
                <button class="fs-btn" onclick="toggleFullscreen('canvasWrapper3')" title="Fullscreen">
                    <i class="fas fa-expand"></i>
                </button>
            </div>
            <canvas id="canvasStep3" class="main-canvas"></canvas>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-4 mt-4">
            <label class="block text-sm font-medium text-slate-700 mb-3">Gaya Garis:</label>
            <div class="flex gap-3 mb-4">
                <button onclick="setPathStyle('straight')" class="path-style-btn flex-1 py-2 px-3 border-2 rounded-lg text-sm font-medium transition <?= !isset($soal) ? 'active' : '' ?>" id="btnStyleStraight">
                    <i class="fas fa-minus mr-1"></i> Lurus
                </button>
                <button onclick="setPathStyle('elbow')" class="path-style-btn flex-1 py-2 px-3 border-2 rounded-lg text-sm font-medium transition" id="btnStyleElbow">
                    <i class="fas fa-angle-right mr-1"></i> Siku (Elbow)
                </button>
                <button onclick="setPathStyle('bezier')" class="path-style-btn flex-1 py-2 px-3 border-2 rounded-lg text-sm font-medium transition <?= isset($soal) ? 'active' : '' ?>" id="btnStyleBezier">
                    <i class="fas fa-bezier-curve mr-1"></i> Bezier
                </button>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Kategori <span class="text-red-500">*</span></label>
                    <select id="idKategori" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
                        <option value="">Pilih Kategori...</option>
                        <?php foreach ($kategori as $k): ?>
                            <option value="<?= $k['id'] ?>" <?= (isset($soal) && $soal['id_kategori'] == $k['id']) ? 'selected' : '' ?>>
                                <?= $k['nama_kategori'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Nama Soal <span class="text-red-500">*</span></label>
                    <input type="text" id="namaSoal" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none"
                        placeholder="Contoh: Rangkaian Pengisian Baterai" value="<?= isset($soal) ? esc($soal['nama_soal']) : '' ?>">
                </div>
            </div>
            <div class="mt-4">
                <label class="block text-sm font-medium text-slate-700 mb-2">Deskripsi:</label>
                <input type="text" id="deskripsiSoal" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none"
                    placeholder="Deskripsi singkat soal..." value="<?= isset($soal) ? esc($soal['deskripsi'] ?? '') : '' ?>">
            </div>
            <?php if (isset($soal)): ?>
                <div class="mt-4">
                    <label class="block text-sm font-medium text-slate-700 mb-2">Status:</label>
                    <select id="statusSoal" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
                        <option value="aktif" <?= $soal['status'] === 'aktif' ? 'selected' : '' ?>>Aktif</option>
                        <option value="nonaktif" <?= $soal['status'] === 'nonaktif' ? 'selected' : '' ?>>Nonaktif</option>
                    </select>
                </div>
            <?php endif; ?>
            <div class="flex gap-2 mt-4">
                <a href="/admin/soal" class="flex-1 text-center bg-slate-200 hover:bg-slate-300 text-slate-700 font-semibold py-3 rounded-lg transition">
                    Batal
                </a>
                <button onclick="saveSoal()" class="flex-[2] bg-green-500 hover:bg-green-600 text-white font-semibold py-3 rounded-lg transition">
                    <i class="fas fa-save mr-2"></i><?= isset($soal) ? 'Update Soal' : 'Simpan Soal' ?>
                </button>
            </div>
        </div>
    </div>

    <?= $this->endSection() ?>

    <?= $this->section('styles') ?>
    <style>
        .path-style-btn {
            border-color: #e2e8f0;
            color: #475569;
            background: white;
        }

        .path-style-btn.active {
            border-color: #3b82f6;
            color: #3b82f6;
            background: #eff6ff;
        }

        .path-style-btn:hover {
            border-color: #94a3b8;
        }

        .canvas-wrapper {
            width: 100%;
            min-height: 400px;
        }

        .main-canvas {
            display: block;
            cursor: crosshair;
            touch-action: none;
            max-width: none;
            width: 100%;
            height: auto;
        }

        @media (max-width: 768px) {
            .canvas-wrapper {
                min-height: 300px;
            }
        }
    </style>
    <?= $this->endSection() ?>

    <?= $this->section('scripts') ?>
    <script>
        const editMode = <?= isset($soal) ? 'true' : 'false' ?>;
        const existingSoal = <?= isset($soal) ? json_encode($soal, JSON_NUMERIC_CHECK) : 'null' ?>;
        const isMobile = window.innerWidth <= 768;

        let currentStep = 1;
        let soalImage = null;
        let soalImageObj = null;
        let imgWidth = 0,
            imgHeight = 0;
        let points = [];
        let paths = [];
        let currentTool = 'addPoint';
        let tempPath = null;
        let editingPointId = null;
        let canvas2, ctx2, canvas3, ctx3;
        let tempIdCounter = 1;
        let pathStyle = editMode ? 'bezier' : 'straight';
        let selectedPath = null;
        let draggingControl = null;
        let isDragging = false;

        // ==================== PER-SOAL POINT SIZE ====================
        // One size for all points in this soal
        let soalPointSize = 24;

        // ==================== DYNAMIC SCALING SYSTEM ====================
        const REFERENCE_SIZE = 1200;

        const CanvasScaler = {
            currentScale: 1,
            referenceSize: REFERENCE_SIZE,

            update: function(canvasWidth, canvasHeight) {
                const minDimension = Math.min(canvasWidth, canvasHeight);
                this.currentScale = minDimension / this.referenceSize;
                this.currentScale = Math.max(0.3, Math.min(2.0, this.currentScale));
            },

            get: function(referenceValue) {
                return referenceValue * this.currentScale;
            },

            getPointSizes: function() {
                return {
                    outer: this.get(24),
                    middle: this.get(16),
                    inner: this.get(6),
                    hitRadius: this.get(32),
                    labelFontSize: this.get(14),
                    labelPadding: this.get(6),
                    labelHeight: this.get(22),
                    labelOffsetY: this.get(42),
                    labelTextOffsetY: this.get(31),
                    strokeWidth: this.get(3)
                };
            },

            getClearanceRadius: function() {
                return this.get(45);
            },

            getPathSizes: function() {
                return {
                    glowWidth: this.get(12),
                    glowWidthSelected: this.get(16),
                    mainWidth: this.get(4),
                    mainWidthSelected: this.get(5),
                    selectedDashWidth: this.get(8),
                    endDotSize: this.get(8),
                    endDotInner: this.get(4)
                };
            },

            getControlPointSizes: function() {
                return {
                    outer: this.get(14),
                    inner: this.get(9),
                    hitRadius: this.get(20),
                    fontSize: this.get(11),
                    labelOffsetY: this.get(16)
                };
            },

            getTempPathSizes: function() {
                return {
                    lineWidth: this.get(3),
                    dotRadius: this.get(5),
                    dashLength: this.get(10),
                    dashGap: this.get(5)
                };
            },

            getPathHitRadius: function() {
                return this.get(12);
            }
        };

        let POINT_SIZES = CanvasScaler.getPointSizes();
        let CLEARANCE_RADIUS = CanvasScaler.getClearanceRadius();

        const uploadArea = document.getElementById('uploadArea');
        const gambarInput = document.getElementById('gambarInput');

        uploadArea.addEventListener('click', () => gambarInput.click());
        uploadArea.addEventListener('dragover', e => {
            e.preventDefault();
            uploadArea.classList.add('dragover');
        });
        uploadArea.addEventListener('dragleave', () => uploadArea.classList.remove('dragover'));
        uploadArea.addEventListener('drop', e => {
            e.preventDefault();
            uploadArea.classList.remove('dragover');
            if (e.dataTransfer.files[0]) handleImage(e.dataTransfer.files[0]);
        });
        gambarInput.addEventListener('change', e => {
            if (e.target.files[0]) handleImage(e.target.files[0]);
        });

        function handleImage(file) {
            if (!file.type.startsWith('image/')) {
                showNotification('File harus berupa gambar!', true);
                return;
            }
            const reader = new FileReader();
            reader.onload = function(e) {
                soalImage = e.target.result;
                soalImageObj = new Image();
                soalImageObj.onload = function() {
                    imgWidth = soalImageObj.naturalWidth;
                    imgHeight = soalImageObj.naturalHeight;
                    goToStep(2);
                    showNotification('Gambar berhasil diupload!');
                };
                soalImageObj.src = soalImage;
            };
            reader.readAsDataURL(file);
        }

        function goToStep(step) {
            currentStep = step;
            document.getElementById('step1').classList.add('hidden');
            document.getElementById('step2').classList.add('hidden');
            document.getElementById('step3').classList.add('hidden');
            document.getElementById('step' + step).classList.remove('hidden');
            if (step === 2) setTimeout(initStep2, 50);
            if (step === 3) setTimeout(initStep3, 50);
        }

        if (editMode && existingSoal) {
            soalImageObj = new Image();
            soalImageObj.onload = function() {
                imgWidth = soalImageObj.naturalWidth;
                imgHeight = soalImageObj.naturalHeight;

                // Load global point size from first titik (if exists)
                if (existingSoal.titik && existingSoal.titik.length > 0) {
                    const firstSize = parseInt(existingSoal.titik[0].ukuran);
                    if (firstSize && firstSize >= 16 && firstSize <= 48) {
                        soalPointSize = firstSize;
                    }
                }

                if (existingSoal.titik) {
                    existingSoal.titik.forEach((t, idx) => {
                        points.push({
                            tempId: 'p' + (idx + 1),
                            x: parseFloat(t.x),
                            y: parseFloat(t.y),
                            label: t.label || ''
                        });
                    });
                    tempIdCounter = points.length + 1;
                }
                if (existingSoal.jalur_jawaban) {
                    existingSoal.jalur_jawaban.forEach(j => {
                        const fromPoint = points.find(p => Math.abs(parseFloat(p.x) - parseFloat(j.titik_a_x || 0)) < 0.5 && Math.abs(parseFloat(p.y) - parseFloat(j.titik_a_y || 0)) < 0.5);
                        const toPoint = points.find(p => Math.abs(parseFloat(p.x) - parseFloat(j.titik_b_x || 0)) < 0.5 && Math.abs(parseFloat(p.y) - parseFloat(j.titik_b_y || 0)) < 0.5);
                        if (fromPoint && toPoint) {
                            let cp = null;
                            if (j.control_points) {
                                try {
                                    cp = typeof j.control_points === 'string' ? JSON.parse(j.control_points) : j.control_points;
                                } catch (e) {
                                    cp = null;
                                }
                            }
                            if (!cp && j.style && j.style !== 'straight') {
                                cp = generateAutoRoutingControlPoints(fromPoint, toPoint, j.style).controlPoints;
                            }
                            paths.push({
                                fromTempId: fromPoint.tempId,
                                toTempId: toPoint.tempId,
                                style: j.style || 'straight',
                                controlPoints: cp
                            });
                        }
                    });
                }
                if (paths.length > 0 && paths[0].style) {
                    pathStyle = paths[0].style;
                    updatePathStyleUI();
                }
            };
            soalImageObj.src = '/uploads/soal/' + existingSoal.gambar;
            soalImage = soalImageObj.src;
        }

        function updatePathStyleUI() {
            document.querySelectorAll('.path-style-btn').forEach(b => b.classList.remove('active'));
            const btnId = 'btnStyle' + pathStyle.charAt(0).toUpperCase() + pathStyle.slice(1);
            const btn = document.getElementById(btnId);
            if (btn) btn.classList.add('active');
        }

        function setPathStyle(style) {
            pathStyle = style;
            updatePathStyleUI();
            if (ctx3) redrawStep3();
            showNotification('Gaya garis untuk jalur berikutnya: ' + style);
        }

        // ==================== POINT SIZE CONTROL (PER-SOAL) ====================
        function updatePointSize(size) {
            soalPointSize = parseInt(size);
            document.getElementById('pointSizeValue').textContent = size + 'px';

            // Redraw both canvases to show size change immediately
            if (ctx2) redrawStep2();
            if (ctx3) redrawStep3();
        }

        function initStep2() {
            const canvas = document.getElementById('canvasStep2');
            canvas.width = imgWidth;
            canvas.height = imgHeight;
            canvas2 = canvas;
            ctx2 = canvas.getContext('2d');

            CanvasScaler.update(imgWidth, imgHeight);
            POINT_SIZES = CanvasScaler.getPointSizes();
            CLEARANCE_RADIUS = CanvasScaler.getClearanceRadius();

            // Initialize slider to current soalPointSize
            document.getElementById('pointSizeSlider').value = soalPointSize;
            document.getElementById('pointSizeValue').textContent = soalPointSize + 'px';

            canvas.addEventListener('mousedown', onStep2MouseDown);
            canvas.addEventListener('touchstart', onStep2TouchStart, {
                passive: false
            });
            redrawStep2();
        }

        function onStep2TouchStart(e) {
            e.preventDefault();
            const touch = e.touches[0];
            const mouseEvent = new MouseEvent('mousedown', {
                clientX: touch.clientX,
                clientY: touch.clientY
            });
            onStep2MouseDown(mouseEvent);
        }

        function onStep2MouseDown(e) {
            const pos = getCanvasPos(e, canvas2);
            if (currentTool === 'addPoint') {
                const point = {
                    tempId: 'p' + (tempIdCounter++),
                    x: pos.x,
                    y: pos.y,
                    label: ''
                };
                points.push(point);
                editingPointId = point.tempId;
                redrawStep2();
                showNotification('Titik ditambahkan! Gunakan tool "Edit Label" untuk menambahkan label.');
            } else if (currentTool === 'deletePoint') {
                const point = getPointAt(pos);
                if (point) {
                    points = points.filter(p => p.tempId !== point.tempId);
                    paths = paths.filter(p => p.fromTempId !== point.tempId && p.toTempId !== point.tempId);
                    redrawStep2();
                    showNotification('Titik dihapus!');
                }
            } else if (currentTool === 'editLabel') {
                const point = getPointAt(pos);
                if (point) {
                    editingPointId = point.tempId;
                    document.getElementById('pointLabelInput').value = point.label || '';
                    document.getElementById('labelEditor').classList.remove('hidden');
                    document.getElementById('pointLabelInput').focus();
                }
            }
        }

        function savePointLabel() {
            if (!editingPointId) return;
            const point = points.find(p => p.tempId === editingPointId);
            if (point) point.label = document.getElementById('pointLabelInput').value.trim();
            document.getElementById('labelEditor').classList.add('hidden');
            editingPointId = null;
            redrawStep2();
            showNotification('Label disimpan!');
        }

        function cancelEditLabel() {
            document.getElementById('labelEditor').classList.add('hidden');
            editingPointId = null;
        }

        function redrawStep2() {
            if (!ctx2 || !soalImageObj) return;
            ctx2.clearRect(0, 0, canvas2.width, canvas2.height);
            ctx2.drawImage(soalImageObj, 0, 0);
            drawPoints(ctx2);
        }

        function initStep3() {
            const canvas = document.getElementById('canvasStep3');
            canvas.width = imgWidth;
            canvas.height = imgHeight;
            canvas3 = canvas;
            ctx3 = canvas.getContext('2d');

            CanvasScaler.update(imgWidth, imgHeight);
            POINT_SIZES = CanvasScaler.getPointSizes();
            CLEARANCE_RADIUS = CanvasScaler.getClearanceRadius();

            canvas.addEventListener('mousedown', onStep3MouseDown);
            canvas.addEventListener('mousemove', onStep3MouseMove);
            canvas.addEventListener('mouseup', onStep3MouseUp);
            canvas.addEventListener('touchstart', onStep3TouchStart, {
                passive: false
            });
            canvas.addEventListener('touchmove', onStep3TouchMove, {
                passive: false
            });
            canvas.addEventListener('touchend', onStep3TouchEnd, {
                passive: false
            });
            redrawStep3();
        }

        function onStep3TouchStart(e) {
            e.preventDefault();
            const touch = e.touches[0];
            const rect = canvas3.getBoundingClientRect();
            const scaleX = canvas3.width / rect.width;
            const scaleY = canvas3.height / rect.height;
            handleStep3Down({
                x: (touch.clientX - rect.left) * scaleX,
                y: (touch.clientY - rect.top) * scaleY
            });
        }

        function onStep3TouchMove(e) {
            e.preventDefault();
            if (!isDragging && !tempPath) return;
            const touch = e.touches[0];
            const rect = canvas3.getBoundingClientRect();
            const scaleX = canvas3.width / rect.width;
            const scaleY = canvas3.height / rect.height;
            handleStep3Move({
                x: (touch.clientX - rect.left) * scaleX,
                y: (touch.clientY - rect.top) * scaleY
            });
        }

        function onStep3TouchEnd(e) {
            e.preventDefault();
            handleStep3Up();
        }

        function onStep3MouseDown(e) {
            const pos = getCanvasPos(e, canvas3);
            handleStep3Down(pos);
        }

        function onStep3MouseMove(e) {
            const pos = getCanvasPos(e, canvas3);
            handleStep3Move(pos);
        }

        function onStep3MouseUp(e) {
            handleStep3Up();
        }

        function handleStep3Down(pos) {
            if (selectedPath && selectedPath.controlPoints) {
                const cpSizes = CanvasScaler.getControlPointSizes();
                for (let i = 0; i < selectedPath.controlPoints.length; i++) {
                    const cp = selectedPath.controlPoints[i];
                    const dist = Math.sqrt((pos.x - cp.x) ** 2 + (pos.y - cp.y) ** 2);
                    if (dist < cpSizes.hitRadius) {
                        draggingControl = i;
                        isDragging = true;
                        canvas3.style.cursor = 'grabbing';
                        return;
                    }
                }
            }

            if (currentTool === 'drawPath') {
                const point = getPointAt(pos);
                if (point) {
                    if (!tempPath) {
                        tempPath = {
                            startTempId: point.tempId,
                            end: pos
                        };
                    } else {
                        if (tempPath.startTempId !== point.tempId) {
                            const exists = paths.some(p =>
                                (p.fromTempId === tempPath.startTempId && p.toTempId === point.tempId) ||
                                (p.fromTempId === point.tempId && p.toTempId === tempPath.startTempId)
                            );
                            if (!exists) {
                                const fromPoint = points.find(p => p.tempId === tempPath.startTempId);
                                const toPoint = point;
                                const route = generateAutoRoutingControlPoints(fromPoint, toPoint, pathStyle);

                                const newPath = {
                                    fromTempId: tempPath.startTempId,
                                    toTempId: point.tempId,
                                    style: route.style,
                                    controlPoints: route.controlPoints
                                };
                                paths.push(newPath);
                                selectedPath = newPath;
                                showNotification('Jalur ditambahkan!' + (route.style !== pathStyle ? ' (auto-adjusted)' : ''));
                            }
                        }
                        tempPath = null;
                        redrawStep3();
                    }
                } else {
                    const clickedPath = getPathAt(pos);
                    if (clickedPath) {
                        selectedPath = clickedPath;
                        redrawStep3();
                        showNotification('Jalur dipilih. Drag titik kontrol untuk atur bentuk.');
                    } else {
                        selectedPath = null;
                        redrawStep3();
                    }
                }
            } else if (currentTool === 'deletePath') {
                const clickedPath = getPathAt(pos);
                if (clickedPath) {
                    paths = paths.filter(p => p !== clickedPath);
                    if (selectedPath === clickedPath) selectedPath = null;
                    redrawStep3();
                    showNotification('Jalur dihapus!');
                }
            }
        }

        function handleStep3Move(pos) {
            if (isDragging && draggingControl !== null && selectedPath) {
                selectedPath.controlPoints[draggingControl] = {
                    x: pos.x,
                    y: pos.y
                };
                redrawStep3();
                return;
            }
            if (!tempPath) return;
            tempPath.end = pos;
            redrawStep3();
        }

        function handleStep3Up() {
            draggingControl = null;
            isDragging = false;
            if (canvas3) canvas3.style.cursor = 'crosshair';
        }

        function generateDefaultControlPoints(fromPoint, toPoint, style) {
            const fx = parseFloat(fromPoint.x),
                fy = parseFloat(fromPoint.y);
            const tx = parseFloat(toPoint.x),
                ty = parseFloat(toPoint.y);
            const dx = tx - fx,
                dy = ty - fy;
            const dist = Math.sqrt(dx * dx + dy * dy);

            if (style === 'straight') return null;
            else if (style === 'elbow') {
                if (Math.abs(dx) > Math.abs(dy)) {
                    return [{
                        x: fx + dx / 2,
                        y: fy
                    }, {
                        x: fx + dx / 2,
                        y: ty
                    }];
                } else {
                    return [{
                        x: fx,
                        y: fy + dy / 2
                    }, {
                        x: tx,
                        y: fy + dy / 2
                    }];
                }
            } else {
                const offset = Math.min(dist * 0.5, 120);
                return [{
                    x: fx + (dx > 0 ? offset : -offset),
                    y: fy
                }, {
                    x: tx - (dx > 0 ? offset : -offset),
                    y: ty
                }];
            }
        }

        // ==================== PATH AVOIDANCE FUNCTIONS ====================

        function getCollidingPointsOnLine(x1, y1, x2, y2, excludeIds) {
            const colliding = [];
            for (let point of points) {
                const pid = String(point.tempId);
                if (excludeIds && excludeIds.includes(pid)) continue;

                const px = parseFloat(point.x);
                const py = parseFloat(point.y);

                const dx = x2 - x1;
                const dy = y2 - y1;
                const lenSq = dx * dx + dy * dy;

                let t;
                if (lenSq === 0) {
                    t = 0;
                } else {
                    t = Math.max(0, Math.min(1, ((px - x1) * dx + (py - y1) * dy) / lenSq));
                }

                const projX = x1 + t * dx;
                const projY = y1 + t * dy;
                const dist = Math.sqrt((px - projX) ** 2 + (py - projY) ** 2);

                if (dist < CLEARANCE_RADIUS) {
                    colliding.push({
                        point,
                        dist,
                        projX,
                        projY,
                        t
                    });
                }
            }
            colliding.sort((a, b) => a.dist - b.dist);
            return colliding;
        }

        function elbowPathCollides(x1, y1, cp1, cp2, x2, y2, excludeIds) {
            const segments = [
                [x1, y1, cp1.x, cp1.y],
                [cp1.x, cp1.y, cp2.x, cp2.y],
                [cp2.x, cp2.y, x2, y2]
            ];
            for (let seg of segments) {
                const hits = getCollidingPointsOnLine(seg[0], seg[1], seg[2], seg[3], excludeIds);
                if (hits.length > 0) return true;
            }
            return false;
        }

        function bezierPathCollides(x1, y1, cp1, cp2, x2, y2, excludeIds) {
            const steps = 80;
            for (let i = 0; i <= steps; i++) {
                const t = i / steps;
                const bx = (1 - t) * (1 - t) * (1 - t) * x1 + 3 * (1 - t) * (1 - t) * t * cp1.x + 3 * (1 - t) * t * t * cp2.x + t * t * t * x2;
                const by = (1 - t) * (1 - t) * (1 - t) * y1 + 3 * (1 - t) * (1 - t) * t * cp1.y + 3 * (1 - t) * t * t * cp2.y + t * t * t * y2;

                for (let point of points) {
                    const pid = String(point.tempId);
                    if (excludeIds && excludeIds.includes(pid)) continue;
                    const px = parseFloat(point.x);
                    const py = parseFloat(point.y);
                    const dist = Math.sqrt((px - bx) ** 2 + (py - by) ** 2);
                    if (dist < CLEARANCE_RADIUS) return true;
                }
            }
            return false;
        }

        function generateAutoRoutingControlPoints(fromPoint, toPoint, preferredStyle) {
            const fx = parseFloat(fromPoint.x);
            const fy = parseFloat(fromPoint.y);
            const tx = parseFloat(toPoint.x);
            const ty = parseFloat(toPoint.y);
            const dx = tx - fx;
            const dy = ty - fy;
            const dist = Math.sqrt(dx * dx + dy * dy);
            const excludeIds = [String(fromPoint.tempId), String(toPoint.tempId)];

            const straightHits = getCollidingPointsOnLine(fx, fy, tx, ty, excludeIds);
            if (straightHits.length === 0) {
                if (preferredStyle === 'straight') {
                    return {
                        style: 'straight',
                        controlPoints: null
                    };
                }
            }

            if (preferredStyle === 'straight' || preferredStyle === 'elbow') {
                let elbowCP = generateStandardElbow(fx, fy, tx, ty);
                if (!elbowPathCollides(fx, fy, elbowCP[0], elbowCP[1], tx, ty, excludeIds)) {
                    return {
                        style: 'elbow',
                        controlPoints: elbowCP
                    };
                }

                const midX = (fx + tx) / 2;
                const midY = (fy + ty) / 2;
                const isHorizontalPrimary = Math.abs(dx) > Math.abs(dy);

                const attempts = [{
                        offsetX: 0,
                        offsetY: -CLEARANCE_RADIUS * 2
                    },
                    {
                        offsetX: 0,
                        offsetY: CLEARANCE_RADIUS * 2
                    },
                    {
                        offsetX: -CLEARANCE_RADIUS * 2,
                        offsetY: 0
                    },
                    {
                        offsetX: CLEARANCE_RADIUS * 2,
                        offsetY: 0
                    },
                    {
                        offsetX: -CLEARANCE_RADIUS * 3,
                        offsetY: -CLEARANCE_RADIUS * 3
                    },
                    {
                        offsetX: CLEARANCE_RADIUS * 3,
                        offsetY: CLEARANCE_RADIUS * 3
                    },
                    {
                        offsetX: -CLEARANCE_RADIUS * 3,
                        offsetY: CLEARANCE_RADIUS * 3
                    },
                    {
                        offsetX: CLEARANCE_RADIUS * 3,
                        offsetY: -CLEARANCE_RADIUS * 3
                    },
                    {
                        offsetX: -CLEARANCE_RADIUS * 4,
                        offsetY: 0
                    },
                    {
                        offsetX: CLEARANCE_RADIUS * 4,
                        offsetY: 0
                    },
                    {
                        offsetX: 0,
                        offsetY: -CLEARANCE_RADIUS * 4
                    },
                    {
                        offsetX: 0,
                        offsetY: CLEARANCE_RADIUS * 4
                    },
                ];

                for (let attempt of attempts) {
                    let cp1, cp2;
                    if (isHorizontalPrimary) {
                        cp1 = {
                            x: midX + attempt.offsetX,
                            y: fy
                        };
                        cp2 = {
                            x: midX + attempt.offsetX,
                            y: ty + attempt.offsetY
                        };
                    } else {
                        cp1 = {
                            x: fx + attempt.offsetX,
                            y: midY + attempt.offsetY
                        };
                        cp2 = {
                            x: tx,
                            y: midY + attempt.offsetY
                        };
                    }

                    if (!elbowPathCollides(fx, fy, cp1, cp2, tx, ty, excludeIds)) {
                        return {
                            style: 'elbow',
                            controlPoints: [cp1, cp2]
                        };
                    }
                }

                const doubleBendAttempts = [
                    [{
                        x: fx,
                        y: fy - CLEARANCE_RADIUS * 2.5
                    }, {
                        x: tx,
                        y: fy - CLEARANCE_RADIUS * 2.5
                    }],
                    [{
                        x: fx,
                        y: fy + CLEARANCE_RADIUS * 2.5
                    }, {
                        x: tx,
                        y: fy + CLEARANCE_RADIUS * 2.5
                    }],
                    [{
                        x: fx - CLEARANCE_RADIUS * 2.5,
                        y: fy
                    }, {
                        x: fx - CLEARANCE_RADIUS * 2.5,
                        y: ty
                    }],
                    [{
                        x: fx + CLEARANCE_RADIUS * 2.5,
                        y: fy
                    }, {
                        x: fx + CLEARANCE_RADIUS * 2.5,
                        y: ty
                    }],
                    [{
                        x: fx,
                        y: fy - CLEARANCE_RADIUS * 4
                    }, {
                        x: tx,
                        y: fy - CLEARANCE_RADIUS * 4
                    }],
                    [{
                        x: fx,
                        y: fy + CLEARANCE_RADIUS * 4
                    }, {
                        x: tx,
                        y: fy + CLEARANCE_RADIUS * 4
                    }],
                ];

                for (let bend of doubleBendAttempts) {
                    if (!elbowPathCollides(fx, fy, bend[0], bend[1], tx, ty, excludeIds)) {
                        return {
                            style: 'elbow',
                            controlPoints: bend
                        };
                    }
                }
            }

            const offset = Math.min(dist * 0.5, 150);
            const bigOffset = Math.min(dist * 0.7, 200);

            let cp1 = {
                x: fx + (dx > 0 ? offset : -offset),
                y: fy
            };
            let cp2 = {
                x: tx - (dx > 0 ? offset : -offset),
                y: ty
            };

            if (!bezierPathCollides(fx, fy, cp1, cp2, tx, ty, excludeIds)) {
                return {
                    style: 'bezier',
                    controlPoints: [cp1, cp2]
                };
            }

            const bezierAttempts = [
                [{
                    x: fx + dx * 0.2,
                    y: fy - CLEARANCE_RADIUS * 3
                }, {
                    x: tx - dx * 0.2,
                    y: ty - CLEARANCE_RADIUS * 3
                }],
                [{
                    x: fx + dx * 0.2,
                    y: fy - CLEARANCE_RADIUS * 4
                }, {
                    x: tx - dx * 0.2,
                    y: ty - CLEARANCE_RADIUS * 4
                }],
                [{
                    x: fx + dx * 0.2,
                    y: fy - CLEARANCE_RADIUS * 5
                }, {
                    x: tx - dx * 0.2,
                    y: ty - CLEARANCE_RADIUS * 5
                }],
                [{
                    x: fx + dx * 0.2,
                    y: fy + CLEARANCE_RADIUS * 3
                }, {
                    x: tx - dx * 0.2,
                    y: ty + CLEARANCE_RADIUS * 3
                }],
                [{
                    x: fx + dx * 0.2,
                    y: fy + CLEARANCE_RADIUS * 4
                }, {
                    x: tx - dx * 0.2,
                    y: ty + CLEARANCE_RADIUS * 4
                }],
                [{
                    x: fx + dx * 0.2,
                    y: fy + CLEARANCE_RADIUS * 5
                }, {
                    x: tx - dx * 0.2,
                    y: ty + CLEARANCE_RADIUS * 5
                }],
                [{
                    x: fx - CLEARANCE_RADIUS * 3,
                    y: fy + dy * 0.2
                }, {
                    x: tx - CLEARANCE_RADIUS * 3,
                    y: ty - dy * 0.2
                }],
                [{
                    x: fx - CLEARANCE_RADIUS * 4,
                    y: fy + dy * 0.2
                }, {
                    x: tx - CLEARANCE_RADIUS * 4,
                    y: ty - dy * 0.2
                }],
                [{
                    x: fx + CLEARANCE_RADIUS * 3,
                    y: fy + dy * 0.2
                }, {
                    x: tx + CLEARANCE_RADIUS * 3,
                    y: ty - dy * 0.2
                }],
                [{
                    x: fx + CLEARANCE_RADIUS * 4,
                    y: fy + dy * 0.2
                }, {
                    x: tx + CLEARANCE_RADIUS * 4,
                    y: ty - dy * 0.2
                }],
                [{
                    x: fx + (dx > 0 ? bigOffset : -bigOffset),
                    y: fy
                }, {
                    x: tx - (dx > 0 ? bigOffset : -bigOffset),
                    y: ty
                }],
                [{
                    x: fx + dx * 0.3,
                    y: fy - CLEARANCE_RADIUS * 2
                }, {
                    x: tx - dx * 0.3,
                    y: ty - CLEARANCE_RADIUS * 2
                }],
                [{
                    x: fx + dx * 0.3,
                    y: fy + CLEARANCE_RADIUS * 2
                }, {
                    x: tx - dx * 0.3,
                    y: ty + CLEARANCE_RADIUS * 2
                }],
                [{
                    x: fx + (dx > 0 ? offset * 1.5 : -offset * 1.5),
                    y: fy - CLEARANCE_RADIUS * 3
                }, {
                    x: tx - (dx > 0 ? offset * 1.5 : -offset * 1.5),
                    y: ty + CLEARANCE_RADIUS * 3
                }],
                [{
                    x: fx + (dx > 0 ? offset * 1.5 : -offset * 1.5),
                    y: fy + CLEARANCE_RADIUS * 3
                }, {
                    x: tx - (dx > 0 ? offset * 1.5 : -offset * 1.5),
                    y: ty - CLEARANCE_RADIUS * 3
                }],
                [{
                    x: fx + dx * 0.1,
                    y: fy - CLEARANCE_RADIUS * 6
                }, {
                    x: tx - dx * 0.1,
                    y: ty - CLEARANCE_RADIUS * 6
                }],
                [{
                    x: fx + dx * 0.1,
                    y: fy + CLEARANCE_RADIUS * 6
                }, {
                    x: tx - dx * 0.1,
                    y: ty + CLEARANCE_RADIUS * 6
                }],
                [{
                    x: fx + dx * 0.5,
                    y: fy - CLEARANCE_RADIUS * 3
                }, {
                    x: tx - dx * 0.5,
                    y: ty - CLEARANCE_RADIUS * 3
                }],
                [{
                    x: fx + dx * 0.5,
                    y: fy + CLEARANCE_RADIUS * 3
                }, {
                    x: tx - dx * 0.5,
                    y: ty + CLEARANCE_RADIUS * 3
                }],
                [{
                    x: fx - CLEARANCE_RADIUS * 3,
                    y: fy + dy * 0.5
                }, {
                    x: tx - CLEARANCE_RADIUS * 3,
                    y: ty - dy * 0.5
                }],
                [{
                    x: fx + CLEARANCE_RADIUS * 3,
                    y: fy + dy * 0.5
                }, {
                    x: tx + CLEARANCE_RADIUS * 3,
                    y: ty - dy * 0.5
                }],
            ];

            for (let attempt of bezierAttempts) {
                if (!bezierPathCollides(fx, fy, attempt[0], attempt[1], tx, ty, excludeIds)) {
                    return {
                        style: 'bezier',
                        controlPoints: attempt
                    };
                }
            }

            const detour = findDetourPath(fromPoint, toPoint, straightHits.map(h => h.point));
            if (detour) {
                return detour;
            }

            const farAway = Math.max(dist * 1.5, CLEARANCE_RADIUS * 8);
            const perpX = -dy / dist * farAway;
            const perpY = dx / dist * farAway;
            const midX2 = (fx + tx) / 2;
            const midY2 = (fy + ty) / 2;

            const extremeAttempts = [
                [{
                    x: midX2 + perpX * 0.5,
                    y: midY2 + perpY * 0.5
                }, {
                    x: midX2 + perpX * 0.5,
                    y: midY2 + perpY * 0.5
                }],
                [{
                    x: midX2 - perpX * 0.5,
                    y: midY2 - perpY * 0.5
                }, {
                    x: midX2 - perpX * 0.5,
                    y: midY2 - perpY * 0.5
                }],
                [{
                    x: fx + perpX,
                    y: fy + perpY
                }, {
                    x: tx + perpX,
                    y: ty + perpY
                }],
                [{
                    x: fx - perpX,
                    y: fy - perpY
                }, {
                    x: tx - perpX,
                    y: ty - perpY
                }],
            ];

            for (let attempt of extremeAttempts) {
                if (!bezierPathCollides(fx, fy, attempt[0], attempt[1], tx, ty, excludeIds)) {
                    return {
                        style: 'bezier',
                        controlPoints: attempt
                    };
                }
            }

            return {
                style: 'elbow',
                controlPoints: generateStandardElbow(fx, fy, tx, ty)
            };
        }

        function generateStandardElbow(fx, fy, tx, ty) {
            const dx = tx - fx;
            const dy = ty - fy;
            if (Math.abs(dx) > Math.abs(dy)) {
                return [{
                    x: fx + dx / 2,
                    y: fy
                }, {
                    x: fx + dx / 2,
                    y: ty
                }];
            } else {
                return [{
                    x: fx,
                    y: fy + dy / 2
                }, {
                    x: tx,
                    y: fy + dy / 2
                }];
            }
        }

        function findDetourPath(fromPoint, toPoint, blockingPoints) {
            const fx = parseFloat(fromPoint.x);
            const fy = parseFloat(fromPoint.y);
            const tx = parseFloat(toPoint.x);
            const ty = parseFloat(toPoint.y);
            const excludeIds = [String(fromPoint.tempId), String(toPoint.tempId)];

            let minX = Infinity,
                maxX = -Infinity,
                minY = Infinity,
                maxY = -Infinity;
            for (let bp of blockingPoints) {
                const px = parseFloat(bp.x);
                const py = parseFloat(bp.y);
                minX = Math.min(minX, px);
                maxX = Math.max(maxX, px);
                minY = Math.min(minY, py);
                maxY = Math.max(maxY, py);
            }

            const margin = CLEARANCE_RADIUS * 1.5;
            minX -= margin;
            maxX += margin;
            minY -= margin;
            maxY += margin;

            const detourWaypoints = [{
                    x: (fx + tx) / 2,
                    y: minY - CLEARANCE_RADIUS
                },
                {
                    x: (fx + tx) / 2,
                    y: maxY + CLEARANCE_RADIUS
                },
                {
                    x: minX - CLEARANCE_RADIUS,
                    y: (fy + ty) / 2
                },
                {
                    x: maxX + CLEARANCE_RADIUS,
                    y: (fy + ty) / 2
                },
            ];

            for (let waypoint of detourWaypoints) {
                const hits1 = getCollidingPointsOnLine(fx, fy, waypoint.x, waypoint.y, excludeIds);
                if (hits1.length > 0) continue;

                const hits2 = getCollidingPointsOnLine(waypoint.x, waypoint.y, tx, ty, excludeIds);
                if (hits2.length > 0) continue;

                return {
                    style: 'bezier',
                    controlPoints: [{
                        x: waypoint.x,
                        y: fy
                    }, {
                        x: waypoint.x,
                        y: ty
                    }]
                };
            }

            return null;
        }

        function redrawStep3() {
            if (!ctx3 || !soalImageObj) return;
            ctx3.clearRect(0, 0, canvas3.width, canvas3.height);
            ctx3.drawImage(soalImageObj, 0, 0);
            drawPoints(ctx3);
            drawPaths(ctx3, paths, '#3b82f6');
            if (tempPath) drawTempPath(ctx3);
            if (selectedPath && selectedPath.controlPoints && selectedPath.style !== 'straight') {
                const cpSizes = CanvasScaler.getControlPointSizes();
                for (let i = 0; i < selectedPath.controlPoints.length; i++) {
                    const cp = selectedPath.controlPoints[i];
                    ctx3.beginPath();
                    ctx3.arc(cp.x, cp.y, cpSizes.outer, 0, Math.PI * 2);
                    ctx3.fillStyle = 'rgba(16, 185, 129, 0.2)';
                    ctx3.fill();
                    ctx3.beginPath();
                    ctx3.arc(cp.x, cp.y, cpSizes.inner, 0, Math.PI * 2);
                    ctx3.fillStyle = '#10b981';
                    ctx3.fill();
                    ctx3.strokeStyle = '#fff';
                    ctx3.lineWidth = 2;
                    ctx3.stroke();
                    ctx3.fillStyle = '#10b981';
                    ctx3.font = 'bold ' + cpSizes.fontSize + 'px sans-serif';
                    ctx3.textAlign = 'center';
                    ctx3.fillText('CP' + (i + 1), cp.x, cp.y - cpSizes.labelOffsetY);
                }
                ctx3.fillStyle = '#3b82f6';
                ctx3.font = 'bold 12px sans-serif';
                ctx3.textAlign = 'left';
                ctx3.fillText('Drag titik hijau untuk atur bentuk garis', 10, canvas3.height - 10);
            }
        }

        function setTool(tool) {
            currentTool = tool;
            document.querySelectorAll('.tool-btn').forEach(b => b.classList.remove('active'));
            selectedPath = null;
            draggingControl = null;
            isDragging = false;
            if (tool === 'addPoint') {
                document.getElementById('btnAddPoint').classList.add('active');
                document.getElementById('toolInfo').textContent = 'Klik di gambar untuk menambah titik';
            } else if (tool === 'deletePoint') {
                document.getElementById('btnDeletePoint').classList.add('active');
                document.getElementById('toolInfo').textContent = 'Klik titik untuk menghapusnya';
            } else if (tool === 'editLabel') {
                document.getElementById('btnEditLabel').classList.add('active');
                document.getElementById('toolInfo').textContent = 'Klik titik untuk edit label';
            } else if (tool === 'drawPath') {
                document.getElementById('btnDrawPath').classList.add('active');
                document.getElementById('pathInfo').textContent = 'Klik titik A lalu titik B untuk membuat jalur';
            } else if (tool === 'deletePath') {
                document.getElementById('btnDeletePath').classList.add('active');
                document.getElementById('pathInfo').textContent = 'Klik jalur untuk menghapusnya';
            }
            if (ctx3) redrawStep3();
        }

        function drawPoints(ctx) {
            for (let point of points) {
                const px = parseFloat(point.x);
                const py = parseFloat(point.y);
                const s = POINT_SIZES;

                // Single scaling: CanvasScaler * (soalPointSize / 24)
                const m = soalPointSize / 24;

                ctx.beginPath();
                ctx.arc(px, py, s.outer * m, 0, Math.PI * 2);
                ctx.fillStyle = 'rgba(239, 68, 68, 0.15)';
                ctx.fill();

                ctx.beginPath();
                ctx.arc(px, py, s.middle * m, 0, Math.PI * 2);
                ctx.fillStyle = '#ef4444';
                ctx.fill();

                ctx.strokeStyle = '#fff';
                ctx.lineWidth = s.strokeWidth * m;
                ctx.stroke();

                ctx.beginPath();
                ctx.arc(px, py, s.inner * m, 0, Math.PI * 2);
                ctx.fillStyle = '#fff';
                ctx.fill();

                if (point.label) {
                    ctx.font = 'bold ' + (s.labelFontSize * m) + 'px sans-serif';
                    const textWidth = ctx.measureText(point.label).width;

                    ctx.fillStyle = 'rgba(255,255,255,0.95)';
                    ctx.beginPath();
                    ctx.roundRect(
                        px - (textWidth / 2 + s.labelPadding) * m,
                        py - s.labelOffsetY * m,
                        (textWidth + s.labelPadding * 2) * m,
                        s.labelHeight * m,
                        6 * m
                    );
                    ctx.fill();

                    ctx.strokeStyle = '#e2e8f0';
                    ctx.lineWidth = 1 * m;
                    ctx.stroke();

                    ctx.fillStyle = '#1e293b';
                    ctx.textAlign = 'center';
                    ctx.textBaseline = 'middle';
                    ctx.fillText(point.label, px, py - s.labelTextOffsetY * m);
                }
            }
        }

        function drawPaths(ctx, pathList, color) {
            const pSizes = CanvasScaler.getPathSizes();
            for (let path of pathList) {
                const fromPoint = points.find(p => p.tempId === path.fromTempId);
                const toPoint = points.find(p => p.tempId === path.toTempId);
                if (!fromPoint || !toPoint) continue;
                const fx = parseFloat(fromPoint.x),
                    fy = parseFloat(fromPoint.y);
                const tx = parseFloat(toPoint.x),
                    ty = parseFloat(toPoint.y);
                const isSelected = (path === selectedPath);
                const lineColor = isSelected ? '#2563eb' : color;
                const glowColor = isSelected ? 'rgba(37,99,235,0.2)' : color + '30';

                ctx.beginPath();
                ctx.moveTo(fx, fy);
                if (path.style === 'elbow' && path.controlPoints && path.controlPoints.length >= 2) {
                    ctx.lineTo(path.controlPoints[0].x, path.controlPoints[0].y);
                    ctx.lineTo(path.controlPoints[1].x, path.controlPoints[1].y);
                    ctx.lineTo(tx, ty);
                } else if (path.style === 'bezier' && path.controlPoints && path.controlPoints.length >= 2) {
                    ctx.bezierCurveTo(path.controlPoints[0].x, path.controlPoints[0].y, path.controlPoints[1].x, path.controlPoints[1].y, tx, ty);
                } else {
                    ctx.lineTo(tx, ty);
                }
                ctx.strokeStyle = glowColor;
                ctx.lineWidth = isSelected ? pSizes.glowWidthSelected : pSizes.glowWidth;
                ctx.lineCap = 'round';
                ctx.lineJoin = 'round';
                ctx.stroke();

                ctx.beginPath();
                ctx.moveTo(fx, fy);
                if (path.style === 'elbow' && path.controlPoints) {
                    ctx.lineTo(path.controlPoints[0].x, path.controlPoints[0].y);
                    ctx.lineTo(path.controlPoints[1].x, path.controlPoints[1].y);
                    ctx.lineTo(tx, ty);
                } else if (path.style === 'bezier' && path.controlPoints) {
                    ctx.bezierCurveTo(path.controlPoints[0].x, path.controlPoints[0].y, path.controlPoints[1].x, path.controlPoints[1].y, tx, ty);
                } else {
                    ctx.lineTo(tx, ty);
                }
                ctx.strokeStyle = lineColor;
                ctx.lineWidth = isSelected ? pSizes.mainWidthSelected : pSizes.mainWidth;
                ctx.lineCap = 'round';
                ctx.lineJoin = 'round';
                ctx.stroke();

                if (isSelected) {
                    ctx.setLineDash([pSizes.selectedDashWidth, pSizes.selectedDashWidth / 2]);
                    ctx.strokeStyle = 'rgba(37,99,235,0.3)';
                    ctx.lineWidth = pSizes.selectedDashWidth;
                    ctx.stroke();
                    ctx.setLineDash([]);
                }

                ctx.beginPath();
                ctx.arc(fx, fy, pSizes.endDotSize, 0, Math.PI * 2);
                ctx.arc(tx, ty, pSizes.endDotSize, 0, Math.PI * 2);
                ctx.fillStyle = lineColor;
                ctx.fill();
                ctx.beginPath();
                ctx.arc(fx, fy, pSizes.endDotInner, 0, Math.PI * 2);
                ctx.arc(tx, ty, pSizes.endDotInner, 0, Math.PI * 2);
                ctx.fillStyle = '#fff';
                ctx.fill();
            }
        }

        function drawTempPath(ctx) {
            const startPoint = points.find(p => p.tempId === tempPath.startTempId);
            if (!startPoint) return;
            const sx = parseFloat(startPoint.x),
                sy = parseFloat(startPoint.y);
            const ex = tempPath.end.x,
                ey = tempPath.end.y;
            const tSizes = CanvasScaler.getTempPathSizes();

            ctx.beginPath();
            ctx.moveTo(sx, sy);
            if (pathStyle === 'straight') {
                ctx.lineTo(ex, ey);
            } else if (pathStyle === 'elbow') {
                const dx = ex - sx,
                    dy = ey - sy;
                if (Math.abs(dx) > Math.abs(dy)) {
                    ctx.lineTo(sx + dx / 2, sy);
                    ctx.lineTo(sx + dx / 2, ey);
                } else {
                    ctx.lineTo(sx, sy + dy / 2);
                    ctx.lineTo(ex, sy + dy / 2);
                }
                ctx.lineTo(ex, ey);
            } else {
                const dx = ex - sx,
                    dy = ey - sy;
                const dist = Math.sqrt(dx * dx + dy * dy);
                const offset = Math.min(dist * 0.5, 120);
                ctx.bezierCurveTo(sx + (dx > 0 ? offset : -offset), sy, ex - (dx > 0 ? offset : -offset), ey, ex, ey);
            }
            ctx.strokeStyle = '#94a3b8';
            ctx.lineWidth = tSizes.lineWidth;
            ctx.setLineDash([tSizes.dashLength, tSizes.dashGap]);
            ctx.lineCap = 'round';
            ctx.lineJoin = 'round';
            ctx.stroke();
            ctx.setLineDash([]);
            ctx.beginPath();
            ctx.arc(ex, ey, tSizes.dotRadius, 0, Math.PI * 2);
            ctx.fillStyle = '#94a3b8';
            ctx.fill();
        }

        function getCanvasPos(e, canvas) {
            const rect = canvas.getBoundingClientRect();
            const scaleX = canvas.width / rect.width;
            const scaleY = canvas.height / rect.height;
            return {
                x: (e.clientX - rect.left) * scaleX,
                y: (e.clientY - rect.top) * scaleY
            };
        }

        function getPointAt(pos) {
            for (let point of points) {
                const px = parseFloat(point.x);
                const py = parseFloat(point.y);
                const dist = Math.sqrt((pos.x - px) ** 2 + (pos.y - py) ** 2);
                // Use global soalPointSize for hit radius
                const hitRadius = POINT_SIZES.hitRadius * (soalPointSize / 24);
                if (dist <= hitRadius) return point;
            }
            return null;
        }

        function getPathAt(pos) {
            for (let path of paths) {
                const fromPoint = points.find(p => p.tempId === path.fromTempId);
                const toPoint = points.find(p => p.tempId === path.toTempId);
                if (!fromPoint || !toPoint) continue;
                const fx = parseFloat(fromPoint.x),
                    fy = parseFloat(fromPoint.y);
                const tx = parseFloat(toPoint.x),
                    ty = parseFloat(toPoint.y);
                const dist = pointToPathDistance(pos.x, pos.y, path, fx, fy, tx, ty);
                if (dist < CanvasScaler.getPathHitRadius()) return path;
            }
            return null;
        }

        function pointToPathDistance(px, py, path, x1, y1, x2, y2) {
            if (path.style === 'straight' || !path.controlPoints) {
                const dx = x2 - x1,
                    dy = y2 - y1;
                const len = Math.sqrt(dx * dx + dy * dy);
                if (len === 0) return Math.sqrt((px - x1) ** 2 + (py - y1) ** 2);
                const t = Math.max(0, Math.min(1, ((px - x1) * dx + (py - y1) * dy) / (len * len)));
                const projX = x1 + t * dx,
                    projY = y1 + t * dy;
                return Math.sqrt((px - projX) ** 2 + (py - projY) ** 2);
            } else if (path.style === 'elbow') {
                let minDist = Infinity;
                const pts = [{
                    x: x1,
                    y: y1
                }, path.controlPoints[0], path.controlPoints[1], {
                    x: x2,
                    y: y2
                }];
                for (let i = 0; i < pts.length - 1; i++) {
                    const dx = pts[i + 1].x - pts[i].x,
                        dy = pts[i + 1].y - pts[i].y;
                    const len = Math.sqrt(dx * dx + dy * dy);
                    if (len === 0) continue;
                    const t = Math.max(0, Math.min(1, ((px - pts[i].x) * dx + (py - pts[i].y) * dy) / (len * len)));
                    const projX = pts[i].x + t * dx,
                        projY = pts[i].y + t * dy;
                    const d = Math.sqrt((px - projX) ** 2 + (py - projY) ** 2);
                    if (d < minDist) minDist = d;
                }
                return minDist;
            } else {
                let minDist = Infinity;
                const cp1 = path.controlPoints[0],
                    cp2 = path.controlPoints[1];
                for (let t = 0; t <= 1; t += 0.02) {
                    const bx = (1 - t) * (1 - t) * (1 - t) * x1 + 3 * (1 - t) * (1 - t) * t * cp1.x + 3 * (1 - t) * t * t * cp2.x + t * t * t * x2;
                    const by = (1 - t) * (1 - t) * (1 - t) * y1 + 3 * (1 - t) * (1 - t) * t * cp1.y + 3 * (1 - t) * t * t * cp2.y + t * t * t * y2;
                    const d = Math.sqrt((px - bx) ** 2 + (py - by) ** 2);
                    if (d < minDist) minDist = d;
                }
                return minDist;
            }
        }

        function saveSoal() {
            const idKategori = document.getElementById('idKategori').value;
            const namaSoal = document.getElementById('namaSoal').value.trim();
            const deskripsi = document.getElementById('deskripsiSoal').value.trim();
            const status = document.getElementById('statusSoal')?.value || 'aktif';

            if (!idKategori) {
                showNotification('Pilih kategori soal terlebih dahulu!', true);
                document.getElementById('idKategori').focus();
                return;
            }
            if (!namaSoal) {
                showNotification('Nama soal harus diisi!', true);
                return;
            }
            if (points.length === 0) {
                showNotification('Tambahkan minimal 1 titik!', true);
                return;
            }
            if (paths.length === 0) {
                showNotification('Tambahkan minimal 1 jalur!', true);
                return;
            }

            const formData = new FormData();

            if (soalImage && soalImage.startsWith('data:')) {
                const byteString = atob(soalImage.split(',')[1]);
                const mimeString = soalImage.split(',')[0].split(':')[1].split(';')[0];
                const ab = new ArrayBuffer(byteString.length);
                const ia = new Uint8Array(ab);
                for (let i = 0; i < byteString.length; i++) ia[i] = byteString.charCodeAt(i);
                const blob = new Blob([ab], {
                    type: mimeString
                });
                formData.append('gambar', new File([blob], 'soal.png', {
                    type: mimeString
                }));
            }

            formData.append('id_kategori', idKategori);
            formData.append('nama_soal', namaSoal);
            formData.append('deskripsi', deskripsi);
            formData.append('status', status);

            // All points use the same global soalPointSize
            const titikData = points.map(p => ({
                x: p.x,
                y: p.y,
                label: p.label || '',
                ukuran: soalPointSize
            }));
            formData.append('titik', JSON.stringify(titikData));

            const jalurData = paths.map(p => ({
                fromIndex: points.findIndex(pt => pt.tempId === p.fromTempId),
                toIndex: points.findIndex(pt => pt.tempId === p.toTempId),
                style: p.style || 'straight',
                controlPoints: p.controlPoints || null
            }));
            formData.append('jalur', JSON.stringify(jalurData));

            const csrfName = document.querySelector('meta[name="csrf-token-name"]')?.content || '<?= csrf_token() ?>';
            const csrfHash = document.querySelector('meta[name="csrf-token"]')?.content || '<?= csrf_hash() ?>';
            formData.append(csrfName, csrfHash);

            const url = editMode ? '/admin/soal/update/' + existingSoal.id : '/admin/soal/simpan';

            fetch(url, {
                    method: 'POST',
                    body: formData
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        showNotification(editMode ? 'Soal berhasil diupdate!' : 'Soal berhasil disimpan!');
                        setTimeout(() => window.location.href = '/admin/soal', 1500);
                    } else {
                        showNotification(data.message || 'Gagal!', true);
                    }
                })
                .catch(err => showNotification('Error: ' + err.message, true));
        }
    </script>
    <?= $this->endSection() ?>