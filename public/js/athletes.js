// ===== TOAST HELPER =====
function showToast(msg, type = 'success') {
    const toastEl  = document.getElementById('app-toast');
    const toastMsg = document.getElementById('toast-msg');
    const toastIcon = document.getElementById('toast-icon');

    // Warna & ikon
    toastEl.className = 'toast align-items-center border-0 shadow text-white';
    if (type === 'success') {
        toastEl.classList.add('bg-success');
        toastIcon.textContent = 'check_circle';
    } else if (type === 'error') {
        toastEl.classList.add('bg-danger');
        toastIcon.textContent = 'error';
    } else {
        toastEl.classList.add('bg-warning', 'text-dark');
        toastIcon.textContent = 'warning';
    }

    toastMsg.textContent = msg;
    const bsToast = bootstrap.Toast.getOrCreateInstance(toastEl, { delay: 3500 });
    bsToast.show();
}

// ===== MODAL TAMBAH PESERTA =====
const btnAdd    = document.getElementById('btn-add-peserta');
const modal     = document.getElementById('modal-peserta');
const cancelBtn = document.getElementById('btn-cancel-peserta');
const form      = document.getElementById('form-peserta');

if (btnAdd) btnAdd.addEventListener('click', () => { modal.style.display = 'flex'; });
if (cancelBtn) cancelBtn.addEventListener('click', () => { modal.style.display = 'none'; form.reset(); resetTambahKlub(); resetPreviewKU(); });
if (modal) modal.addEventListener('click', (e) => { if (e.target === modal) { modal.style.display = 'none'; form.reset(); resetTambahKlub(); resetPreviewKU(); } });

// ===== TOMBOL TAMBAH KLUB =====
const btnTambahKlub = document.getElementById('btn-tambah-klub');
const wrapInputKlub = document.getElementById('wrap-input-klub');
const inputKlubBaru = document.getElementById('input-klub-baru');
const btnKonfirmKlub = document.getElementById('btn-konfirm-klub');
const selectKlub = document.getElementById('select-asal-klub');
const iconTambah = document.getElementById('icon-tambah-klub');
const msgKlub = document.getElementById('msg-klub');
let klubPanelOpen = false;

function resetTambahKlub() {
    if (!wrapInputKlub) return;
    wrapInputKlub.style.setProperty('display', 'none', 'important');
    iconTambah.textContent = 'add';
    btnTambahKlub.style.backgroundColor = '#52c41a';
    klubPanelOpen = false;
    inputKlubBaru.value = '';
    msgKlub.style.display = 'none';
    selectKlub.disabled = false;
}

if (btnTambahKlub) {
    btnTambahKlub.addEventListener('click', () => {
        klubPanelOpen = !klubPanelOpen;
        if (klubPanelOpen) {
            wrapInputKlub.style.setProperty('display', 'flex', 'important');
            inputKlubBaru.focus();
            iconTambah.textContent = 'close';
            btnTambahKlub.style.backgroundColor = '#ff4d4f';
            selectKlub.disabled = true;
        } else {
            resetTambahKlub();
        }
    });
}

if (btnKonfirmKlub) {
    btnKonfirmKlub.addEventListener('click', () => {
        const val = inputKlubBaru.value.trim();
        if (!val) { alert('Isi nama klub/sekolah!'); return; }
        const ex = Array.from(selectKlub.options).find(o => o.value.toLowerCase() === val.toLowerCase());
        if (!ex) {
            const opt = new Option(val, val);
            selectKlub.add(opt);
        }
        selectKlub.value = val;
        
        msgKlub.textContent = 'Klub baru ditambahkan ke daftar!';
        msgKlub.style.display = 'block';
        wrapInputKlub.style.setProperty('display', 'none', 'important');
        iconTambah.textContent = 'add';
        btnTambahKlub.style.backgroundColor = '#52c41a';
        klubPanelOpen = false;
        inputKlubBaru.value = '';
        selectKlub.disabled = false;
        setTimeout(() => msgKlub.style.display = 'none', 3000);
    });
}

