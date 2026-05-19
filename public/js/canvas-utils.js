/**
 * Canvas Utilities - Drawing functions for quiz canvas
 * Shared by admin/soal_form.php and user/kerjaan.php
 */

const CanvasUtils = {
    // Configuration
    CONFIG: {
        POINT_SIZES: {
            outer: 24,
            middle: 16,
            inner: 6,
            hitRadius: 32
        },
        PATH_HIT_RADIUS: 12,
        BEZIER_SAMPLE_STEPS: 80,
        CONTROL_POINT_SIZE: 14,
        CONTROL_POINT_INNER: 9,
        PATH_GLOW_WIDTH: 12,
        PATH_MAIN_WIDTH: 4,
        PATH_SELECTED_WIDTH: 6,
        PATH_END_DOT_SIZE: 8,
        PATH_END_DOT_INNER: 4
    },

    /**
     * Draw all points on canvas
     * @param {CanvasRenderingContext2D} ctx - Canvas context
     * @param {Array} points - Array of point objects with x, y, label
     */
    drawPoints: function(ctx, points) {
        const s = this.CONFIG.POINT_SIZES;

        for (let point of points) {
            const px = parseFloat(point.x);
            const py = parseFloat(point.y);

            // Outer glow
            ctx.beginPath();
            ctx.arc(px, py, s.outer, 0, Math.PI * 2);
            ctx.fillStyle = 'rgba(239, 68, 68, 0.15)';
            ctx.fill();

            // Middle ring
            ctx.beginPath();
            ctx.arc(px, py, s.middle, 0, Math.PI * 2);
            ctx.fillStyle = '#ef4444';
            ctx.fill();
            ctx.strokeStyle = '#fff';
            ctx.lineWidth = 3;
            ctx.stroke();

            // Inner dot
            ctx.beginPath();
            ctx.arc(px, py, s.inner, 0, Math.PI * 2);
            ctx.fillStyle = '#fff';
            ctx.fill();

            // Label
            if (point.label) {
                ctx.font = 'bold 14px sans-serif';
                const textWidth = ctx.measureText(point.label).width;
                ctx.fillStyle = 'rgba(255,255,255,0.95)';
                ctx.beginPath();
                ctx.roundRect(px - textWidth / 2 - 6, py - 42, textWidth + 12, 22, 6);
                ctx.fill();
                ctx.strokeStyle = '#e2e8f0';
                ctx.lineWidth = 1;
                ctx.stroke();
                ctx.fillStyle = '#1e293b';
                ctx.textAlign = 'center';
                ctx.textBaseline = 'middle';
                ctx.fillText(point.label, px, py - 31);
            }
        }
    },

    /**
     * Draw a single path on canvas
     * @param {CanvasRenderingContext2D} ctx - Canvas context
     * @param {Object} path - Path object with style, controlPoints, fromId/toId or fromTempId/toTempId
     * @param {Object} fromPoint - From point object
     * @param {Object} toPoint - To point object
     * @param {string} color - Path color
     * @param {boolean} isSelected - Whether path is selected
     * @param {boolean} isCorrectPath - Whether this is a correct/answer path (for alpha)
     */
    drawPath: function(ctx, path, fromPoint, toPoint, color, isSelected, isCorrectPath) {
        if (!fromPoint || !toPoint) return;

        const fx = parseFloat(fromPoint.x);
        const fy = parseFloat(fromPoint.y);
        const tx = parseFloat(toPoint.x);
        const ty = parseFloat(toPoint.y);

        const style = path.style || 'straight';
        let controlPoints = null;

        // Handle both admin (tempId) and user (id) formats
        if (path.control_points) {
            try {
                controlPoints = typeof path.control_points === 'string'
                    ? JSON.parse(path.control_points)
                    : path.control_points;
            } catch (e) {
                controlPoints = null;
            }
        } else if (path.controlPoints) {
            controlPoints = path.controlPoints;
        }

        const lineColor = isSelected ? '#059669' : color;
        const glowColor = color + '30';
        const alpha = isCorrectPath ? 0.2 : 1;

        // Helper to draw path segments
        const drawPathSegments = function(ctx, style, controlPoints) {
            if (style === 'elbow' && controlPoints && controlPoints.length >= 2) {
                ctx.lineTo(controlPoints[0].x, controlPoints[0].y);
                ctx.lineTo(controlPoints[1].x, controlPoints[1].y);
                ctx.lineTo(tx, ty);
            } else if (style === 'bezier' && controlPoints && controlPoints.length >= 2) {
                ctx.bezierCurveTo(
                    controlPoints[0].x, controlPoints[0].y,
                    controlPoints[1].x, controlPoints[1].y,
                    tx, ty
                );
            } else {
                ctx.lineTo(tx, ty);
            }
        };

        ctx.globalAlpha = alpha;

        // Glow layer
        ctx.beginPath();
        ctx.moveTo(fx, fy);
        drawPathSegments(ctx, style, controlPoints);
        ctx.strokeStyle = glowColor;
        ctx.lineWidth = isSelected ? 16 : this.CONFIG.PATH_GLOW_WIDTH;
        ctx.lineCap = 'round';
        ctx.lineJoin = 'round';
        ctx.stroke();

        // Main path
        ctx.beginPath();
        ctx.moveTo(fx, fy);
        drawPathSegments(ctx, style, controlPoints);
        ctx.strokeStyle = lineColor;
        ctx.lineWidth = isSelected ? this.CONFIG.PATH_SELECTED_WIDTH : this.CONFIG.PATH_MAIN_WIDTH;
        ctx.lineCap = 'round';
        ctx.lineJoin = 'round';
        ctx.stroke();

        // Selected dashed overlay
        if (isSelected) {
            ctx.setLineDash([6, 4]);
            ctx.strokeStyle = 'rgba(239,68,68,0.5)';
            ctx.lineWidth = 10;
            ctx.stroke();
            ctx.setLineDash([]);
        }

        // End dots
        ctx.beginPath();
        ctx.arc(fx, fy, this.CONFIG.PATH_END_DOT_SIZE, 0, Math.PI * 2);
        ctx.arc(tx, ty, this.CONFIG.PATH_END_DOT_SIZE, 0, Math.PI * 2);
        ctx.fillStyle = lineColor;
        ctx.fill();

        ctx.beginPath();
        ctx.arc(fx, fy, this.CONFIG.PATH_END_DOT_INNER, 0, Math.PI * 2);
        ctx.arc(tx, ty, this.CONFIG.PATH_END_DOT_INNER, 0, Math.PI * 2);
        ctx.fillStyle = '#fff';
        ctx.fill();

        ctx.globalAlpha = 1;
    },

    /**
     * Draw multiple paths on canvas
     * @param {CanvasRenderingContext2D} ctx - Canvas context
     * @param {Array} pathList - Array of path objects
     * @param {Array} points - Array of point objects (for lookup)
     * @param {string} color - Base color
     * @param {Object} selectedPath - Currently selected path (optional)
     * @param {number} alpha - Opacity (0-1)
     */
    drawPaths: function(ctx, pathList, points, color, selectedPath, alpha = 1) {
        ctx.globalAlpha = alpha;

        for (let path of pathList) {
            // Find points - supports both id and tempId formats
            let fromPoint, toPoint;
            if (path.titik_a_id !== undefined) {
                // Correct path format from database
                fromPoint = points.find(p => String(p.id || p.tempId) === String(path.titik_a_id));
                toPoint = points.find(p => String(p.id || p.tempId) === String(path.titik_b_id));
            } else {
                // User path format
                fromPoint = points.find(p => String(p.id || p.tempId) === String(path.fromId || path.fromTempId));
                toPoint = points.find(p => String(p.id || p.tempId) === String(path.toId || path.toTempId));
            }

            const isSelected = selectedPath && path === selectedPath;
            this.drawPath(ctx, path, fromPoint, toPoint, color, isSelected, false);
        }

        ctx.globalAlpha = 1;
    },

    /**
     * Draw control point handles for selected path
     * @param {CanvasRenderingContext2D} ctx - Canvas context
     * @param {Object} path - Selected path with controlPoints
     * @param {number} canvasHeight - Canvas height for text positioning
     */
    drawControlPoints: function(ctx, path, canvasHeight) {
        if (!path || !path.controlPoints || path.style === 'straight') return;

        for (let i = 0; i < path.controlPoints.length; i++) {
            const cp = path.controlPoints[i];

            // Outer glow
            ctx.beginPath();
            ctx.arc(cp.x, cp.y, this.CONFIG.CONTROL_POINT_SIZE, 0, Math.PI * 2);
            ctx.fillStyle = 'rgba(16, 185, 129, 0.2)';
            ctx.fill();

            // Inner circle
            ctx.beginPath();
            ctx.arc(cp.x, cp.y, this.CONFIG.CONTROL_POINT_INNER, 0, Math.PI * 2);
            ctx.fillStyle = '#10b981';
            ctx.fill();
            ctx.strokeStyle = '#fff';
            ctx.lineWidth = 2;
            ctx.stroke();

            // Label
            ctx.fillStyle = '#10b981';
            ctx.font = 'bold 11px sans-serif';
            ctx.textAlign = 'center';
            ctx.fillText('CP' + (i + 1), cp.x, cp.y - 16);
        }

        // Helper text
        ctx.fillStyle = '#3b82f6';
        ctx.font = 'bold 12px sans-serif';
        ctx.textAlign = 'left';
        ctx.fillText('Drag titik hijau untuk atur bentuk garis', 10, canvasHeight - 10);
    },

    /**
     * Draw a temporary path being created
     * @param {CanvasRenderingContext2D} ctx - Canvas context
     * @param {Object} startPoint - Start point object
     * @param {Object} endPos - End position {x, y}
     * @param {string} style - Path style
     */
    drawTempPath: function(ctx, startPoint, endPos, style) {
        if (!startPoint) return;

        const sx = parseFloat(startPoint.x);
        const sy = parseFloat(startPoint.y);
        const ex = endPos.x;
        const ey = endPos.y;

        ctx.beginPath();
        ctx.moveTo(sx, sy);

        if (style === 'straight') {
            ctx.lineTo(ex, ey);
        } else if (style === 'elbow') {
            const dx = ex - sx;
            const dy = ey - sy;
            if (Math.abs(dx) > Math.abs(dy)) {
                ctx.lineTo(sx + dx / 2, sy);
                ctx.lineTo(sx + dx / 2, ey);
            } else {
                ctx.lineTo(sx, sy + dy / 2);
                ctx.lineTo(ex, sy + dy / 2);
            }
            ctx.lineTo(ex, ey);
        } else {
            const dx = ex - sx;
            const dy = ey - sy;
            const dist = Math.sqrt(dx * dx + dy * dy);
            const offset = Math.min(dist * 0.5, 120);
            ctx.bezierCurveTo(sx + (dx > 0 ? offset : -offset), sy, ex - (dx > 0 ? offset : -offset), ey, ex, ey);
        }

        ctx.strokeStyle = '#3b82f6';
        ctx.lineWidth = 3;
        ctx.setLineDash([10, 5]);
        ctx.lineCap = 'round';
        ctx.lineJoin = 'round';
        ctx.stroke();
        ctx.setLineDash([]);

        // End dot
        ctx.beginPath();
        ctx.arc(ex, ey, 5, 0, Math.PI * 2);
        ctx.fillStyle = '#3b82f6';
        ctx.fill();
    },

    /**
     * Get point at canvas position
     * @param {Object} pos - Canvas position {x, y}
     * @param {Array} points - Array of point objects
     * @returns {Object|null} - Point object or null
     */
    getPointAt: function(pos, points) {
        const hitRadius = this.CONFIG.POINT_SIZES.hitRadius;
        for (let point of points) {
            const px = parseFloat(point.x);
            const py = parseFloat(point.y);
            const dist = Math.sqrt((pos.x - px) ** 2 + (pos.y - py) ** 2);
            if (dist <= hitRadius) return point;
        }
        return null;
    },

    /**
     * Get path at canvas position
     * @param {Object} pos - Canvas position {x, y}
     * @param {Array} paths - Array of path objects
     * @param {Array} points - Array of point objects
     * @returns {Object|null} - Path object or null
     */
    getPathAt: function(pos, paths, points) {
        const hitRadius = this.CONFIG.PATH_HIT_RADIUS;
        for (let path of paths) {
            // Find points
            let fromPoint, toPoint;
            if (path.titik_a_id !== undefined) {
                fromPoint = points.find(p => String(p.id || p.tempId) === String(path.titik_a_id));
                toPoint = points.find(p => String(p.id || p.tempId) === String(path.titik_b_id));
            } else {
                fromPoint = points.find(p => String(p.id || p.tempId) === String(path.fromId || path.fromTempId));
                toPoint = points.find(p => String(p.id || p.tempId) === String(path.toId || path.toTempId));
            }

            if (!fromPoint || !toPoint) continue;

            const fx = parseFloat(fromPoint.x);
            const fy = parseFloat(fromPoint.y);
            const tx = parseFloat(toPoint.x);
            const ty = parseFloat(toPoint.y);

            const dist = this.pointToPathDistance(pos.x, pos.y, path, fx, fy, tx, ty);
            if (dist < hitRadius) return path;
        }
        return null;
    },

    /**
     * Calculate distance from point to path
     * @param {number} px, py - Point position
     * @param {Object} path - Path object
     * @param {number} x1, y1 - From point
     * @param {number} x2, y2 - To point
     * @returns {number} - Distance
     */
    pointToPathDistance: function(px, py, path, x1, y1, x2, y2) {
        const style = path.style || 'straight';
        const controlPoints = path.controlPoints || null;

        if (style === 'straight' || !controlPoints) {
            const dx = x2 - x1;
            const dy = y2 - y1;
            const len = Math.sqrt(dx * dx + dy * dy);
            if (len === 0) return Math.sqrt((px - x1) ** 2 + (py - y1) ** 2);
            const t = Math.max(0, Math.min(1, ((px - x1) * dx + (py - y1) * dy) / (len * len)));
            const projX = x1 + t * dx;
            const projY = y1 + t * dy;
            return Math.sqrt((px - projX) ** 2 + (py - projY) ** 2);
        } else if (style === 'elbow') {
            let minDist = Infinity;
            const pts = [
                { x: x1, y: y1 },
                controlPoints[0],
                controlPoints[1],
                { x: x2, y: y2 }
            ];
            for (let i = 0; i < pts.length - 1; i++) {
                const dx = pts[i + 1].x - pts[i].x;
                const dy = pts[i + 1].y - pts[i].y;
                const len = Math.sqrt(dx * dx + dy * dy);
                if (len === 0) continue;
                const t = Math.max(0, Math.min(1, ((px - pts[i].x) * dx + (py - pts[i].y) * dy) / (len * len)));
                const projX = pts[i].x + t * dx;
                const projY = pts[i].y + t * dy;
                const d = Math.sqrt((px - projX) ** 2 + (py - projY) ** 2);
                if (d < minDist) minDist = d;
            }
            return minDist;
        } else {
            // Bezier - sample points along curve
            let minDist = Infinity;
            const cp1 = controlPoints[0];
            const cp2 = controlPoints[1];
            const steps = this.CONFIG.BEZIER_SAMPLE_STEPS;
            for (let i = 0; i <= steps; i++) {
                const t = i / steps;
                const bx = (1 - t) * (1 - t) * (1 - t) * x1 + 3 * (1 - t) * (1 - t) * t * cp1.x + 3 * (1 - t) * t * t * cp2.x + t * t * t * x2;
                const by = (1 - t) * (1 - t) * (1 - t) * y1 + 3 * (1 - t) * (1 - t) * t * cp1.y + 3 * (1 - t) * t * t * cp2.y + t * t * t * y2;
                const d = Math.sqrt((px - bx) ** 2 + (py - by) ** 2);
                if (d < minDist) minDist = d;
            }
            return minDist;
        }
    },

    /**
     * Get canvas-relative position from mouse event
     * @param {MouseEvent} e - Mouse event
     * @param {HTMLCanvasElement} canvas - Canvas element
     * @returns {Object} - Canvas position {x, y}
     */
    getCanvasPos: function(e, canvas) {
        const rect = canvas.getBoundingClientRect();
        const scaleX = canvas.width / rect.width;
        const scaleY = canvas.height / rect.height;
        return {
            x: (e.clientX - rect.left) * scaleX,
            y: (e.clientY - rect.top) * scaleY
        };
    },

    /**
     * Get canvas-relative position from touch event
     * @param {Touch} touch - Touch object
     * @param {HTMLCanvasElement} canvas - Canvas element
     * @returns {Object} - Canvas position {x, y}
     */
    getTouchPos: function(touch, canvas) {
        const rect = canvas.getBoundingClientRect();
        const scaleX = canvas.width / rect.width;
        const scaleY = canvas.height / rect.height;
        return {
            x: (touch.clientX - rect.left) * scaleX,
            y: (touch.clientY - rect.top) * scaleY
        };
    }
};

// Export for use
if (typeof module !== 'undefined' && module.exports) {
    module.exports = CanvasUtils;
}