<?php

namespace Tests\Feature;

use App\Models\MataPelajaran;
use App\Models\Siswa;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MicroModalAlertsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_layout_includes_global_micromodal_alert_and_confirm_containers(): void
    {
        $response = $this->get(route('dashboard'));

        $response->assertStatus(200);
        $response->assertSee('id="global-alert-modal"', false);
        $response->assertSee('id="global-confirm-modal"', false);
        $response->assertSee('class="modal micromodal-slide"', false);
        $response->assertSee('data-micromodal-close', false);
        $response->assertSee('role="dialog"', false);
        $response->assertSee('aria-modal="true"', false);
    }

    public function test_flash_success_triggers_micromodal_alert(): void
    {
        $response = $this->withSession(['success' => 'Data penilaian berhasil disimpan secara massal.'])
            ->get(route('penilaian.matrix'));

        $response->assertStatus(200);
        $response->assertSee("window.showAlertModal('Berhasil!'", false);
        $response->assertSee('Data penilaian berhasil disimpan secara massal.', false);
    }

    public function test_flash_error_triggers_micromodal_alert(): void
    {
        $response = $this->withSession(['error' => 'Format file Excel tidak sesuai standar.'])
            ->get(route('penilaian.matrix'));

        $response->assertStatus(200);
        $response->assertSee("window.showAlertModal('Terjadi Kesalahan'", false);
        $response->assertSee('Format file Excel tidak sesuai standar.', false);
    }

    public function test_validation_errors_trigger_micromodal_alert(): void
    {
        $messageBag = new \Illuminate\Support\MessageBag(['nama' => ['Nama siswa wajib diisi.']]);
        $viewErrorBag = new \Illuminate\Support\ViewErrorBag();
        $viewErrorBag->put('default', $messageBag);

        $view = $this->view('siswa.create', [
            'errors' => $viewErrorBag,
            'kelasList' => collect(['X-A', 'X-B', 'XI-A', 'XII-A']),
        ]);

        $view->assertSee("window.showAlertModal('Validasi Gagal'", false);
        $view->assertSee('Nama siswa wajib diisi.', false);
    }

    public function test_matrix_page_contains_all_micromodal_dialogs(): void
    {
        $mapel = MataPelajaran::first();
        $response = $this->get(route('penilaian.matrix', [
            'mata_pelajaran_id' => $mapel->id,
            'kelas' => 'X-A',
        ]));

        $response->assertStatus(200);

        // Check 4 MicroModal dialogs
        $response->assertSee('id="quick-edit-siswa-modal"', false);
        $response->assertSee('id="reset-nilai-modal"', false);
        $response->assertSee('id="delete-siswa-modal"', false);
        $response->assertSee('id="import-excel-modal"', false);

        // Check data-micromodal-trigger on import button
        $response->assertSee('data-micromodal-trigger="import-excel-modal"', false);

        // Check accessibility attributes
        $response->assertSee('aria-labelledby="quick-edit-siswa-modal-title"', false);
        $response->assertSee('aria-labelledby="reset-nilai-modal-title"', false);
        $response->assertSee('aria-labelledby="delete-siswa-modal-title"', false);
        $response->assertSee('aria-labelledby="import-excel-modal-title"', false);
    }

    public function test_delete_forms_use_data_confirm_for_micromodal_interception(): void
    {
        $responseSiswa = $this->get(route('siswa.index'));
        $responseSiswa->assertStatus(200);
        $responseSiswa->assertSee('data-confirm=', false);
        $responseSiswa->assertSee('data-confirm-title="Hapus Data Siswa"', false);

        $responseMapel = $this->get(route('matapelajaran.index'));
        $responseMapel->assertStatus(200);
        $responseMapel->assertSee('data-confirm=', false);
        $responseMapel->assertSee('data-confirm-title="Hapus Mata Pelajaran"', false);

        $responseTp = $this->get(route('tujuan-pembelajaran.index'));
        $responseTp->assertStatus(200);
        $responseTp->assertSee('data-confirm=', false);
        $responseTp->assertSee('data-confirm-title="Hapus Tujuan Pembelajaran"', false);
    }

    public function test_reusable_modal_blade_component_renders_compliant_markup(): void
    {
        $view = $this->blade(
            '<x-modal id="test-custom-modal" title="Judul Modal Uji" size="lg">
                <p>Isi konten dialog modal uji.</p>
                <x-slot:footer>
                    <button type="button" data-micromodal-close>Batal</button>
                </x-slot:footer>
            </x-modal>'
        );

        $view->assertSee('id="test-custom-modal"', false);
        $view->assertSee('class="modal micromodal-slide"', false);
        $view->assertSee('modal__container--lg', false);
        $view->assertSee('Judul Modal Uji', false);
        $view->assertSee('Isi konten dialog modal uji.', false);
        $view->assertSee('data-micromodal-close', false);
        $view->assertSee('role="dialog"', false);
        $view->assertSee('aria-modal="true"', false);
    }
}
