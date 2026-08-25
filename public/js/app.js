// PLA Application JavaScript

function toggleSidebar() {
  const sidebar = document.getElementById('sidebar');
  if (sidebar) {
    sidebar.classList.toggle('active');
  }
}

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

      // Default weights (can be updated from dataset or defaults)
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
      if (displayGrade) displayGrade.innerText = grade;
      if (displayStatus) {
        displayStatus.innerText = status;
        displayStatus.className = 'badge ' + (status === 'Lulus' ? 'badge-success' : 'badge-danger');
      }
    };

    [inputTugas, inputKuis, inputUts, inputUas, inputKeaktifan].forEach(input => {
      if (input) input.addEventListener('input', calculateGrade);
    });

    calculateGrade();
  }
});