if (form) {
    form.addEventListener('submit', (e) => {
        e.preventDefault();
        const formData = new FormData(form);

        fetch(window.APP_ROUTES.athletesStore, {
            method: 'POST',
            body: formData,
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
        })
        .then(r => r.json().then(data => ({ status: r.status, data })))
        .then(({ status, data }) => {
            if (status === 200 && data.message) {
                modal.style.display = 'none';
                form.reset();
                showToast(data.message, 'success');
                setTimeout(() => location.reload(), 1200);
            } else if (data.error) {
                showToast(data.error, 'error');
            } else if (data.errors) {
                const first = Object.values(data.errors)[0][0];
                showToast('Validasi: ' + first, 'error');
            } else {
                showToast('Gagal menyimpan peserta', 'error');
            }
        })
        .catch(err => showToast('Error: ' + err.message, 'error'));
    });
}

// ===== DELETE ATLET =====
let deleteTargetId   = null;
let deleteTargetRow  = null;

// Klik tombol hapus → tampilkan modal konfirmasi Bootstrap
document.addEventListener('click', function(e) {
    const btn = e.target.closest('.btn-delete-athlete');
    if (!btn) return;

    deleteTargetId  = btn.dataset.id;
    deleteTargetRow = btn.closest('tr');

    document.getElementById('hapus-nama-atlet').textContent = btn.dataset.nama;
    const bsModal = new bootstrap.Modal(document.getElementById('modal-konfirmasi-hapus'));
    bsModal.show();
});

