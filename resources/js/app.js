import MicroModal from 'micromodal';

window.MicroModal = MicroModal;

// Global MicroModal helper functions
window.openModal = function (modalId) {
    if (window.MicroModal) {
        window.MicroModal.show(modalId, {
            awaitOpenAnimation: true,
            awaitCloseAnimation: true,
            disableScroll: true,
        });
    }
};

window.closeModal = function (modalId) {
    if (window.MicroModal) {
        window.MicroModal.close(modalId);
    }
};

// Global Alert Modal trigger using MicroModal
window.showAlertModal = function (title, message, type = 'info') {
    const titleEl = document.getElementById('global-alert-modal-title');
    const descEl = document.getElementById('global-alert-modal-desc');
    const iconContainer = document.getElementById('global-alert-modal-icon');

    if (titleEl) titleEl.textContent = title;
    if (descEl) descEl.innerHTML = message;

    if (iconContainer) {
        if (type === 'success') {
            iconContainer.className = 'h-12 w-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center border border-emerald-100 shrink-0';
            iconContainer.innerHTML = '<svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>';
        } else if (type === 'error') {
            iconContainer.className = 'h-12 w-12 rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center border border-rose-100 shrink-0';
            iconContainer.innerHTML = '<svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>';
        } else if (type === 'warning') {
            iconContainer.className = 'h-12 w-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center border border-amber-100 shrink-0';
            iconContainer.innerHTML = '<svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>';
        } else {
            iconContainer.className = 'h-12 w-12 rounded-2xl bg-teal-50 text-teal-600 flex items-center justify-center border border-teal-100 shrink-0';
            iconContainer.innerHTML = '<svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>';
        }
    }

    window.openModal('global-alert-modal');
};

// Global Confirm Modal trigger using MicroModal
window.showConfirmModal = function ({ title, message, type = 'danger', confirmText = 'Konfirmasi', onConfirm }) {
    const titleEl = document.getElementById('global-confirm-modal-title');
    const descEl = document.getElementById('global-confirm-modal-desc');
    const btnConfirm = document.getElementById('global-confirm-modal-btn');
    const iconContainer = document.getElementById('global-confirm-modal-icon');

    if (titleEl) titleEl.textContent = title;
    if (descEl) descEl.innerHTML = message;
    if (btnConfirm) {
        btnConfirm.textContent = confirmText;
        if (type === 'danger') {
            btnConfirm.className = 'px-5 py-2 text-sm font-semibold bg-rose-600 hover:bg-rose-700 text-white rounded-xl shadow-xs transition-colors';
            if (iconContainer) {
                iconContainer.className = 'h-12 w-12 rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center border border-rose-100 shrink-0';
                iconContainer.innerHTML = '<svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/></svg>';
            }
        } else {
            btnConfirm.className = 'px-5 py-2 text-sm font-semibold bg-teal-600 hover:bg-teal-700 text-white rounded-xl shadow-xs transition-colors';
            if (iconContainer) {
                iconContainer.className = 'h-12 w-12 rounded-2xl bg-teal-50 text-teal-600 flex items-center justify-center border border-teal-100 shrink-0';
                iconContainer.innerHTML = '<svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>';
            }
        }

        // Replace click handler cleanly
        const newBtn = btnConfirm.cloneNode(true);
        btnConfirm.parentNode.replaceChild(newBtn, btnConfirm);
        newBtn.addEventListener('click', () => {
            window.closeModal('global-confirm-modal');
            if (typeof onConfirm === 'function') onConfirm();
        });
    }

    window.openModal('global-confirm-modal');
};

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

// Initialize MicroModal on DOMContentLoaded
document.addEventListener('DOMContentLoaded', () => {
    try {
        MicroModal.init({
            openTrigger: 'data-micromodal-trigger',
            closeTrigger: 'data-micromodal-close',
            openClass: 'is-open',
            disableScroll: true,
            disableFocus: false,
            awaitOpenAnimation: true,
            awaitCloseAnimation: true,
            debugMode: false,
        });
    } catch (e) {
        console.warn('MicroModal init:', e);
    }

    // Intercept forms with data-confirm to use MicroModal Confirm Dialog
    document.addEventListener('submit', function (e) {
        const form = e.target;
        const confirmMsg = form.getAttribute('data-confirm');
        if (confirmMsg && !form.dataset.confirmed) {
            e.preventDefault();
            const title = form.getAttribute('data-confirm-title') || 'Konfirmasi Hapus Data';
            const confirmText = form.getAttribute('data-confirm-btn') || 'Ya, Hapus';
            const type = form.getAttribute('data-confirm-type') || 'danger';

            window.showConfirmModal({
                title: title,
                message: confirmMsg,
                type: type,
                confirmText: confirmText,
                onConfirm: () => {
                    form.dataset.confirmed = 'true';
                    form.submit();
                }
            });
        }
    });
});

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
