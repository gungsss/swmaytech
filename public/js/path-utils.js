/**
 * Path Utilities - Auto-routing algorithms for quiz canvas
 * Shared by admin/soal_form.php and user/kerjakan.php
 */

const PathUtils = {
    // Configuration
    CLEARANCE_RADIUS: 45,
    BEZIER_STEPS: 80,

    /**
     * Get all points that would be hit by a straight line (excluding endpoints)
     * @param {number} x1 - Start X
     * @param {number} y1 - Start Y
     * @param {number} x2 - End X
     * @param {number} y2 - End Y
     * @param {Array} points - Array of point objects with x, y, id/tempId
     * @param {Array} excludeIds - IDs to exclude from collision check
     * @returns {Array} - Array of colliding points with distance info
     */
    getCollidingPointsOnLine: function(x1, y1, x2, y2, points, excludeIds) {
        const colliding = [];
        const dx = x2 - x1;
        const dy = y2 - y1;
        const lenSq = dx * dx + dy * dy;

        for (let point of points) {
            const pid = String(point.id || point.tempId);
            if (excludeIds && excludeIds.includes(pid)) continue;

            const px = parseFloat(point.x);
            const py = parseFloat(point.y);

            let t;
            if (lenSq === 0) {
                t = 0;
            } else {
                t = Math.max(0, Math.min(1, ((px - x1) * dx + (py - y1) * dy) / lenSq));
            }

            const projX = x1 + t * dx;
            const projY = y1 + t * dy;
            const dist = Math.sqrt((px - projX) ** 2 + (py - projY) ** 2);

            if (dist < this.CLEARANCE_RADIUS) {
                colliding.push({
                    point: point,
                    dist: dist,
                    projX: projX,
                    projY: projY,
                    t: t
                });
            }
        }
        // Sort by distance (closest first)
        colliding.sort((a, b) => a.dist - b.dist);
        return colliding;
    },

    /**
     * Check if an elbow path collides with any point
     * @param {number} x1, y1 - Start point
     * @param {Object} cp1, cp2 - Control points
     * @param {number} x2, y2 - End point
     * @param {Array} points - Array of point objects
     * @param {Array} excludeIds - IDs to exclude
     * @returns {boolean} - true if collides
     */
    elbowPathCollides: function(x1, y1, cp1, cp2, x2, y2, points, excludeIds) {
        const segments = [
            [x1, y1, cp1.x, cp1.y],
            [cp1.x, cp1.y, cp2.x, cp2.y],
            [cp2.x, cp2.y, x2, y2]
        ];
        for (let seg of segments) {
            const hits = this.getCollidingPointsOnLine(seg[0], seg[1], seg[2], seg[3], points, excludeIds);
            if (hits.length > 0) return true;
        }
        return false;
    },

    /**
     * Check if a bezier path collides with any point (sample-based)
     * @param {number} x1, y1, cp1, cp2, x2, y2 - Bezier parameters
     * @param {Array} points - Array of point objects
     * @param {Array} excludeIds - IDs to exclude
     * @returns {boolean} - true if collides
     */
    bezierPathCollides: function(x1, y1, cp1, cp2, x2, y2, points, excludeIds) {
        const steps = this.BEZIER_STEPS;
        for (let i = 0; i <= steps; i++) {
            const t = i / steps;
            const bx = (1 - t) * (1 - t) * (1 - t) * x1 + 3 * (1 - t) * (1 - t) * t * cp1.x + 3 * (1 - t) * t * t * cp2.x + t * t * t * x2;
            const by = (1 - t) * (1 - t) * (1 - t) * y1 + 3 * (1 - t) * (1 - t) * t * cp1.y + 3 * (1 - t) * t * t * cp2.y + t * t * t * y2;

            for (let point of points) {
                const pid = String(point.id || point.tempId);
                if (excludeIds && excludeIds.includes(pid)) continue;
                const px = parseFloat(point.x);
                const py = parseFloat(point.y);
                const dist = Math.sqrt((px - bx) ** 2 + (py - by) ** 2);
                if (dist < this.CLEARANCE_RADIUS) return true;
            }
        }
        return false;
    },

    /**
     * Generate standard elbow control points (no avoidance)
     * @param {number} fx, fy - From point
     * @param {number} tx, ty - To point
     * @returns {Array} - Array of 2 control points
     */
    generateStandardElbow: function(fx, fy, tx, ty) {
        const dx = tx - fx;
        const dy = ty - fy;
        if (Math.abs(dx) > Math.abs(dy)) {
            return [
                { x: fx + dx / 2, y: fy },
                { x: fx + dx / 2, y: ty }
            ];
        } else {
            return [
                { x: fx, y: fy + dy / 2 },
                { x: tx, y: fy + dy / 2 }
            ];
        }
    },

    /**
     * Find a detour path around blocking points using intermediate waypoints
     * @param {Object} fromPoint - From point object
     * @param {Object} toPoint - To point object
     * @param {Array} points - All points array
     * @param {Array} blockingPoints - Points to route around
     * @returns {Object|null} - Route object or null
     */
    findDetourPath: function(fromPoint, toPoint, points, blockingPoints) {
        const fx = parseFloat(fromPoint.x);
        const fy = parseFloat(fromPoint.y);
        const tx = parseFloat(toPoint.x);
        const ty = parseFloat(toPoint.y);
        const excludeIds = [String(fromPoint.id || fromPoint.tempId), String(toPoint.id || toPoint.tempId)];

        // Calculate bounding box of blocking points
        let minX = Infinity, maxX = -Infinity, minY = Infinity, maxY = -Infinity;
        if (blockingPoints && blockingPoints.length > 0) {
            for (let bp of blockingPoints) {
                const px = parseFloat(bp.x);
                const py = parseFloat(bp.y);
                minX = Math.min(minX, px);
                maxX = Math.max(maxX, px);
                minY = Math.min(minY, py);
                maxY = Math.max(maxY, py);
            }
        } else {
            // If no blocking points, use midpoint
            minX = Math.min(fx, tx);
            maxX = Math.max(fx, tx);
            minY = Math.min(fy, ty);
            maxY = Math.max(fy, ty);
        }

        // Add margin
        const margin = this.CLEARANCE_RADIUS * 1.5;
        minX -= margin;
        maxX += margin;
        minY -= margin;
        maxY += margin;

        // Try detour waypoints: go around the bounding box
        const detourWaypoints = [
            { x: (fx + tx) / 2, y: minY - this.CLEARANCE_RADIUS },
            { x: (fx + tx) / 2, y: maxY + this.CLEARANCE_RADIUS },
            { x: minX - this.CLEARANCE_RADIUS, y: (fy + ty) / 2 },
            { x: maxX + this.CLEARANCE_RADIUS, y: (fy + ty) / 2 },
        ];

        for (let waypoint of detourWaypoints) {
            const hits1 = this.getCollidingPointsOnLine(fx, fy, waypoint.x, waypoint.y, points, excludeIds);
            if (hits1.length > 0) continue;

            const hits2 = this.getCollidingPointsOnLine(waypoint.x, waypoint.y, tx, ty, points, excludeIds);
            if (hits2.length > 0) continue;

            return {
                style: 'bezier',
                controlPoints: [{ x: waypoint.x, y: fy }, { x: waypoint.x, y: ty }]
            };
        }

        return null;
    },

    /**
     * Generate auto-routing control points that avoid all points
     * This is the main auto-routing algorithm
     * @param {Object} fromPoint - From point object
     * @param {Object} toPoint - To point object
     * @param {string} preferredStyle - 'straight', 'elbow', or 'bezier'
     * @param {Array} points - All points array
     * @returns {Object} - Route object with style and controlPoints
     */
    generateAutoRoutingControlPoints: function(fromPoint, toPoint, preferredStyle, points) {
        const fx = parseFloat(fromPoint.x);
        const fy = parseFloat(fromPoint.y);
        const tx = parseFloat(toPoint.x);
        const ty = parseFloat(toPoint.y);
        const dx = tx - fx;
        const dy = ty - fy;
        const dist = Math.sqrt(dx * dx + dy * dy);
        const excludeIds = [String(fromPoint.id || fromPoint.tempId), String(toPoint.id || toPoint.tempId)];

        // First, check if straight line is clear
        const straightHits = this.getCollidingPointsOnLine(fx, fy, tx, ty, points, excludeIds);

        if (straightHits.length === 0) {
            if (preferredStyle === 'straight') {
                return { style: 'straight', controlPoints: null };
            }
        }

        // ===== Try elbow with avoidance =====
        if (preferredStyle === 'straight' || preferredStyle === 'elbow') {
            // Try standard elbow first
            let elbowCP = this.generateStandardElbow(fx, fy, tx, ty);
            if (!this.elbowPathCollides(fx, fy, elbowCP[0], elbowCP[1], tx, ty, points, excludeIds)) {
                return { style: 'elbow', controlPoints: elbowCP };
            }

            // Standard elbow collides, try adjusted elbow
            const midX = (fx + tx) / 2;
            const midY = (fy + ty) / 2;
            const isHorizontalPrimary = Math.abs(dx) > Math.abs(dy);

            const attempts = [
                { offsetX: 0, offsetY: -this.CLEARANCE_RADIUS * 2 },
                { offsetX: 0, offsetY: this.CLEARANCE_RADIUS * 2 },
                { offsetX: -this.CLEARANCE_RADIUS * 2, offsetY: 0 },
                { offsetX: this.CLEARANCE_RADIUS * 2, offsetY: 0 },
                { offsetX: -this.CLEARANCE_RADIUS * 3, offsetY: -this.CLEARANCE_RADIUS * 3 },
                { offsetX: this.CLEARANCE_RADIUS * 3, offsetY: this.CLEARANCE_RADIUS * 3 },
                { offsetX: -this.CLEARANCE_RADIUS * 3, offsetY: this.CLEARANCE_RADIUS * 3 },
                { offsetX: this.CLEARANCE_RADIUS * 3, offsetY: -this.CLEARANCE_RADIUS * 3 },
                { offsetX: -this.CLEARANCE_RADIUS * 4, offsetY: 0 },
                { offsetX: this.CLEARANCE_RADIUS * 4, offsetY: 0 },
                { offsetX: 0, offsetY: -this.CLEARANCE_RADIUS * 4 },
                { offsetX: 0, offsetY: this.CLEARANCE_RADIUS * 4 },
            ];

            for (let attempt of attempts) {
                let cp1, cp2;
                if (isHorizontalPrimary) {
                    cp1 = { x: midX + attempt.offsetX, y: fy };
                    cp2 = { x: midX + attempt.offsetX, y: ty + attempt.offsetY };
                } else {
                    cp1 = { x: fx + attempt.offsetX, y: midY + attempt.offsetY };
                    cp2 = { x: tx, y: midY + attempt.offsetY };
                }

                if (!this.elbowPathCollides(fx, fy, cp1, cp2, tx, ty, points, excludeIds)) {
                    return { style: 'elbow', controlPoints: [cp1, cp2] };
                }
            }

            // Try "double bend" elbow
            const doubleBendAttempts = [
                [{ x: fx, y: fy - this.CLEARANCE_RADIUS * 2.5 }, { x: tx, y: fy - this.CLEARANCE_RADIUS * 2.5 }],
                [{ x: fx, y: fy + this.CLEARANCE_RADIUS * 2.5 }, { x: tx, y: fy + this.CLEARANCE_RADIUS * 2.5 }],
                [{ x: fx - this.CLEARANCE_RADIUS * 2.5, y: fy }, { x: fx - this.CLEARANCE_RADIUS * 2.5, y: ty }],
                [{ x: fx + this.CLEARANCE_RADIUS * 2.5, y: fy }, { x: fx + this.CLEARANCE_RADIUS * 2.5, y: ty }],
                [{ x: fx, y: fy - this.CLEARANCE_RADIUS * 4 }, { x: tx, y: fy - this.CLEARANCE_RADIUS * 4 }],
                [{ x: fx, y: fy + this.CLEARANCE_RADIUS * 4 }, { x: tx, y: fy + this.CLEARANCE_RADIUS * 4 }],
            ];

            for (let bend of doubleBendAttempts) {
                if (!this.elbowPathCollides(fx, fy, bend[0], bend[1], tx, ty, points, excludeIds)) {
                    return { style: 'elbow', controlPoints: bend };
                }
            }
        }

        // ===== Try bezier with avoidance =====
        const offset = Math.min(dist * 0.5, 150);
        const bigOffset = Math.min(dist * 0.7, 200);

        // Standard bezier
        let cp1 = { x: fx + (dx > 0 ? offset : -offset), y: fy };
        let cp2 = { x: tx - (dx > 0 ? offset : -offset), y: ty };

        if (!this.bezierPathCollides(fx, fy, cp1, cp2, tx, ty, points, excludeIds)) {
            return { style: 'bezier', controlPoints: [cp1, cp2] };
        }

        // Try many different bezier curves
        const bezierAttempts = [
            [{ x: fx + dx * 0.2, y: fy - this.CLEARANCE_RADIUS * 3 }, { x: tx - dx * 0.2, y: ty - this.CLEARANCE_RADIUS * 3 }],
            [{ x: fx + dx * 0.2, y: fy - this.CLEARANCE_RADIUS * 4 }, { x: tx - dx * 0.2, y: ty - this.CLEARANCE_RADIUS * 4 }],
            [{ x: fx + dx * 0.2, y: fy - this.CLEARANCE_RADIUS * 5 }, { x: tx - dx * 0.2, y: ty - this.CLEARANCE_RADIUS * 5 }],
            [{ x: fx + dx * 0.2, y: fy + this.CLEARANCE_RADIUS * 3 }, { x: tx - dx * 0.2, y: ty + this.CLEARANCE_RADIUS * 3 }],
            [{ x: fx + dx * 0.2, y: fy + this.CLEARANCE_RADIUS * 4 }, { x: tx - dx * 0.2, y: ty + this.CLEARANCE_RADIUS * 4 }],
            [{ x: fx + dx * 0.2, y: fy + this.CLEARANCE_RADIUS * 5 }, { x: tx - dx * 0.2, y: ty + this.CLEARANCE_RADIUS * 5 }],
            [{ x: fx - this.CLEARANCE_RADIUS * 3, y: fy + dy * 0.2 }, { x: tx - this.CLEARANCE_RADIUS * 3, y: ty - dy * 0.2 }],
            [{ x: fx - this.CLEARANCE_RADIUS * 4, y: fy + dy * 0.2 }, { x: tx - this.CLEARANCE_RADIUS * 4, y: ty - dy * 0.2 }],
            [{ x: fx + this.CLEARANCE_RADIUS * 3, y: fy + dy * 0.2 }, { x: tx + this.CLEARANCE_RADIUS * 3, y: ty - dy * 0.2 }],
            [{ x: fx + this.CLEARANCE_RADIUS * 4, y: fy + dy * 0.2 }, { x: tx + this.CLEARANCE_RADIUS * 4, y: ty - dy * 0.2 }],
            [{ x: fx + (dx > 0 ? bigOffset : -bigOffset), y: fy }, { x: tx - (dx > 0 ? bigOffset : -bigOffset), y: ty }],
            [{ x: fx + dx * 0.3, y: fy - this.CLEARANCE_RADIUS * 2 }, { x: tx - dx * 0.3, y: ty - this.CLEARANCE_RADIUS * 2 }],
            [{ x: fx + dx * 0.3, y: fy + this.CLEARANCE_RADIUS * 2 }, { x: tx - dx * 0.3, y: ty + this.CLEARANCE_RADIUS * 2 }],
            [{ x: fx + (dx > 0 ? offset * 1.5 : -offset * 1.5), y: fy - this.CLEARANCE_RADIUS * 3 }, { x: tx - (dx > 0 ? offset * 1.5 : -offset * 1.5), y: ty + this.CLEARANCE_RADIUS * 3 }],
            [{ x: fx + (dx > 0 ? offset * 1.5 : -offset * 1.5), y: fy + this.CLEARANCE_RADIUS * 3 }, { x: tx - (dx > 0 ? offset * 1.5 : -offset * 1.5), y: ty - this.CLEARANCE_RADIUS * 3 }],
            [{ x: fx + dx * 0.1, y: fy - this.CLEARANCE_RADIUS * 6 }, { x: tx - dx * 0.1, y: ty - this.CLEARANCE_RADIUS * 6 }],
            [{ x: fx + dx * 0.1, y: fy + this.CLEARANCE_RADIUS * 6 }, { x: tx - dx * 0.1, y: ty + this.CLEARANCE_RADIUS * 6 }],
            [{ x: fx + dx * 0.5, y: fy - this.CLEARANCE_RADIUS * 3 }, { x: tx - dx * 0.5, y: ty - this.CLEARANCE_RADIUS * 3 }],
            [{ x: fx + dx * 0.5, y: fy + this.CLEARANCE_RADIUS * 3 }, { x: tx - dx * 0.5, y: ty + this.CLEARANCE_RADIUS * 3 }],
            [{ x: fx - this.CLEARANCE_RADIUS * 3, y: fy + dy * 0.5 }, { x: tx - this.CLEARANCE_RADIUS * 3, y: ty - dy * 0.5 }],
            [{ x: fx + this.CLEARANCE_RADIUS * 3, y: fy + dy * 0.5 }, { x: tx + this.CLEARANCE_RADIUS * 3, y: ty - dy * 0.5 }],
        ];

        for (let attempt of bezierAttempts) {
            if (!this.bezierPathCollides(fx, fy, attempt[0], attempt[1], tx, ty, points, excludeIds)) {
                return { style: 'bezier', controlPoints: attempt };
            }
        }

        // Try detour path
        const detour = this.findDetourPath(fromPoint, toPoint, points, straightHits.map(h => h.point));
        if (detour) {
            return detour;
        }

        // Last resort: try extreme bezier
        const farAway = Math.max(dist * 1.5, this.CLEARANCE_RADIUS * 8);
        const perpX = -dy / dist * farAway;
        const perpY = dx / dist * farAway;
        const midX = (fx + tx) / 2;
        const midY = (fy + ty) / 2;

        const extremeAttempts = [
            [{ x: midX + perpX * 0.5, y: midY + perpY * 0.5 }, { x: midX + perpX * 0.5, y: midY + perpY * 0.5 }],
            [{ x: midX - perpX * 0.5, y: midY - perpY * 0.5 }, { x: midX - perpX * 0.5, y: midY - perpY * 0.5 }],
            [{ x: fx + perpX, y: fy + perpY }, { x: tx + perpX, y: ty + perpY }],
            [{ x: fx - perpX, y: fy - perpY }, { x: tx - perpX, y: ty - perpY }],
        ];

        for (let attempt of extremeAttempts) {
            if (!this.bezierPathCollides(fx, fy, attempt[0], attempt[1], tx, ty, points, excludeIds)) {
                return { style: 'bezier', controlPoints: attempt };
            }
        }

        // If all else fails, return elbow (which bends around obstacles)
        return { style: 'elbow', controlPoints: this.generateStandardElbow(fx, fy, tx, ty) };
    },

    /**
     * Generate default control points for a style (for preview temp path)
     * @param {Object} fromPoint - From point
     * @param {Object} toPoint - To point
     * @param {string} style - 'straight', 'elbow', or 'bezier'
     * @returns {Array|null} - Control points or null for straight
     */
    generateDefaultControlPoints: function(fromPoint, toPoint, style) {
        const fx = parseFloat(fromPoint.x);
        const fy = parseFloat(fromPoint.y);
        const tx = parseFloat(toPoint.x);
        const ty = parseFloat(toPoint.y);
        const dx = tx - fx;
        const dy = ty - fy;
        const dist = Math.sqrt(dx * dx + dy * dy);

        if (style === 'straight') return null;
        else if (style === 'elbow') {
            if (Math.abs(dx) > Math.abs(dy)) {
                return [
                    { x: fx + dx / 2, y: fy },
                    { x: fx + dx / 2, y: ty }
                ];
            } else {
                return [
                    { x: fx, y: fy + dy / 2 },
                    { x: tx, y: fy + dy / 2 }
                ];
            }
        } else {
            const offset = Math.min(dist * 0.5, 120);
            return [
                { x: fx + (dx > 0 ? offset : -offset), y: fy },
                { x: tx - (dx > 0 ? offset : -offset), y: ty }
            ];
        }
    }
};

// Export for use
if (typeof module !== 'undefined' && module.exports) {
    module.exports = PathUtils;
}