// Klik tombol "Hapus" di dalam modal konfirmasi
const btnKonfirmasiHapus = document.getElementById('btn-konfirmasi-hapus');
if (btnKonfirmasiHapus) {
    btnKonfirmasiHapus.addEventListener('click', function() {
        if (!deleteTargetId) return;

        // Tutup modal konfirmasi
        bootstrap.Modal.getInstance(document.getElementById('modal-konfirmasi-hapus')).hide();

        fetch(`/athletes/${deleteTargetId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': window.APP_ROUTES.csrfToken,
                'Accept': 'application/json'
            }
        })
        .then(r => r.json())
        .then(data => {
            if (data.message) {
                showToast(data.message, 'success');
                // Reload halaman setelah toast muncul sebentar
                setTimeout(() => location.reload(), 1200);
            } else {
                showToast(data.error || 'Gagal menghapus atlet', 'error');
            }
            deleteTargetId  = null;
            deleteTargetRow = null;
        })
        .catch(err => showToast('Error: ' + err.message, 'error'));
    });
}

// ===== KELOMPOK UMUR OTOMATIS (PRSI) =====
function hitungKU(tanggalLahir) {
    const lahir = new Date(tanggalLahir);
    const today = new Date();
    let umur = today.getFullYear() - lahir.getFullYear();
    const belumUltah =
        today.getMonth() < lahir.getMonth() ||
        (today.getMonth() === lahir.getMonth() && today.getDate() < lahir.getDate());
    if (belumUltah) umur--;

    if (umur < 10)  return { ku: null, umur };
    if (umur <= 12) return { ku: 'KU I (10–12 Tahun)', umur };
    if (umur <= 14) return { ku: 'KU II (13–14 Tahun)', umur };
    if (umur <= 17) return { ku: 'KU III (15–17 Tahun)', umur };
    if (umur <= 24) return { ku: 'KU IV (18–24 Tahun)', umur };
    return { ku: 'Senior / Open (25+ Tahun)', umur };
}

const inputTanggalLahir = document.getElementById('input-tanggal-lahir');
if (inputTanggalLahir) {
    inputTanggalLahir.addEventListener('change', function () {
        const val = this.value;
        const previewKU      = document.getElementById('preview-ku');
        const previewWarning = document.getElementById('preview-ku-warning');

        if (!val) {
            previewKU.style.display      = 'none';
            previewWarning.style.display = 'none';
            return;
        }

        const { ku, umur } = hitungKU(val);

        if (ku === null) {
            previewKU.style.display      = 'none';
            previewWarning.style.display = 'block';
        } else {
            const labelFull = document.getElementById('label-ku-full');
            previewKU.style.display      = 'block';
            previewWarning.style.display = 'none';
            labelFull.textContent = ku + ' (' + umur + ' tahun)';
        }
    });
}

function resetPreviewKU() {
    const pKU = document.getElementById('preview-ku');
    const pWarning = document.getElementById('preview-ku-warning');
    if(pKU) pKU.style.display = 'none';
    if(pWarning) pWarning.style.display = 'none';
    if(inputTanggalLahir) inputTanggalLahir.value = '';
}

// ===== FILTER ATLET =====
let activeKU    = 'all';
let activeKlub  = 'all';
let activeEvent = 'all';

function applyFilter() {
    const rows = document.querySelectorAll('.athlete-row');
    let visibleCount = 0;

    rows.forEach(row => {
        const ku     = row.dataset.ku;
        const klub   = row.dataset.klub;
        const events = row.dataset.events;

        const matchKU    = activeKU    === 'all' || ku === activeKU;
        const matchKlub  = activeKlub  === 'all' || klub === activeKlub;
        const matchEvent = activeEvent === 'all' || events.includes(activeEvent);

        if (matchKU && matchKlub && matchEvent) {
            row.style.display = '';
            visibleCount++;
        } else {
            row.style.display = 'none';
        }
    });

    // Tampilkan baris kosong jika tidak ada hasil
    const emptyFilter = document.getElementById('row-empty-filter');
    if (emptyFilter) emptyFilter.style.display = visibleCount === 0 ? '' : 'none';

    // Info jumlah
    const filterInfo = document.getElementById('filter-info');
    const filterCount = document.getElementById('filter-count');
    const isFiltered = activeKU !== 'all' || activeKlub !== 'all' || activeEvent !== 'all';
    
    if (filterInfo) {
        if (isFiltered) {
            filterInfo.style.display = 'block';
            if(filterCount) filterCount.textContent = visibleCount;
        } else {
            filterInfo.style.display = 'none';
        }
    }

    // Tombol reset
    const btnResetFilter = document.getElementById('btn-reset-filter');
    if(btnResetFilter) btnResetFilter.style.display = isFiltered ? 'inline-block' : 'none';
}

// Filter KU chips
document.querySelectorAll('#filter-ku .filter-chip').forEach(chip => {
    chip.addEventListener('click', function() {
        document.querySelectorAll('#filter-ku .filter-chip').forEach(c => c.classList.remove('active'));
        this.classList.add('active');
        activeKU = this.dataset.ku;
        applyFilter();
    });
});

// Filter Klub
const filterKlub = document.getElementById('filter-klub');
if (filterKlub) {
    filterKlub.addEventListener('change', function() {
        activeKlub = this.value;
        applyFilter();
    });
}

// Filter Event
const filterEvent = document.getElementById('filter-event');
if (filterEvent) {
    filterEvent.addEventListener('change', function() {
        activeEvent = this.value;
        applyFilter();
    });
}

// Reset semua filter
const btnResetFilter = document.getElementById('btn-reset-filter');
if (btnResetFilter) {
    btnResetFilter.addEventListener('click', function() {
        activeKU    = 'all';
        activeKlub  = 'all';
        activeEvent = 'all';
        document.querySelectorAll('#filter-ku .filter-chip').forEach(c => c.classList.remove('active'));
        document.querySelector('#filter-ku .filter-chip[data-ku="all"]').classList.add('active');
        if(filterKlub) filterKlub.value  = 'all';
        if(filterEvent) filterEvent.value = 'all';
        applyFilter();
    });
}
