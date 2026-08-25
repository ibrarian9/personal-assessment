// PLA Application JavaScript (Tailwind CSS v4 Edition)

window.toggleSidebar = function () {
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebar-overlay');
    if (sidebar) {
        sidebar.classList.toggle('-translate-x-full');
    }
    if (overlay) {
        overlay.classList.toggle('hidden');
    }
};

// Live Grade Calculator for Penilaian Form
document.addEventListener('DOMContentLoaded', () => {
    const inputTugas = document.getElementById('input_tugas');
    const inputKuis = document.getElementById('input_kuis');
    const inputUts = document.getElementById('input_uts');
    const inputUas = document.getElementById('input_uas');
    const inputKeaktifan = document.getElementById('input_keaktifan');

    const displayNilaiAkhir = document.getElementById('display_nilai_akhir');
    const displayGrade = document.getElementById('display_grade');
    const displayStatus = document.getElementById('display_status');

    if (inputTugas && inputKuis && inputUts && inputUas) {
        const calculateGrade = () => {
            const tugas = parseFloat(inputTugas.value) || 0;
            const kuis = parseFloat(inputKuis.value) || 0;
            const uts = parseFloat(inputUts.value) || 0;
            const uas = parseFloat(inputUas.value) || 0;
            const keaktifan = parseFloat(inputKeaktifan ? inputKeaktifan.value : 0) || 0;

            const bTugas = parseFloat(window.bobotConfig?.tugas || 20);
            const bKuis = parseFloat(window.bobotConfig?.kuis || 15);
            const bUts = parseFloat(window.bobotConfig?.uts || 25);
            const bUas = parseFloat(window.bobotConfig?.uas || 30);
            const bKeaktifan = parseFloat(window.bobotConfig?.keaktifan || 10);

            const totalBobot = bTugas + bKuis + bUts + bUas + bKeaktifan;
            const score = ((tugas * bTugas) + (kuis * bKuis) + (uts * bUts) + (uas * bUas) + (keaktifan * bKeaktifan)) / (totalBobot || 100);
            const finalScore = Math.round(score * 100) / 100;

            let grade = 'E';
            if (finalScore >= 85) grade = 'A';
            else if (finalScore >= 80) grade = 'A-';
            else if (finalScore >= 75) grade = 'B+';
            else if (finalScore >= 70) grade = 'B';
            else if (finalScore >= 65) grade = 'B-';
            else if (finalScore >= 60) grade = 'C+';
            else if (finalScore >= 55) grade = 'C';
            else if (finalScore >= 40) grade = 'D';

            const status = (grade === 'D' || grade === 'E') ? 'Tidak Lulus' : 'Lulus';

            if (displayNilaiAkhir) displayNilaiAkhir.innerText = finalScore.toFixed(2);
            if (displayGrade) displayGrade.innerText = 'Grade ' + grade;
            if (displayStatus) {
                displayStatus.innerText = status;
                if (status === 'Lulus') {
                    displayStatus.className = 'inline-flex items-center px-3 py-1 text-sm font-semibold rounded-full bg-emerald-100 text-emerald-800 border border-emerald-200';
                } else {
                    displayStatus.className = 'inline-flex items-center px-3 py-1 text-sm font-semibold rounded-full bg-rose-100 text-rose-800 border border-rose-200';
                }
            }
        };

        [inputTugas, inputKuis, inputUts, inputUas, inputKeaktifan].forEach(input => {
            if (input) input.addEventListener('input', calculateGrade);
        });

        calculateGrade();
    }

    // Setting Bobot Live Checker
    const bobotInputs = document.querySelectorAll('.bobot-input');
    const bobotBadge = document.getElementById('total_bobot_badge');

    if (bobotInputs.length > 0 && bobotBadge) {
        const checkTotal = () => {
            let total = 0;
            bobotInputs.forEach(i => total += (parseFloat(i.value) || 0));
            total = Math.round(total * 10) / 10;

            bobotBadge.innerText = total + '%';
            if (Math.abs(total - 100) < 0.01) {
                bobotBadge.className = 'inline-flex items-center px-3.5 py-1 rounded-full text-sm font-bold bg-emerald-100 text-emerald-800 border border-emerald-300';
            } else {
                bobotBadge.className = 'inline-flex items-center px-3.5 py-1 rounded-full text-sm font-bold bg-rose-100 text-rose-800 border border-rose-300 animate-pulse';
            }
        };

        bobotInputs.forEach(i => i.addEventListener('input', checkTotal));
        checkTotal();
    }
});
