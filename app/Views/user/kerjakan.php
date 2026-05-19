<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<nav class="breadcrumb">
    <a href="/user/kategori"><i class="fas fa-home"></i> Kategori</a>
    <span class="separator">/</span>
    <a href="/user/dashboard/<?= $soal['id_kategori'] ?? '' ?>"><?= $soal['kategori']['nama_kategori'] ?? 'Soal' ?></a>
    <span class="separator">/</span>
    <span class="text-slate-500">Kerjakan Soal</span>
</nav>

<div class="mb-4 sm:mb-6 flex flex-wrap justify-between items-center gap-3">
    <div>
        <a href="/user/dashboard/<?= $soal['id_kategori'] ?? '' ?>" class="back-btn">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
        <h1 class="text-xl sm:text-2xl font-bold text-slate-800 mt-1"><?= $soal['nama_soal'] ?></h1>
        <p class="text-slate-500 text-sm mt-1"><?= $soal['deskripsi'] ?? '' ?></p>
    </div>
    <div class="flex gap-2">
        <button onclick="resetJawaban()" class="bg-slate-200 hover:bg-slate-300 text-slate-700 px-3 sm:px-4 py-2 rounded-lg font-medium transition text-sm">
            <i class="fas fa-redo mr-1"></i><span class="hidden sm:inline">Reset</span>
        </button>
        <button onclick="checkJawaban()" class="bg-green-500 hover:bg-green-600 text-white px-3 sm:px-4 py-2 rounded-lg font-medium transition text-sm">
            <i class="fas fa-check-circle mr-1"></i><span class="hidden sm:inline">Periksa</span>
        </button>
    </div>
</div>

<div class="bg-white rounded-xl shadow-sm border border-slate-200 p-3 sm:p-4 mb-4">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div class="flex items-center gap-4 text-xs sm:text-sm text-slate-600">
            <div class="flex items-center gap-2">
                <span class="w-3 h-3 rounded-full bg-red-500"></span>
                <span>Titik koneksi</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="w-8 h-1 bg-green-500 rounded"></span>
                <span>Jalur kamu</span>
            </div>
        </div>
        <div class="flex items-center gap-3">
            <div class="text-sm font-medium" id="pathCounter">
                <span class="text-slate-500">Jalur:</span>
                <span class="text-blue-600 font-bold" id="currentPathCount">0</span>
                <span class="text-slate-400">/</span>
                <span class="text-slate-600" id="maxPathCount">0</span>
            </div>
            <div class="text-xs text-slate-400 hidden sm:inline">
                <i class="fas fa-info-circle mr-1"></i>
                Klik/drag titik A ke titik B untuk sambung. Klik jalur untuk hapus.
            </div>
        </div>
    </div>
</div>

<div class="canvas-wrapper" id="userCanvasWrapper">
    <div class="fs-controls">
        <button class="fs-btn" onclick="toggleFullscreen()" title="Fullscreen">
            <i class="fas fa-expand"></i>
        </button>
    </div>
    <canvas id="userCanvas" class="main-canvas"></canvas>
</div>

