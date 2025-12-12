<?php

/**
 * Grade Calculator Component
 * Interactive grade calculator with GPA prediction
 * Verdant SMS v3.0
 */

if (!isset($_SESSION['user_id'])) return;

$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'] ?? 'student';
?>

<!-- Grade Calculator Styles -->
<style>
    .grade-calculator {
        background: rgba(10, 10, 10, 0.95);
        border: 1px solid rgba(0, 255, 136, 0.3);
        border-radius: 12px;
        padding: 20px;
        margin: 20px 0;
    }

    .calc-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }

    .calc-title {
        color: #00ff88;
        font-size: 1.3rem;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .calc-mode-toggle {
        display: flex;
        background: rgba(0, 0, 0, 0.5);
        border-radius: 20px;
        padding: 4px;
    }

    .calc-mode-btn {
        background: transparent;
        border: none;
        color: #888;
        padding: 8px 16px;
        border-radius: 16px;
        cursor: pointer;
        transition: all 0.3s ease;
        font-size: 0.85rem;
    }

    .calc-mode-btn.active {
        background: rgba(0, 255, 136, 0.2);
        color: #00ff88;
    }

    .calc-body {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 20px;
    }

    .calc-inputs {
        display: flex;
        flex-direction: column;
        gap: 15px;
    }

    .grade-row {
        display: grid;
        grid-template-columns: 2fr 1fr 1fr auto;
        gap: 10px;
        align-items: center;
        background: rgba(20, 20, 20, 0.9);
        padding: 12px;
        border-radius: 8px;
        border: 1px solid rgba(0, 255, 136, 0.1);
    }

    .grade-row input,
    .grade-row select {
        background: rgba(0, 0, 0, 0.5);
        border: 1px solid rgba(0, 255, 136, 0.3);
        border-radius: 6px;
        padding: 10px 12px;
        color: #fff;
        font-size: 0.95rem;
    }

    .grade-row input:focus,
    .grade-row select:focus {
        outline: none;
        border-color: #00ff88;
    }

    .grade-row input::placeholder {
        color: #666;
    }

    .remove-row-btn {
        background: rgba(255, 107, 107, 0.2);
        border: none;
        color: #ff6b6b;
        width: 36px;
        height: 36px;
        border-radius: 50%;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .remove-row-btn:hover {
        background: rgba(255, 107, 107, 0.4);
    }

    .add-row-btn {
        background: rgba(0, 255, 136, 0.1);
        border: 1px dashed rgba(0, 255, 136, 0.3);
        color: #00ff88;
        padding: 12px;
        border-radius: 8px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        transition: all 0.3s ease;
    }

    .add-row-btn:hover {
        background: rgba(0, 255, 136, 0.2);
        border-style: solid;
    }

    .calc-results {
        background: rgba(20, 20, 20, 0.9);
        border: 1px solid rgba(0, 255, 136, 0.2);
        border-radius: 12px;
        padding: 20px;
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    .gpa-display {
        position: relative;
        width: 150px;
        height: 150px;
        margin-bottom: 20px;
    }

    .gpa-circle {
        width: 100%;
        height: 100%;
        position: relative;
    }

    .gpa-circle svg {
        transform: rotate(-90deg);
        width: 100%;
        height: 100%;
    }

    .gpa-circle-bg {
        fill: none;
        stroke: rgba(0, 255, 136, 0.1);
        stroke-width: 10;
    }

    .gpa-circle-progress {
        fill: none;
        stroke: #00ff88;
        stroke-width: 10;
        stroke-linecap: round;
        stroke-dasharray: 408;
        stroke-dashoffset: 408;
        transition: stroke-dashoffset 1s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .gpa-value {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        text-align: center;
    }

    .gpa-number {
        font-size: 2.5rem;
        font-weight: 700;
        color: #fff;
    }

    .gpa-max {
        font-size: 0.85rem;
        color: #888;
    }

    .gpa-grade {
        font-size: 1.5rem;
        font-weight: 700;
        margin: 10px 0;
        padding: 8px 20px;
        border-radius: 8px;
        text-align: center;
    }

    .gpa-grade.a {
        background: rgba(0, 255, 136, 0.2);
        color: #00ff88;
    }

    .gpa-grade.b {
        background: rgba(0, 212, 255, 0.2);
        color: #00d4ff;
    }

    .gpa-grade.c {
        background: rgba(255, 204, 0, 0.2);
        color: #ffcc00;
    }

    .gpa-grade.d {
        background: rgba(255, 136, 0, 0.2);
        color: #ff8800;
    }

    .gpa-grade.f {
        background: rgba(255, 107, 107, 0.2);
        color: #ff6b6b;
    }

    .results-breakdown {
        width: 100%;
        margin-top: 15px;
        padding-top: 15px;
        border-top: 1px solid rgba(255, 255, 255, 0.1);
    }

    .breakdown-row {
        display: flex;
        justify-content: space-between;
        padding: 8px 0;
        color: #888;
        font-size: 0.9rem;
    }

    .breakdown-row span:last-child {
        color: #fff;
        font-weight: 600;
    }

    /* What-If Mode */
    .whatif-container {
        margin-top: 20px;
        padding: 15px;
        background: rgba(0, 212, 255, 0.1);
        border: 1px solid rgba(0, 212, 255, 0.3);
        border-radius: 8px;
    }

    .whatif-title {
        color: #00d4ff;
        font-size: 1rem;
        font-weight: 600;
        margin-bottom: 10px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .whatif-input-row {
        display: flex;
        gap: 10px;
        align-items: center;
    }

    .whatif-input-row input,
    .whatif-input-row select {
        flex: 1;
        background: rgba(0, 0, 0, 0.5);
        border: 1px solid rgba(0, 212, 255, 0.3);
        border-radius: 6px;
        padding: 10px;
        color: #fff;
    }

    .whatif-result {
        margin-top: 10px;
        padding: 10px;
        background: rgba(0, 0, 0, 0.3);
        border-radius: 6px;
        color: #00d4ff;
        text-align: center;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .calc-body {
            grid-template-columns: 1fr;
        }

        .grade-row {
            grid-template-columns: 1fr 1fr;
            gap: 8px;
        }

        .grade-row input:first-child {
            grid-column: span 2;
        }
    }
</style>

<!-- Grade Calculator HTML -->
<div class="grade-calculator" id="gradeCalculator">
    <div class="calc-header">
        <h3 class="calc-title">
            <i class="fas fa-calculator"></i>
            Grade Calculator
        </h3>
        <div class="calc-mode-toggle">
            <button class="calc-mode-btn active" data-mode="gpa">GPA</button>
            <button class="calc-mode-btn" data-mode="weighted">Weighted</button>
            <button class="calc-mode-btn" data-mode="whatif">What-If</button>
        </div>
    </div>

    <div class="calc-body">
        <div class="calc-inputs">
            <div class="grade-row">
                <input type="text" placeholder="Subject/Course Name">
                <input type="number" placeholder="Score" min="0" max="100" class="grade-score">
                <select class="credit-hours">
                    <option value="3">3 Credits</option>
                    <option value="4">4 Credits</option>
                    <option value="2">2 Credits</option>
                    <option value="1">1 Credit</option>
                </select>
                <button class="remove-row-btn" onclick="GradeCalc.removeRow(this)">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="grade-row">
                <input type="text" placeholder="Subject/Course Name">
                <input type="number" placeholder="Score" min="0" max="100" class="grade-score">
                <select class="credit-hours">
                    <option value="3">3 Credits</option>
                    <option value="4">4 Credits</option>
                    <option value="2">2 Credits</option>
                    <option value="1">1 Credit</option>
                </select>
                <button class="remove-row-btn" onclick="GradeCalc.removeRow(this)">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="grade-row">
                <input type="text" placeholder="Subject/Course Name">
                <input type="number" placeholder="Score" min="0" max="100" class="grade-score">
                <select class="credit-hours">
                    <option value="3">3 Credits</option>
                    <option value="4">4 Credits</option>
                    <option value="2">2 Credits</option>
                    <option value="1">1 Credit</option>
                </select>
                <button class="remove-row-btn" onclick="GradeCalc.removeRow(this)">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <button class="add-row-btn" onclick="GradeCalc.addRow()">
                <i class="fas fa-plus"></i>
                Add Course
            </button>

            <div class="whatif-container" id="whatifContainer" style="display: none;">
                <div class="whatif-title">
                    <i class="fas fa-lightbulb"></i>
                    What If Scenario
                </div>
                <div class="whatif-input-row">
                    <input type="text" id="whatifCourse" placeholder="Future Course">
                    <input type="number" id="whatifScore" placeholder="Expected %" min="0" max="100">
                    <select id="whatifCredits">
                        <option value="3">3 Credits</option>
                        <option value="4">4 Credits</option>
                        <option value="2">2 Credits</option>
                    </select>
                </div>
                <div class="whatif-result" id="whatifResult">
                    Enter a hypothetical grade to see projected GPA
                </div>
            </div>
        </div>

        <div class="calc-results">
            <div class="gpa-display">
                <div class="gpa-circle">
                    <svg viewBox="0 0 140 140">
                        <circle class="gpa-circle-bg" cx="70" cy="70" r="65"></circle>
                        <circle class="gpa-circle-progress" id="gpaProgress" cx="70" cy="70" r="65"></circle>
                    </svg>
                    <div class="gpa-value">
                        <div class="gpa-number" id="gpaNumber">0.00</div>
                        <div class="gpa-max">/ 4.00</div>
                    </div>
                </div>
            </div>

            <div class="gpa-grade a" id="gpaGrade">-</div>

            <div class="results-breakdown">
                <div class="breakdown-row">
                    <span>Total Credits</span>
                    <span id="totalCredits">0</span>
                </div>
                <div class="breakdown-row">
                    <span>Quality Points</span>
                    <span id="qualityPoints">0</span>
                </div>
                <div class="breakdown-row">
                    <span>Average Score</span>
                    <span id="avgScore">0%</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Grade Calculator JavaScript -->
<script>
    const GradeCalc = {
        init() {
            this.setupListeners();
            this.calculate();
        },

        setupListeners() {
            // Mode toggle
            document.querySelectorAll('.calc-mode-btn').forEach(btn => {
                btn.addEventListener('click', () => {
                    document.querySelectorAll('.calc-mode-btn').forEach(b => b.classList.remove('active'));
                    btn.classList.add('active');
                    this.setMode(btn.dataset.mode);
                });
            });

            // Score inputs
            document.querySelector('.calc-inputs').addEventListener('input', (e) => {
                if (e.target.classList.contains('grade-score') ||
                    e.target.classList.contains('credit-hours')) {
                    this.calculate();
                }
            });

            // What-if inputs
            ['whatifScore', 'whatifCredits'].forEach(id => {
                const el = document.getElementById(id);
                if (el) el.addEventListener('input', () => this.calculateWhatIf());
            });
        },

        setMode(mode) {
            const whatifContainer = document.getElementById('whatifContainer');
            if (mode === 'whatif') {
                whatifContainer.style.display = 'block';
                this.calculateWhatIf();
            } else {
                whatifContainer.style.display = 'none';
            }
            this.calculate();
        },

        addRow() {
            const inputs = document.querySelector('.calc-inputs');
            const addBtn = inputs.querySelector('.add-row-btn');

            const row = document.createElement('div');
            row.className = 'grade-row';
            row.innerHTML = `
            <input type="text" placeholder="Subject/Course Name">
            <input type="number" placeholder="Score" min="0" max="100" class="grade-score">
            <select class="credit-hours">
                <option value="3">3 Credits</option>
                <option value="4">4 Credits</option>
                <option value="2">2 Credits</option>
                <option value="1">1 Credit</option>
            </select>
            <button class="remove-row-btn" onclick="GradeCalc.removeRow(this)">
                <i class="fas fa-times"></i>
            </button>
        `;

            inputs.insertBefore(row, addBtn);
            row.querySelector('.grade-score').addEventListener('input', () => this.calculate());
            row.querySelector('.credit-hours').addEventListener('change', () => this.calculate());
        },

        removeRow(btn) {
            const rows = document.querySelectorAll('.grade-row');
            if (rows.length > 1) {
                btn.closest('.grade-row').remove();
                this.calculate();
            }
        },

        scoreToGradePoints(score) {
            if (score >= 93) return 4.0;
            if (score >= 90) return 3.7;
            if (score >= 87) return 3.3;
            if (score >= 83) return 3.0;
            if (score >= 80) return 2.7;
            if (score >= 77) return 2.3;
            if (score >= 73) return 2.0;
            if (score >= 70) return 1.7;
            if (score >= 67) return 1.3;
            if (score >= 63) return 1.0;
            if (score >= 60) return 0.7;
            return 0.0;
        },

        gpaToLetter(gpa) {
            if (gpa >= 3.7) return {
                letter: 'A',
                class: 'a'
            };
            if (gpa >= 3.0) return {
                letter: 'B',
                class: 'b'
            };
            if (gpa >= 2.0) return {
                letter: 'C',
                class: 'c'
            };
            if (gpa >= 1.0) return {
                letter: 'D',
                class: 'd'
            };
            return {
                letter: 'F',
                class: 'f'
            };
        },

        calculate() {
            const rows = document.querySelectorAll('.grade-row');
            let totalCredits = 0;
            let qualityPoints = 0;
            let totalScores = 0;
            let scoreCount = 0;

            rows.forEach(row => {
                const scoreInput = row.querySelector('.grade-score');
                const creditsSelect = row.querySelector('.credit-hours');

                if (scoreInput && creditsSelect) {
                    const score = parseFloat(scoreInput.value) || 0;
                    const credits = parseFloat(creditsSelect.value) || 3;

                    if (score > 0) {
                        const gradePoints = this.scoreToGradePoints(score);
                        totalCredits += credits;
                        qualityPoints += gradePoints * credits;
                        totalScores += score;
                        scoreCount++;
                    }
                }
            });

            const gpa = totalCredits > 0 ? qualityPoints / totalCredits : 0;
            const avgScore = scoreCount > 0 ? totalScores / scoreCount : 0;
            const gradeInfo = this.gpaToLetter(gpa);

            // Update UI
            document.getElementById('gpaNumber').textContent = gpa.toFixed(2);
            document.getElementById('totalCredits').textContent = totalCredits;
            document.getElementById('qualityPoints').textContent = qualityPoints.toFixed(1);
            document.getElementById('avgScore').textContent = avgScore.toFixed(1) + '%';

            const gradeEl = document.getElementById('gpaGrade');
            gradeEl.textContent = gradeInfo.letter;
            gradeEl.className = 'gpa-grade ' + gradeInfo.class;

            // Update circle
            const circumference = 408; // 2 * π * 65
            const progress = (gpa / 4.0) * circumference;
            document.getElementById('gpaProgress').style.strokeDashoffset = circumference - progress;
        },

        calculateWhatIf() {
            const score = parseFloat(document.getElementById('whatifScore').value) || 0;
            const credits = parseFloat(document.getElementById('whatifCredits').value) || 3;

            if (score <= 0) {
                document.getElementById('whatifResult').textContent =
                    'Enter a hypothetical grade to see projected GPA';
                return;
            }

            // Get current totals
            const rows = document.querySelectorAll('.grade-row');
            let totalCredits = 0;
            let qualityPoints = 0;

            rows.forEach(row => {
                const scoreInput = row.querySelector('.grade-score');
                const creditsSelect = row.querySelector('.credit-hours');

                if (scoreInput && creditsSelect) {
                    const s = parseFloat(scoreInput.value) || 0;
                    const c = parseFloat(creditsSelect.value) || 3;

                    if (s > 0) {
                        totalCredits += c;
                        qualityPoints += this.scoreToGradePoints(s) * c;
                    }
                }
            });

            // Add hypothetical
            totalCredits += credits;
            qualityPoints += this.scoreToGradePoints(score) * credits;

            const projectedGpa = totalCredits > 0 ? qualityPoints / totalCredits : 0;
            const gradeInfo = this.gpaToLetter(projectedGpa);

            document.getElementById('whatifResult').innerHTML =
                `Projected GPA: <strong>${projectedGpa.toFixed(2)}</strong> (${gradeInfo.letter})`;
        }
    };

    GradeCalc.init();
</script>