<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<style>
    /* Fullscreen styles - works on both desktop and mobile */
    .canvas-wrapper.fullscreen-active {
        position: fixed !important;
        top: 0;
        left: 0;
        width: 100vw !important;
        height: 100vh !important;
        z-index: 9999;
        background: #0f172a;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 10px;
        box-sizing: border-box;
    }

    .canvas-wrapper.fullscreen-active canvas {
        max-width: 100%;
        max-height: 100%;
        width: auto;
        height: auto;
        object-fit: contain;
    }

    .canvas-wrapper.fullscreen-active .fs-controls {
        position: fixed;
        top: 10px;
        right: 10px;
        z-index: 10000;
    }

    /* Native fullscreen API support */
    .canvas-wrapper:fullscreen {
        display: flex;
        align-items: center;
        justify-content: center;
        background: #0f172a;
        padding: 10px;
    }

    .canvas-wrapper:fullscreen canvas {
        max-width: 100%;
        max-height: 100%;
        width: auto;
        height: auto;
    }

    .canvas-wrapper:-webkit-full-screen {
        display: flex;
        align-items: center;
        justify-content: center;
        background: #0f172a;
        padding: 10px;
    }

    .canvas-wrapper:-webkit-full-screen canvas {
        max-width: 100%;
        max-height: 100%;
        width: auto;
        height: auto;
    }

    .canvas-wrapper:-moz-full-screen {
        display: flex;
        align-items: center;
        justify-content: center;
        background: #0f172a;
        padding: 10px;
    }

    .canvas-wrapper:-moz-full-screen canvas {
        max-width: 100%;
        max-height: 100%;
        width: auto;
        height: auto;
    }

    .main-canvas {
        touch-action: pinch-zoom;
    }

    /* Ensure canvas has proper spacing from bottom nav */
    .canvas-wrapper {
        margin-bottom: 70px;
        overscroll-behavior: contain;
    }

    /* Mobile-specific adjustments */
    @media (max-width: 640px) {
        .canvas-wrapper {
            margin-bottom: 80px;
            border-radius: 8px;
            overflow: hidden;
        }
    }

    /* Fix for devices with bottom navigation bars */
    @supports (padding-bottom: env(safe-area-inset-bottom)) {
        .canvas-wrapper {
            margin-bottom: calc(80px + env(safe-area-inset-bottom));
        }
    }

    /* Breadcrumb mobile adjustments */
    @media (max-width: 640px) {

        /* Override main layout padding */
        body>main {
            padding-top: 0.75rem !important;
            padding-bottom: 1rem !important;
        }

        /* Override breadcrumb margin from layout */
        .breadcrumb {
            margin-bottom: 0.5rem !important;
        }

        .breadcrumb a,
        .breadcrumb span {
            font-size: 0.7rem;
        }

        .breadcrumb .separator {
            margin: 0 0.25rem;
        }

    }

    .canvas-wrapper {
        width: 100%;
        overflow: hidden;
    }

    .main-canvas {
        width: 100%;
        height: auto;
        display: block;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    const soalData = <?= json_encode($soal, JSON_NUMERIC_CHECK) ?>;
    let points = soalData.titik || [];
    let correctPaths = soalData.jalur_jawaban || [];
    let userPaths = [];
    let tempPath = null;
    let userCanvas, userCtx;
    let soalImageObj = null;

    // ==================== PER-SOAL POINT SIZE ====================
    // Read ukuran from database (from first titik, since all titik have same ukuran per soal)
    let soalPointSize = 24; // default
    if (points.length > 0 && points[0].ukuran) {
        const dbSize = parseInt(points[0].ukuran);
        if (dbSize >= 1 && dbSize <= 48) {
            soalPointSize = dbSize;
        }
    }

    // Drag-and-drop state (like chess)
    let dragState = {
        isDragging: false,
        startPoint: null,
        currentPos: null,
        touchId: null
    };

    // For control point editing
    let userSelectedPath = null;
    let userDraggingControl = null;
    let userIsDraggingControl = false;

    // Max paths
    const maxPaths = correctPaths.length;
    const CLEARANCE_RADIUS = 45;
    const GRID = 20; // Grid size for A* algorithm

    // Dynamic point sizes based on soalPointSize
    function getPointSizes() {
        const m = soalPointSize / 24; // multiplier relative to base 24px
        return {
            outer: 30 * m,
            middle: 20 * m,
            inner: 8 * m,
            hitRadius: 38 * m,
            labelFontSize: 14 * m,
            labelPadding: 6 * m,
            labelHeight: 22 * m,
            labelOffsetY: 42 * m,
            labelTextOffsetY: 31 * m,
            strokeWidth: 3 * m
        };
    }

    // ========== GRID COORDINATE SYSTEM (from OR Router) ==========
    function gx(x) {
        return Math.floor(x / GRID);
    }

    function gy(y) {
        return Math.floor(y / GRID);
    }

    function px(x) {
        return x * GRID + GRID / 2;
    }

    function py(y) {
        return y * GRID + GRID / 2;
    }

    function snapToGrid(val) {
        return Math.floor(val / GRID) * GRID + GRID / 2;
    }
    // ============================================================

    // ========== A* PATHFINDING (from OR Router) ==========
    function makeBlockedSet(excludeIds) {
        const blocked = new Set();
        for (let point of points) {
            const pid = String(point.id);
            if (excludeIds && excludeIds.includes(pid)) continue;

            const cx = gx(parseFloat(point.x));
            const cy = gy(parseFloat(point.y));

            const r = Math.ceil(CLEARANCE_RADIUS / GRID) + 1;

            for (let dy = -r; dy <= r; dy++) {
                for (let dx = -r; dx <= r; dx++) {
                    blocked.add(`${cx + dx},${cy + dy}`);
                }
            }
        }
        return blocked;
    }

    function astar(start, end, blocked) {
        const key = p => `${p.x},${p.y}`;

        const open = [start];
        const came = {};
        const g = {};
        const f = {};

        g[key(start)] = 0;
        f[key(start)] = Math.abs(start.x - end.x) + Math.abs(start.y - end.y);

        const dirs = [{
                x: 1,
                y: 0
            },
            {
                x: -1,
                y: 0
            },
            {
                x: 0,
                y: 1
            },
            {
                x: 0,
                y: -1
            },
            {
                x: 1,
                y: 1
            },
            {
                x: -1,
                y: 1
            },
            {
                x: 1,
                y: -1
            },
            {
                x: -1,
                y: -1
            }
        ];

        while (open.length) {
            open.sort((a, b) => f[key(a)] - f[key(b)]);
            const current = open.shift();

            if (current.x === end.x && current.y === end.y) {
                const path = [];
                let cur = current;
                while (cur) {
                    path.push(cur);
                    cur = came[key(cur)];
                }
                return path.reverse();
            }

            for (const d of dirs) {
                const next = {
                    x: current.x + d.x,
                    y: current.y + d.y
                };
                const nk = key(next);

                if (blocked.has(nk)) continue;

                const tentative = g[key(current)] + (d.x !== 0 && d.y !== 0 ? 1.414 : 1);

                if (g[nk] === undefined || tentative < g[nk]) {
                    came[nk] = current;
                    g[nk] = tentative;
                    f[nk] = tentative + Math.abs(next.x - end.x) + Math.abs(next.y - end.y);

                    if (!open.find(p => p.x === next.x && p.y === next.y)) {
                        open.push(next);
                    }
                }
            }
        }
        return [];
    }

    // Simplify A* path by removing collinear points
    function simplifyPath(path) {
        if (path.length < 3) return path;

        const out = [path[0]];
        for (let i = 1; i < path.length - 1; i++) {
            const a = path[i - 1];
            const b = path[i];
            const c = path[i + 1];

            const dx1 = b.x - a.x;
            const dy1 = b.y - a.y;
            const dx2 = c.x - b.x;
            const dy2 = c.y - b.y;

            if (dx1 !== dx2 || dy1 !== dy2) {
                out.push(b);
            }
        }
        out.push(path[path.length - 1]);
        return out;
    }

    // Convert A* grid path to control points for rendering
    function gridPathToControlPoints(gridPath, fx, fy, tx, ty) {
        if (gridPath.length < 2) {
            return {
                style: 'straight',
                controlPoints: null
            };
        }

        // Convert grid coordinates to pixel coordinates
        const pixelPath = gridPath.map(p => ({
            x: px(p.x),
            y: py(p.y)
        }));

        // Convert to elbow style with control points
        if (pixelPath.length === 2) {
            return {
                style: 'straight',
                controlPoints: null
            };
        }

        // Find key waypoints (corners)
        const waypoints = [];
        for (let i = 1; i < pixelPath.length - 1; i++) {
            const prev = pixelPath[i - 1];
            const curr = pixelPath[i];
            const next = pixelPath[i + 1];

            const dx1 = curr.x - prev.x;
            const dy1 = curr.y - prev.y;
            const dx2 = next.x - curr.x;
            const dy2 = next.y - curr.y;

            if (dx1 !== dx2 || dy1 !== dy2) {
                waypoints.push(curr);
            }
        }

        if (waypoints.length === 0) {
            return {
                style: 'straight',
                controlPoints: null
            };
        }

        // Take first two waypoints as control points
        const cp1 = waypoints[0];
        const cp2 = waypoints.length > 1 ? waypoints[1] : {
            x: (cp1.x + tx) / 2,
            y: (cp1.y + ty) / 2
        };

        return {
            style: 'elbow',
            controlPoints: [cp1, cp2],
            allWaypoints: pixelPath
        };
    }

    // A* based route function
    function routeAStar(fromPoint, toPoint) {
        const fx = parseFloat(fromPoint.x);
        const fy = parseFloat(fromPoint.y);
        const tx = parseFloat(toPoint.x);
        const ty = parseFloat(toPoint.y);
        const excludeIds = [String(fromPoint.id), String(toPoint.id)];

        const start = {
            x: gx(fx),
            y: gy(fy)
        };
        const end = {
            x: gx(tx),
            y: gy(ty)
        };

        const blocked = makeBlockedSet(excludeIds);

        const rawPath = astar(start, end, blocked);
        const simplified = simplifyPath(rawPath);

        return gridPathToControlPoints(simplified, fx, fy, tx, ty);
    }
    // ============================================================

    window.onload = function() {
        document.getElementById('maxPathCount').textContent = maxPaths;
        updatePathCounter();

        soalImageObj = new Image();
        soalImageObj.onload = function() {
            initUserCanvas();
        };
        soalImageObj.onerror = function() {
            showNotification('Gagal memuat gambar!', true);
        };
        soalImageObj.src = '/uploads/soal/' + soalData.gambar;
    };

    function updatePathCounter() {
        const countEl = document.getElementById('currentPathCount');
        countEl.textContent = userPaths.length;
        if (userPaths.length >= maxPaths) {
            countEl.className = 'text-green-600 font-bold';
        } else {
            countEl.className = 'text-blue-600 font-bold';
        }
    }

    function initUserCanvas() {
        const canvas = document.getElementById('userCanvas');

        const originalWidth = soalData.img_width || soalImageObj.naturalWidth || 1600;
        const originalHeight = soalData.img_height || soalImageObj.naturalHeight || 900;

        canvas.width = originalWidth;
        canvas.height = originalHeight;

        userCanvas = canvas;
        userCtx = canvas.getContext('2d');

        // pasang event sekali saja
        canvas.addEventListener('mousedown', onMouseDown);
        canvas.addEventListener('mousemove', onMouseMove);
        canvas.addEventListener('mouseup', onMouseUp);
        canvas.addEventListener('mouseleave', onMouseUp);

        canvas.addEventListener('touchstart', onTouchStart, {
            passive: false
        });
        canvas.addEventListener('touchmove', onTouchMove, {
            passive: false
        });
        canvas.addEventListener('touchend', onTouchEnd, {
            passive: false
        });
        canvas.addEventListener('touchcancel', onTouchEnd, {
            passive: true
        });

        redrawUser();
    }

    // ========== FULLSCREEN (Desktop + Mobile) ==========
    function toggleFullscreen() {
        const wrapper = document.getElementById('userCanvasWrapper');

        // Try native fullscreen API first
        if (!document.fullscreenElement && !document.webkitFullscreenElement && !document.mozFullScreenElement) {
            if (wrapper.requestFullscreen) {
                wrapper.requestFullscreen().catch(() => fallbackFullscreen(wrapper));
            } else if (wrapper.webkitRequestFullscreen) {
                wrapper.webkitRequestFullscreen().catch(() => fallbackFullscreen(wrapper));
            } else if (wrapper.msRequestFullscreen) {
                wrapper.msRequestFullscreen().catch(() => fallbackFullscreen(wrapper));
            } else {
                fallbackFullscreen(wrapper);
            }
        } else {
            if (document.exitFullscreen) {
                document.exitFullscreen();
            } else if (document.webkitExitFullscreen) {
                document.webkitExitFullscreen();
            } else if (document.msExitFullscreen) {
                document.msExitFullscreen();
            }
            wrapper.classList.remove('fullscreen-active');
        }
    }

    function fallbackFullscreen(wrapper) {
        // Fallback for mobile browsers that don't support fullscreen API well
        wrapper.classList.toggle('fullscreen-active');
        setTimeout(redrawUser, 100);
    }

    document.addEventListener('fullscreenchange', onFullscreenChange);
    document.addEventListener('webkitfullscreenchange', onFullscreenChange);
    document.addEventListener('mozfullscreenchange', onFullscreenChange);
    document.addEventListener('MSFullscreenChange', onFullscreenChange);

    function onFullscreenChange() {
        const wrapper = document.getElementById('userCanvasWrapper');
        const isFullscreen = !!(document.fullscreenElement || document.webkitFullscreenElement || document.mozFullScreenElement);

        if (isFullscreen) {
            wrapper.classList.add('fullscreen-active');
        } else {
            wrapper.classList.remove('fullscreen-active');
        }

        setTimeout(redrawUser, 100);
    }
    // ====================================

    // ========== COORDINATE CONVERSION ==========
    function getCanvasPosFromMouse(e) {
        const rect = userCanvas.getBoundingClientRect();
        const scaleX = userCanvas.width / rect.width;
        const scaleY = userCanvas.height / rect.height;
        return {
            x: (e.clientX - rect.left) * scaleX,
            y: (e.clientY - rect.top) * scaleY
        };
    }

    function getCanvasPosFromTouch(touch) {
        const rect = userCanvas.getBoundingClientRect();
        const scaleX = userCanvas.width / rect.width;
        const scaleY = userCanvas.height / rect.height;
        return {
            x: (touch.clientX - rect.left) * scaleX,
            y: (touch.clientY - rect.top) * scaleY
        };
    }
    // ====================================

    // ========== MOUSE EVENTS (Desktop) ==========
    function onMouseDown(e) {
        const pos = getCanvasPosFromMouse(e);
        handlePointerDown(pos);
    }

    function onMouseMove(e) {
        const pos = getCanvasPosFromMouse(e);
        handlePointerMove(pos);
    }

    function onMouseUp(e) {
        handlePointerUp();
    }
    // ====================================

    // ========== TOUCH EVENTS (Mobile) ==========
    // Strategy: 
    // - 1 finger on point = drag/connect (preventDefault)
    // - 1 finger NOT on point = click/path selection (allow browser scroll)
    // - Multi-touch anytime = cancel drag, let browser handle pinch zoom

    function cancelDrag() {
        // Cancel any ongoing drag operation
        dragState.isDragging = false;
        dragState.startPoint = null;
        dragState.touchId = null;
        dragState.currentPos = null;
        tempPath = null;
        userIsDraggingControl = false;
        userDraggingControl = null;
        redrawUser();
    }

    function onTouchStart(e) {
        // Multi-touch (2+ jari) = biarkan browser pinch-zoom/pan
        // Cancel any ongoing drag immediately
        if (e.touches.length > 1) {
            cancelDrag();
            return; // Don't preventDefault - let browser handle pinch zoom
        }

        const touch = e.touches[0];
        const pos = getCanvasPosFromTouch(touch);

        // Check if touching a point
        const point = getPointAt(pos);
        if (point) {
            // ON POINT: Start drag - prevent default to stop page scroll
            e.preventDefault();

            dragState.isDragging = true;
            dragState.startPoint = point;
            dragState.currentPos = pos;
            dragState.touchId = touch.identifier;

            // Also handle as immediate click for desktop-like behavior
            handlePointerDown(pos);
        } else {
            // NOT ON POINT: Allow browser to handle (scroll, pinch zoom, etc.)
            // Don't call preventDefault - this allows pinch zoom to work
            dragState.isDragging = false;
            dragState.startPoint = null;
            // Don't call handlePointerDown here - we want browser to handle scroll
        }
    }

    function onTouchMove(e) {
        // Multi-touch = pinch zoom/pan, biarkan browser handle
        if (e.touches.length > 1) {
            // Second finger touched while dragging - cancel drag and let browser zoom
            if (dragState.isDragging) {
                cancelDrag();
            }
            return; // Don't preventDefault - let browser handle pinch zoom
        }

        const touch = e.touches[0];

        // Only track the same touch that started
        if (dragState.isDragging && dragState.touchId === touch.identifier) {
            // Only prevent default when actually dragging a point
            e.preventDefault();

            const pos = getCanvasPosFromTouch(touch);
            dragState.currentPos = pos;

            // If moved significantly from start point, show drag line
            if (dragState.startPoint) {
                const startPos = {
                    x: parseFloat(dragState.startPoint.x),
                    y: parseFloat(dragState.startPoint.y)
                };
                const dist = Math.sqrt((pos.x - startPos.x) ** 2 + (pos.y - startPos.y) ** 2);

                if (dist > 10) {
                    // Show drag line (temp path)
                    tempPath = {
                        startId: String(dragState.startPoint.id),
                        end: pos
                    };
                    redrawUser();
                }
            }
        } else if (userIsDraggingControl) {
            e.preventDefault();
            const pos = getCanvasPosFromTouch(touch);
            handlePointerMove(pos);
        }
        // If not dragging and not dragging control, don't prevent default
        // This allows page scroll when touching empty areas
    }

    function onTouchEnd(e) {
        // If still have active touches (multi-touch ending), ignore
        if (e.touches.length > 0) return;

        // All touches ended - complete the drag if any
        const touch = e.changedTouches[0];

        if (dragState.isDragging && dragState.touchId === touch.identifier) {
            const pos = getCanvasPosFromTouch(touch);
            const endPoint = getPointAt(pos);

            // If released on a different point, complete the connection
            if (endPoint && dragState.startPoint &&
                String(endPoint.id) !== String(dragState.startPoint.id)) {
                // Complete drag-and-drop connection
                handleDragComplete(dragState.startPoint, endPoint);
            }

            // Reset drag state
            dragState.isDragging = false;
            dragState.startPoint = null;
            dragState.touchId = null;
            dragState.currentPos = null;
            tempPath = null;
            redrawUser();
        }

        handlePointerUp();
    }
    // ====================================

    // ========== POINTER LOGIC (Unified) ==========
    function handlePointerDown(pos) {
        // Check control points first
        if (userSelectedPath && userSelectedPath.controlPoints) {
            for (let i = 0; i < userSelectedPath.controlPoints.length; i++) {
                const cp = userSelectedPath.controlPoints[i];
                const dist = Math.sqrt((pos.x - cp.x) ** 2 + (pos.y - cp.y) ** 2);
                if (dist < 20) {
                    userDraggingControl = i;
                    userIsDraggingControl = true;
                    userCanvas.style.cursor = 'grabbing';
                    return;
                }
            }
        }

        const point = getPointAt(pos);
        if (point) {
            if (!tempPath && userPaths.length >= maxPaths) {
                showNotification('Maksimal ' + maxPaths + ' jalur! Klik jalur yang sudah ada untuk menghapusnya.', true);
                return;
            }

            if (!tempPath) {
                tempPath = {
                    startId: String(point.id),
                    end: pos
                };
                redrawUser();
            } else {
                if (tempPath.startId !== String(point.id)) {
                    completeConnection(tempPath.startId, String(point.id));
                }
                tempPath = null;
                redrawUser();
            }
        } else {
            const clickedPath = getUserPathAt(pos);
            if (clickedPath) {
                if (userSelectedPath === clickedPath) {
                    userPaths = userPaths.filter(p => p !== clickedPath);
                    userSelectedPath = null;
                    updatePathCounter();
                    showNotification('Jalur dihapus! (' + userPaths.length + '/' + maxPaths + ')');
                } else {
                    userSelectedPath = clickedPath;
                    if (clickedPath.style !== 'straight') {
                        showNotification('Jalur dipilih. Klik lagi untuk hapus, atau drag titik hijau untuk atur bentuk.');
                    } else {
                        showNotification('Jalur dipilih. Klik lagi untuk menghapusnya.');
                    }
                }
                redrawUser();
            } else {
                userSelectedPath = null;
                redrawUser();
            }
        }
    }

    function handlePointerMove(pos) {
        if (userIsDraggingControl && userDraggingControl !== null && userSelectedPath) {
            userSelectedPath.controlPoints[userDraggingControl] = {
                x: pos.x,
                y: pos.y
            };
            redrawUser();
            return;
        }
        if (!tempPath) return;
        tempPath.end = pos;
        redrawUser();
    }

    function handlePointerUp() {
        userDraggingControl = null;
        userIsDraggingControl = false;
        if (userCanvas) userCanvas.style.cursor = 'crosshair';
    }

    function handleDragComplete(fromPoint, toPoint) {
        const exists = userPaths.some(p =>
            (p.fromId === String(fromPoint.id) && p.toId === String(toPoint.id)) ||
            (p.fromId === String(toPoint.id) && p.toId === String(fromPoint.id))
        );

        if (!exists) {
            if (userPaths.length >= maxPaths) {
                showNotification('Maksimal ' + maxPaths + ' jalur! Hapus jalur dulu.', true);
                return;
            }

            // Auto-routing
            const route = generateAutoRoutingControlPoints(fromPoint, toPoint);

            const newPath = {
                fromId: String(fromPoint.id),
                toId: String(toPoint.id),
                style: route.style,
                controlPoints: route.controlPoints
            };
            userPaths.push(newPath);
            userSelectedPath = newPath;
            updatePathCounter();

            if (route.style !== 'straight') {
                showNotification('Jalur otomatis diatur menghindari titik! (' + userPaths.length + '/' + maxPaths + ')');
            } else {
                showNotification('Jalur tersambung! (' + userPaths.length + '/' + maxPaths + ')');
            }
            redrawUser();
        }
    }

    function completeConnection(fromId, toId) {
        const exists = userPaths.some(p =>
            (p.fromId === fromId && p.toId === toId) ||
            (p.fromId === toId && p.toId === fromId)
        );

        if (!exists) {
            if (userPaths.length >= maxPaths) {
                showNotification('Maksimal ' + maxPaths + ' jalur! Hapus jalur dulu.', true);
                return;
            }

            const fromPoint = points.find(p => String(p.id) === fromId);
            const toPoint = points.find(p => String(p.id) === toId);

            const route = generateAutoRoutingControlPoints(fromPoint, toPoint);

            const newPath = {
                fromId: fromId,
                toId: toId,
                style: route.style,
                controlPoints: route.controlPoints
            };
            userPaths.push(newPath);
            userSelectedPath = newPath;
            updatePathCounter();

            if (route.style !== 'straight') {
                showNotification('Jalur otomatis diatur menghindari titik! (' + userPaths.length + '/' + maxPaths + ')');
            } else {
                showNotification('Jalur tersambung! (' + userPaths.length + '/' + maxPaths + ')');
            }
        }
    }
    // ====================================

    // ============================================================
    // SMART AUTO-ROUTING
    // ============================================================

    function getCollidingPointsOnLine(x1, y1, x2, y2, excludeIds) {
        const colliding = [];
        for (let point of points) {
            const pid = String(point.id);
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
                const pid = String(point.id);
                if (excludeIds && excludeIds.includes(pid)) continue;
                const px = parseFloat(point.x);
                const py = parseFloat(point.y);
                const dist = Math.sqrt((px - bx) ** 2 + (py - by) ** 2);
                if (dist < CLEARANCE_RADIUS) return true;
            }
        }
        return false;
    }

    function generateAutoRoutingControlPoints(fromPoint, toPoint) {
        const fx = parseFloat(fromPoint.x);
        const fy = parseFloat(fromPoint.y);
        const tx = parseFloat(toPoint.x);
        const ty = parseFloat(toPoint.y);
        const dx = tx - fx;
        const dy = ty - fy;
        const dist = Math.sqrt(dx * dx + dy * dy);
        const excludeIds = [String(fromPoint.id), String(toPoint.id)];

        // 1. Try straight line first
        const straightHits = getCollidingPointsOnLine(fx, fy, tx, ty, excludeIds);
        if (straightHits.length === 0) {
            return {
                style: 'straight',
                controlPoints: null
            };
        }

        // 2. Standard elbow
        let elbowCP = generateStandardElbow(fx, fy, tx, ty);
        if (!elbowPathCollides(fx, fy, elbowCP[0], elbowCP[1], tx, ty, excludeIds)) {
            return {
                style: 'elbow',
                controlPoints: elbowCP
            };
        }

        // 3. Small offset elbow
        const midX = (fx + tx) / 2;
        const midY = (fy + ty) / 2;
        const isHorizontalPrimary = Math.abs(dx) > Math.abs(dy);

        const smallAttempts = [{
                offsetX: 0,
                offsetY: -CLEARANCE_RADIUS * 1.5
            },
            {
                offsetX: 0,
                offsetY: CLEARANCE_RADIUS * 1.5
            },
            {
                offsetX: -CLEARANCE_RADIUS * 1.5,
                offsetY: 0
            },
            {
                offsetX: CLEARANCE_RADIUS * 1.5,
                offsetY: 0
            },
        ];

        for (let attempt of smallAttempts) {
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

        // 4. Medium offset elbow
        const mediumAttempts = [{
                offsetX: 0,
                offsetY: -CLEARANCE_RADIUS * 2.5
            },
            {
                offsetX: 0,
                offsetY: CLEARANCE_RADIUS * 2.5
            },
            {
                offsetX: -CLEARANCE_RADIUS * 2.5,
                offsetY: 0
            },
            {
                offsetX: CLEARANCE_RADIUS * 2.5,
                offsetY: 0
            },
        ];

        for (let attempt of mediumAttempts) {
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

        // 5. Double bend elbow
        const doubleBendAttempts = [
            [{
                x: fx,
                y: fy - CLEARANCE_RADIUS * 2
            }, {
                x: tx,
                y: fy - CLEARANCE_RADIUS * 2
            }],
            [{
                x: fx,
                y: fy + CLEARANCE_RADIUS * 2
            }, {
                x: tx,
                y: fy + CLEARANCE_RADIUS * 2
            }],
            [{
                x: fx - CLEARANCE_RADIUS * 2,
                y: fy
            }, {
                x: fx - CLEARANCE_RADIUS * 2,
                y: ty
            }],
            [{
                x: fx + CLEARANCE_RADIUS * 2,
                y: fy
            }, {
                x: fx + CLEARANCE_RADIUS * 2,
                y: ty
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

        // 6. Bezier curves
        const offset = Math.min(dist * 0.5, 150);
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
                y: fy - CLEARANCE_RADIUS * 2
            }, {
                x: tx - dx * 0.2,
                y: ty - CLEARANCE_RADIUS * 2
            }],
            [{
                x: fx + dx * 0.2,
                y: fy + CLEARANCE_RADIUS * 2
            }, {
                x: tx - dx * 0.2,
                y: ty + CLEARANCE_RADIUS * 2
            }],
            [{
                x: fx + dx * 0.3,
                y: fy - CLEARANCE_RADIUS * 3
            }, {
                x: tx - dx * 0.3,
                y: ty - CLEARANCE_RADIUS * 3
            }],
            [{
                x: fx + dx * 0.3,
                y: fy + CLEARANCE_RADIUS * 3
            }, {
                x: tx - dx * 0.3,
                y: ty + CLEARANCE_RADIUS * 3
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

        // 7. A* Pathfinding (from OR Router) - Use when geometric fails
        const astarResult = routeAStar(fromPoint, toPoint);
        if (astarResult && astarResult.style !== 'straight') {
            return astarResult;
        }

        // 8. Detour (if A* returns straight, try detour)
        const detour = findDetourPath(fromPoint, toPoint, straightHits.map(h => h.point));
        if (detour) return detour;

        // 9. Fallback to A* even if it returned straight (ensure no collision)
        if (astarResult) return astarResult;

        // 10. Final fallback
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
        const excludeIds = [String(fromPoint.id), String(toPoint.id)];

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
    // ============================================================

    // ========== GRID DRAWING ==========
    function drawGrid(ctx) {
        const w = userCanvas.width;
        const h = userCanvas.height;

        // Draw grid lines
        ctx.strokeStyle = '#e0e0e0';
        ctx.lineWidth = 1;

        // Vertical lines
        for (let x = 0; x <= w; x += GRID) {
            ctx.beginPath();
            ctx.moveTo(x, 0);
            ctx.lineTo(x, h);
            ctx.stroke();
        }

        // Horizontal lines
        for (let y = 0; y <= h; y += GRID) {
            ctx.beginPath();
            ctx.moveTo(0, y);
            ctx.lineTo(w, y);
            ctx.stroke();
        }
    }
    // =================================

    function redrawUser() {
        if (!userCtx || !soalImageObj) return;
        userCtx.clearRect(0, 0, userCanvas.width, userCanvas.height);

        // White background
        userCtx.fillStyle = '#ffffff';
        userCtx.fillRect(0, 0, userCanvas.width, userCanvas.height);

        // Draw grid first
        drawGrid(userCtx);

        // Draw soal image
        userCtx.drawImage(
            soalImageObj,
            0,
            0,
            userCanvas.width,
            userCanvas.height
        );
        drawPoints(userCtx);

        // Draw user paths only (correct paths hidden)
        drawUserPaths(userCtx, userPaths, '#10b981');

        // Draw temp path (drag line or click line)
        if (tempPath) drawUserTempPath(userCtx);

        // Draw control points for selected path
        if (userSelectedPath && userSelectedPath.controlPoints && userSelectedPath.style !== 'straight') {
            for (let i = 0; i < userSelectedPath.controlPoints.length; i++) {
                const cp = userSelectedPath.controlPoints[i];
                userCtx.beginPath();
                userCtx.arc(cp.x, cp.y, 14, 0, Math.PI * 2);
                userCtx.fillStyle = 'rgba(16, 185, 129, 0.2)';
                userCtx.fill();
                userCtx.beginPath();
                userCtx.arc(cp.x, cp.y, 9, 0, Math.PI * 2);
                userCtx.fillStyle = '#10b981';
                userCtx.fill();
                userCtx.strokeStyle = '#fff';
                userCtx.lineWidth = 2;
                userCtx.stroke();
                userCtx.fillStyle = '#10b981';
                userCtx.font = 'bold 11px sans-serif';
                userCtx.textAlign = 'center';
                userCtx.fillText('CP' + (i + 1), cp.x, cp.y - 16);
            }
        }

        // Bottom hints
        if (userSelectedPath) {
            userCtx.fillStyle = '#ef4444';
            userCtx.font = 'bold 12px sans-serif';
            userCtx.textAlign = 'left';
            userCtx.fillText('🗑️ Klik jalur ini lagi untuk HAPUS', 10, userCanvas.height - 10);
        } else if (dragState.isDragging && dragState.startPoint) {
            userCtx.fillStyle = '#3b82f6';
            userCtx.font = 'bold 12px sans-serif';
            userCtx.textAlign = 'left';
            userCtx.fillText('⬆️ Lepaskan di titik tujuan', 10, userCanvas.height - 10);
        } else if (userPaths.length < maxPaths) {
            userCtx.fillStyle = '#3b82f6';
            userCtx.font = 'bold 12px sans-serif';
            userCtx.textAlign = 'left';
            userCtx.fillText('💡 Klik/drag titik A → titik B (' + userPaths.length + '/' + maxPaths + ')', 10, userCanvas.height - 10);
        } else {
            userCtx.fillStyle = '#f59e0b';
            userCtx.font = 'bold 12px sans-serif';
            userCtx.textAlign = 'left';
            userCtx.fillText('⚠️ Jalur penuh! Klik jalur untuk hapus', 10, userCanvas.height - 10);
        }
    }

    function drawPoints(ctx) {
        const s = getPointSizes();
        const m = soalPointSize / 24; // multiplier for label positioning

        for (let point of points) {
            const px = parseFloat(point.x);
            const py = parseFloat(point.y);

            ctx.beginPath();
            ctx.arc(px, py, s.outer, 0, Math.PI * 2);
            ctx.fillStyle = 'rgba(239, 68, 68, 0.15)';
            ctx.fill();

            ctx.beginPath();
            ctx.arc(px, py, s.middle, 0, Math.PI * 2);
            ctx.fillStyle = '#ef4444';
            ctx.fill();
            ctx.strokeStyle = '#fff';
            ctx.lineWidth = s.strokeWidth;
            ctx.stroke();

            ctx.beginPath();
            ctx.arc(px, py, s.inner, 0, Math.PI * 2);
            ctx.fillStyle = '#fff';
            ctx.fill();

            if (point.label) {
                ctx.font = 'bold ' + s.labelFontSize + 'px sans-serif';
                const tw = ctx.measureText(point.label).width;
                ctx.fillStyle = 'rgba(255,255,255,0.95)';
                ctx.beginPath();
                ctx.roundRect(
                    px - tw / 2 - s.labelPadding,
                    py - s.labelOffsetY,
                    tw + s.labelPadding * 2,
                    s.labelHeight,
                    6 * m
                );
                ctx.fill();
                ctx.strokeStyle = '#e2e8f0';
                ctx.lineWidth = 1 * m;
                ctx.stroke();

                ctx.fillStyle = '#1e293b';
                ctx.textAlign = 'center';
                ctx.textBaseline = 'middle';
                ctx.fillText(point.label, px, py - s.labelTextOffsetY);
            }
        }
    }

    function drawUserPaths(ctx, pathList, color) {
        for (let path of pathList) {
            const fromPoint = points.find(p => String(p.id) === String(path.fromId));
            const toPoint = points.find(p => String(p.id) === String(path.toId));
            if (!fromPoint || !toPoint) continue;

            const fx = parseFloat(fromPoint.x);
            const fy = parseFloat(fromPoint.y);
            const tx = parseFloat(toPoint.x);
            const ty = parseFloat(toPoint.y);

            const isSelected = (path === userSelectedPath);
            const lineColor = isSelected ? '#059669' : color;

            ctx.beginPath();
            ctx.moveTo(fx, fy);

            if (path.style === 'elbow' && path.controlPoints && path.controlPoints.length >= 2) {
                ctx.lineTo(path.controlPoints[0].x, path.controlPoints[0].y);
                ctx.lineTo(path.controlPoints[1].x, path.controlPoints[1].y);
                ctx.lineTo(tx, ty);
            } else if (path.style === 'bezier' && path.controlPoints && path.controlPoints.length >= 2) {
                ctx.bezierCurveTo(
                    path.controlPoints[0].x, path.controlPoints[0].y,
                    path.controlPoints[1].x, path.controlPoints[1].y,
                    tx, ty
                );
            } else {
                ctx.lineTo(tx, ty);
            }

            ctx.strokeStyle = color + '30';
            ctx.lineWidth = 12;
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
                ctx.bezierCurveTo(
                    path.controlPoints[0].x, path.controlPoints[0].y,
                    path.controlPoints[1].x, path.controlPoints[1].y,
                    tx, ty
                );
            } else {
                ctx.lineTo(tx, ty);
            }
            ctx.strokeStyle = lineColor;
            ctx.lineWidth = isSelected ? 6 : 4;
            ctx.lineCap = 'round';
            ctx.lineJoin = 'round';
            ctx.stroke();

            if (isSelected) {
                ctx.setLineDash([6, 4]);
                ctx.strokeStyle = 'rgba(239,68,68,0.5)';
                ctx.lineWidth = 10;
                ctx.stroke();
                ctx.setLineDash([]);

                ctx.fillStyle = '#ef4444';
                ctx.font = 'bold 14px sans-serif';
                ctx.textAlign = 'center';
                ctx.fillText('🗑️ Klik lagi untuk hapus', (fx + tx) / 2, (fy + ty) / 2 - 15);
            }

            ctx.beginPath();
            ctx.arc(fx, fy, 8, 0, Math.PI * 2);
            ctx.arc(tx, ty, 8, 0, Math.PI * 2);
            ctx.fillStyle = lineColor;
            ctx.fill();

            ctx.beginPath();
            ctx.arc(fx, fy, 4, 0, Math.PI * 2);
            ctx.arc(tx, ty, 4, 0, Math.PI * 2);
            ctx.fillStyle = '#fff';
            ctx.fill();
        }
    }

    function drawUserTempPath(ctx) {
        const startPoint = points.find(p => String(p.id) === String(tempPath.startId));
        if (!startPoint) return;

        const sx = parseFloat(startPoint.x);
        const sy = parseFloat(startPoint.y);
        const ex = tempPath.end.x;
        const ey = tempPath.end.y;

        ctx.beginPath();
        ctx.moveTo(sx, sy);
        ctx.lineTo(ex, ey);
        ctx.strokeStyle = '#94a3b8';
        ctx.lineWidth = 3;
        ctx.setLineDash([10, 5]);
        ctx.lineCap = 'round';
        ctx.stroke();
        ctx.setLineDash([]);

        ctx.beginPath();
        ctx.arc(ex, ey, 5, 0, Math.PI * 2);
        ctx.fillStyle = '#94a3b8';
        ctx.fill();
    }

    function getPointAt(pos) {
        const s = getPointSizes();
        for (let point of points) {
            const px = parseFloat(point.x);
            const py = parseFloat(point.y);
            const dist = Math.sqrt((pos.x - px) ** 2 + (pos.y - py) ** 2);
            if (dist <= s.hitRadius) return point;
        }
        return null;
    }

    function getUserPathAt(pos) {
        for (let path of userPaths) {
            const fromPoint = points.find(p => String(p.id) === String(path.fromId));
            const toPoint = points.find(p => String(p.id) === String(path.toId));
            if (!fromPoint || !toPoint) continue;

            const fx = parseFloat(fromPoint.x);
            const fy = parseFloat(fromPoint.y);
            const tx = parseFloat(toPoint.x);
            const ty = parseFloat(toPoint.y);

            const dist = pointToUserPathDistance(pos.x, pos.y, path, fx, fy, tx, ty);
            if (dist < 12) return path;
        }
        return null;
    }

    function pointToUserPathDistance(px, py, path, x1, y1, x2, y2) {
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

    function checkJawaban() {
        if (userPaths.length === 0) {
            showNotification('Anda belum menyambungkan jalur!', true);
            return;
        }

        let benar = 0;
        for (let correctPath of correctPaths) {
            const found = userPaths.some(up =>
                (String(up.fromId) == String(correctPath.titik_a_id) && String(up.toId) == String(correctPath.titik_b_id)) ||
                (String(up.fromId) == String(correctPath.titik_b_id) && String(up.toId) == String(correctPath.titik_a_id))
            );
            if (found) benar++;
        }

        const total = correctPaths.length;
        const skor = benar;
        const salah = userPaths.length - benar;

        const formData = new FormData();
        formData.append('id_soal', soalData.id);
        formData.append('jawaban', JSON.stringify(userPaths));

        const csrfName = document.querySelector('meta[name="csrf-token-name"]')?.content || '<?= csrf_token() ?>';
        const csrfHash = document.querySelector('meta[name="csrf-token"]')?.content || '<?= csrf_hash() ?>';
        formData.append(csrfName, csrfHash);

        fetch('/user/soal/simpan-jawaban', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    const persen = total > 0 ? Math.round((benar / total) * 100) : 0;
                    let message = '<div class="text-center">';
                    message += '<div class="text-5xl mb-4">' + (persen >= 80 ? '🎉' : persen >= 60 ? '👍' : '💪') + '</div>';
                    message += '<div class="text-2xl font-bold mb-2">' + benar + ' / ' + total + ' Benar</div>';
                    message += '<div class="text-lg text-slate-600 mb-4">Skor: ' + persen + '%</div>';
                    message += '<div class="flex justify-center gap-4 text-sm">';
                    message += '<span class="text-green-600"><i class="fas fa-check mr-1"></i>' + benar + ' Benar</span>';
                    message += '<span class="text-red-600"><i class="fas fa-times mr-1"></i>' + salah + ' Salah</span>';
                    message += '</div></div>';

                    // Redirect to dashboard after modal is closed
                    showModal('Hasil Pengerjaan', message, function() {
                        window.location.href = '/user/dashboard/' + soalData.id_kategori;
                    });
                }
            });
    }

    function resetJawaban() {
        userPaths = [];
        userSelectedPath = null;
        tempPath = null;
        dragState.isDragging = false;
        dragState.startPoint = null;
        updatePathCounter();
        redrawUser();
        showNotification('Jawaban direset!');
    }
</script>
<?= $this->endSection() ?>