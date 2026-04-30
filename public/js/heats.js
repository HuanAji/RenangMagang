    const CSRF = document.querySelector('meta[name="csrf-token"]').content;
    let ACTIVE_HEAT_ID = null;

    // ===== TOAST HELPER =====
    function showToast(msg, type = 'success') {
        const toastEl  = document.getElementById('app-toast');
        const toastMsg = document.getElementById('toast-msg');
        const toastIcon = document.getElementById('toast-icon');

        toastEl.className = 'toast align-items-center border-0 shadow text-white';
        if (type === 'success') {
            toastEl.classList.add('bg-success');
            toastIcon.textContent = 'check_circle';
        } else if (type === 'error') {
            toastEl.classList.add('bg-danger');
            toastIcon.textContent = 'error';
        } else {
            // warning
            toastEl.className = 'toast align-items-center border-0 shadow bg-warning text-dark';
            toastIcon.textContent = 'warning';
        }

        toastMsg.textContent = msg;
        const bsToast = bootstrap.Toast.getOrCreateInstance(toastEl, { delay: 3500 });
        bsToast.show();
    }

    // ===== CLOCK =====
    function updateClock() {
        const now = new Date();
        document.getElementById('clock').textContent = now.toLocaleString('id-ID', {
            weekday: 'long', year: 'numeric', month: 'long', day: 'numeric',
            hour: '2-digit', minute: '2-digit', second: '2-digit'
        });
    }
    setInterval(updateClock, 1000);
    updateClock();

    // ===== LOAD & GENERATE HEATS =====
    document.getElementById('btn-load-heats').addEventListener('click', loadHeats);

    document.getElementById('btn-generate-heats').addEventListener('click', () => {
        const eventId = document.getElementById('select-event').value;
        const gender = document.getElementById('select-gender').value;
        if (!eventId || !gender) { showToast('Pilih event dan jenis kelamin sebelum membuat Heat!', 'warning'); return; }
        
        const bsModal = new bootstrap.Modal(document.getElementById('modal-generate-heat'));
        bsModal.show();
    });

    document.getElementById('btn-konfirmasi-generate').addEventListener('click', (e) => {
        const btn = e.currentTarget;
        const oriText = btn.innerHTML;
        btn.innerHTML = `<span class="spinner-border spinner-border-sm" aria-hidden="true"></span> Memproses...`;
        btn.disabled = true;

        const eventId = document.getElementById('select-event').value;
        const gender = document.getElementById('select-gender').value;
        
        fetch('/heats/regenerate', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': CSRF,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ event_id: eventId, jenis_kelamin: gender })
        })
        .then(r => r.json())
        .then(data => {
            showToast(data.message || 'Heat berhasil di-generate!', 'success');
            const modalEl = document.getElementById('modal-generate-heat');
            const modalInstance = bootstrap.Modal.getInstance(modalEl);
            if(modalInstance) modalInstance.hide();
            
            loadHeats();
        })
        .catch(err => showToast('Gagal generate heat: ' + err.message, 'error'))
        .finally(() => {
            btn.innerHTML = oriText;
            btn.disabled = false;
        });
    });

    // ===== GENERATE ALL HEATS =====
    document.getElementById('btn-generate-all').addEventListener('click', () => {
        const bsModal = new bootstrap.Modal(document.getElementById('modal-generate-all'));
        bsModal.show();
    });

    document.getElementById('btn-konfirmasi-generate-all').addEventListener('click', () => {
        // Close the confirmation modal
        const modalEl = document.getElementById('modal-generate-all');
        const modalInstance = bootstrap.Modal.getInstance(modalEl);
        if (modalInstance) modalInstance.hide();

        // Show overlay
        const overlay = document.getElementById('gen-all-overlay');
        const processingEl = document.getElementById('gen-all-processing');
        const doneEl = document.getElementById('gen-all-done');
        const progressBar = document.getElementById('gen-all-progress-bar');
        const statusEl = document.getElementById('gen-all-status');

        // Reset states
        processingEl.style.display = 'block';
        doneEl.style.display = 'none';
        progressBar.style.width = '0%';
        statusEl.innerHTML = 'Mempersiapkan data...';
        overlay.classList.add('active');

        // Animate progress bar (fake progress until server responds)
        let fakeProgress = 0;
        const progressInterval = setInterval(() => {
            if (fakeProgress < 85) {
                fakeProgress += Math.random() * 8 + 2;
                if (fakeProgress > 85) fakeProgress = 85;
                progressBar.style.width = fakeProgress + '%';

                // Update status text at different stages
                if (fakeProgress > 10 && fakeProgress < 30) {
                    statusEl.innerHTML = 'Mengambil data registrasi peserta...';
                } else if (fakeProgress >= 30 && fakeProgress < 50) {
                    statusEl.innerHTML = 'Menghitung kelompok umur atlet...';
                } else if (fakeProgress >= 50 && fakeProgress < 70) {
                    statusEl.innerHTML = 'Membuat heat dan jalur perlombaan...';
                } else if (fakeProgress >= 70) {
                    statusEl.innerHTML = 'Finalisasi data heat...';
                }
            }
        }, 400);

        fetch('/heats/regenerate-all', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': CSRF,
                'Accept': 'application/json'
            }
        })
        .then(r => r.json())
        .then(data => {
            clearInterval(progressInterval);

            // Complete the progress bar to 100%
            progressBar.style.width = '100%';
            statusEl.innerHTML = '<strong>Selesai!</strong>';

            // After a brief pause, show the done state
            setTimeout(() => {
                processingEl.style.display = 'none';
                doneEl.style.display = 'block';

                // Update result text
                document.getElementById('gen-all-result-text').textContent =
                    `${data.processed || 0} kombinasi event berhasil diproses. Total ${data.total_heats || 0} heat terbuat.`;

                // Build details list
                const detailsEl = document.getElementById('gen-all-details');
                if (data.details && data.details.length > 0) {
                    let detailsHtml = '';
                    data.details.forEach((d, i) => {
                        detailsHtml += `<div style="padding:6px 0; border-bottom:1px solid #e2e8f0; font-size:0.82rem; display:flex; align-items:center; gap:8px;">
                            <span style="width:22px;height:22px;border-radius:50%;background:#dcfce7;color:#16a34a;display:inline-flex;align-items:center;justify-content:center;font-size:0.7rem;font-weight:700;flex-shrink:0;">${i + 1}</span>
                            <span style="font-weight:600;color:#1e293b;">${d.event}</span>
                            <span style="color:#64748b;">— ${d.gender}</span>
                        </div>`;
                    });
                    detailsEl.innerHTML = detailsHtml;
                } else {
                    detailsEl.innerHTML = '<div style="font-size:0.82rem; color:#94a3b8; padding:10px 0;">Tidak ada data yang diproses.</div>';
                }
            }, 600);
        })
        .catch(err => {
            clearInterval(progressInterval);
            overlay.classList.remove('active');
            showToast('Gagal generate semua heat: ' + err.message, 'error');
        });
    });

    // Close overlay button
    document.getElementById('btn-gen-all-close').addEventListener('click', () => {
        document.getElementById('gen-all-overlay').classList.remove('active');
        // Reload heats if event/gender are selected
        const eventId = document.getElementById('select-event').value;
        const gender = document.getElementById('select-gender').value;
        if (eventId && gender) loadHeats();
    });

    function loadHeats() {
        const eventId = document.getElementById('select-event').value;
        const gender  = document.getElementById('select-gender').value;
        const ku      = document.getElementById('select-ku').value;
        if (!eventId || !gender) { showToast('Pilih event dan jenis kelamin terlebih dahulu!', 'warning'); return; }
        
        const container = document.getElementById('heat-list');
        container.innerHTML = `<div class="empty-state" style="padding:40px; grid-column: 1 / -1;">
            <div class="spinner-border text-primary" role="status" style="width: 2.2rem; height: 2.2rem; margin-bottom: 12px; color: var(--accent-blue) !important;">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p style="margin:0; font-size:0.85rem; color:var(--text-secondary); font-weight:600;">Memuat Daftar Heat...</p>
        </div>`;

        let url = `/api/heats?event_id=${eventId}&jenis_kelamin=${gender}`;
        if (ku) url += `&kelompok_umur=${encodeURIComponent(ku)}`;

        fetch(url)
            .then(r => r.json())
            .then(data => renderHeatList(data.heats, data.event_name));
    }

    function renderHeatList(heats, eventName) {
        const container = document.getElementById('heat-list');
        if (!heats || heats.length === 0) {
            container.innerHTML = `<div class="empty-state" style="padding:20px;">
                <span class="material-icons" style="font-size:2rem;">event_busy</span>
                <p style="margin:6px 0 0;font-size:0.82rem;">Belum ada heat untuk kombinasi ini</p>
            </div>`;
            return;
        }

        // Warna badge per KU
        const kuColors = {
            'KU I':   { bg: '#e0f2fe', color: '#0369a1' },
            'KU II':  { bg: '#dcfce7', color: '#15803d' },
            'KU III': { bg: '#fef9c3', color: '#a16207' },
            'KU IV':  { bg: '#ede9fe', color: '#7c3aed' },
            'Senior': { bg: '#fee2e2', color: '#b91c1c' },
            'Umum':   { bg: '#f1f5f9', color: '#64748b' },
        };

        let lastKU = null;
        let html = '';
        heats.forEach(heat => {
            const totalPeserta = heat.lanes.length;
            const isFull = totalPeserta >= 8;
            const statusClass = heat.status === 'active' ? 'active-heat' : heat.status === 'completed' ? 'completed-heat' : '';
            const badgeClass  = heat.status;
            const badgeText   = heat.status === 'active' ? '🔴 AKTIF' : heat.status === 'completed' ? '✅ SELESAI' : '⏳ MENUNGGU';
            const ku          = heat.kelompok_umur || '-';
            const kuStyle     = kuColors[ku] || { bg: '#f1f5f9', color: '#64748b' };

            // Tampilkan header pemisah KU jika berganti
            if (ku !== lastKU) {
                html += `<div style="grid-column: 1 / -1; margin-top: ${lastKU ? '8px' : '0'}; margin-bottom: 2px;">
                    <span style="font-size:0.72rem; font-weight:700; text-transform:uppercase; letter-spacing:1px;
                        background:${kuStyle.bg}; color:${kuStyle.color}; padding:3px 10px; border-radius:20px;">
                        ${ku}
                    </span>
                </div>`;
                lastKU = ku;
            }

            const pesertaInfo = isFull
                ? `<span style="font-weight:600; color:var(--accent-green);">${totalPeserta} peserta</span>`
                : `<span style="font-weight:600; color:var(--accent-orange);">${totalPeserta} peserta</span>`;

            let actionHtml = '';
            if (heat.status === 'active') {
                actionHtml = `<button class="btn-heat btn-complete" onclick="completeHeat(${heat.id})">
                    <span class="material-icons">stop</span> Tandai Selesai
                </button>`;
            } else {
                actionHtml = `<button class="btn-heat btn-activate" onclick="activateHeat(${heat.id})">
                    <span class="material-icons">play_arrow</span> ${heat.status === 'completed' ? 'Aktifkan Ulang' : 'Aktifkan'}
                </button>`;
            }

            html += `
                <div class="heat-item ${statusClass}">
                    <div class="heat-item-header">
                        <strong>Heat ${heat.heat_number}</strong>
                        <span class="heat-badge ${badgeClass}">${badgeText}</span>
                    </div>
                    <div class="heat-athletes" style="margin-bottom:8px;">${pesertaInfo}</div>
                    <div class="heat-actions">${actionHtml}</div>
                </div>`;
        });
        container.innerHTML = html;
    }

    // ===== ACTIVATE / COMPLETE =====
    window.activateHeat = function(id) {
        fetch(`/heats/${id}/activate`, {
            method:'POST', headers:{'X-CSRF-TOKEN': CSRF, 'Accept':'application/json'}
        }).then(r => r.json()).then(() => { loadHeats(); loadActiveHeat(); });
    }
    let pendingCompleteHeatId = null;
    const modalTandaiSelesai = new bootstrap.Modal(document.getElementById('modal-tandai-selesai'));

    window.completeHeat = function(id) {
        pendingCompleteHeatId = id;
        modalTandaiSelesai.show();
    }

    document.getElementById('btn-konfirmasi-tandai-selesai').addEventListener('click', function() {
        if (!pendingCompleteHeatId) return;
        
        const btn = this;
        const originalText = btn.innerHTML;
        btn.innerHTML = `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Menyimpan...`;
        btn.disabled = true;

        fetch(`/heats/${pendingCompleteHeatId}/complete`, {
            method:'POST', headers:{'X-CSRF-TOKEN': CSRF, 'Accept':'application/json'}
        }).then(r => r.json()).then(() => { 
            // Hapus rekam data IoT untuk Heat yang selesai
            fetch('/results/clear-all', { method: 'POST', headers: { 'X-CSRF-TOKEN': CSRF } })
            .then(() => {
                document.getElementById('iot-tbody').innerHTML = `<tr><td colspan="7" class="empty-state" style="padding:30px;">
                    <span class="material-icons">flag</span> Heat telah diselesaikan.
                </td></tr>`;
                iotHidden = true;
                setTimeout(() => { iotHidden = false; }, 4000); 

                loadHeats(); 
                loadActiveHeat(); 
                
                modalTandaiSelesai.hide();
                btn.innerHTML = originalText;
                btn.disabled = false;
                pendingCompleteHeatId = null;
                showToast('Heat berhasil diselesaikan!', 'success');
            });
        }).catch(() => {
            btn.innerHTML = originalText;
            btn.disabled = false;
            showToast('Terjadi kesalahan!', 'error');
        });
    });

    // ===== ACTIVE HEAT =====
    function loadActiveHeat() {
        fetch('/api/active-heat').then(r => r.json()).then(data => {
            const banner = document.getElementById('active-heat-banner');
            const title = document.getElementById('ahb-title');
            const sub = document.getElementById('ahb-subtitle');
            const live = document.getElementById('ahb-live');
            const tbody = document.getElementById('lane-tbody');
            if (data.active) {
                ACTIVE_HEAT_ID = data.heat_id;
                banner.classList.remove('no-active');
                title.textContent = data.info;
                sub.textContent = `${data.lanes.length} Atlet siap berlomba`;
                live.style.display = 'block';
                // Pertama, kita tentukan waktu final untuk tiap lane agar bisa disorting
                data.lanes.forEach(l => {
                    l.finalTime = l.result_time || window.latestIoT[l.lane_number];
                    // Jika belum ada waktu, beri nilai sangat besar agar berada di urutan terbawah
                    l.sortValue = l.finalTime ? l.finalTime : '99:99.999';
                });

                // Sort array berdasarkan sortValue (waktu tercepat di atas)
                data.lanes.sort((a, b) => a.sortValue.localeCompare(b.sortValue));

                let laneHtml = '';
                let validRankCount = 1;
                data.lanes.forEach(l => {
                    let rankBadge = '';
                    if (l.finalTime && l.sortValue !== '99:99.999') {
                        if (validRankCount === 1) rankBadge = '<span style="position:absolute; right:-28px; top:50%; transform:translateY(-50%); font-size:1.2rem;" title="Juara 1">🥇</span>';
                        else if (validRankCount === 2) rankBadge = '<span style="position:absolute; right:-28px; top:50%; transform:translateY(-50%); font-size:1.2rem;" title="Juara 2">🥈</span>';
                        else if (validRankCount === 3) rankBadge = '<span style="position:absolute; right:-28px; top:50%; transform:translateY(-50%); font-size:1.2rem;" title="Juara 3">🥉</span>';
                        validRankCount++;
                    }

                    const timeHtml = l.finalTime
                        ? `<div style="position:relative; display:inline-block;"><span class="result-time">${l.finalTime}</span>${rankBadge}</div>`
                        : `<span class="no-time">menunggu...</span>`;
                    laneHtml += `<tr>
                        <td style="text-align:center;"><span class="lane-num" style="background:var(--accent-blue);">${l.lane_number}</span></td>
                        <td><strong>${l.athlete_name}</strong></td>
                        <td style="color:var(--text-secondary);">${l.club}</td>
                        <td style="text-align:center; white-space:nowrap;">${timeHtml}</td>
                    </tr>`;
                });
                tbody.innerHTML = laneHtml;
            } else {
                ACTIVE_HEAT_ID = null;
                banner.classList.add('no-active');
                title.textContent = 'Belum ada Heat aktif';
                sub.textContent = 'Aktifkan salah satu Heat dari panel kiri untuk memulai perlombaan';
                live.style.display = 'none';
                tbody.innerHTML = `<tr><td colspan="4" class="empty-state" style="padding:30px;">
                    <span class="material-icons">pool</span> Aktifkan Heat untuk melihat peserta
                </td></tr>`;
            }
        });
    }

    // ===== IoT RESULTS =====
    let iotHidden = false;
    window.latestIoT = {}; // Simpan waktu IoT secara global agar bisa dibaca tabel peserta
    function loadIoTResults() {
        if (iotHidden) return;
        fetch('/results/data').then(r => r.json()).then(data => {
            const tbody = document.getElementById('iot-tbody');
            if (!data || data.length === 0) {
                window.latestIoT = {};
                tbody.innerHTML = `<tr><td colspan="7" class="empty-state" style="padding:30px;">
                    <span class="material-icons">timer</span> Menunggu data dari alat IoT...
                </td></tr>`;
                return;
            }
            let html = '';
            window.latestIoT = {};
            data.forEach((item, idx) => {
                const format = item.waktu_format;
                window.latestIoT[item.player] = format; // Simpan waktu untuk player/lane ini
                const menit = item.waktu_menit !== null ? ('0' + item.waktu_menit).slice(-2) : '00';
                const detik = item.waktu_detik !== null ? ('0' + item.waktu_detik).slice(-2) : '00';
                const ms = item.waktu_ms !== null ? ('00' + item.waktu_ms).slice(-3) : '000';
                
                html += `<tr>
                    <td style="text-align:center;">${idx + 1}</td>
                    <td style="text-align:center;">${item.player}</td>
                    <td style="text-align:center;">${menit}</td>
                    <td style="text-align:center;">${detik}</td>
                    <td style="text-align:center;">${ms}</td>
                    <td style="text-align:center;">${format}</td>
                    <td style="text-align:center;">${item.timestamp}</td>
                </tr>`;
            });
            tbody.innerHTML = html;
        });
    }

    // ===== STOPWATCH =====
    let swStartTime = 0, swElapsed = 0, swRunning = false, swInterval = null;
    const swDisplay = document.getElementById('sw-display');
    const swWrap = document.getElementById('sw-wrap');
    const btnStart = document.getElementById('btn-sw-start');
    const btnStop = document.getElementById('btn-sw-stop');
    const btnReset = document.getElementById('btn-sw-reset');

    function formatSW(ms) {
        const totalSec = Math.floor(ms / 1000);
        const m = String(Math.floor(totalSec / 60)).padStart(2, '0');
        const s = String(totalSec % 60).padStart(2, '0');
        const millis = String(ms % 1000).padStart(3, '0');
        return `${m}:${s}<span class="sw-ms">.${millis}</span>`;
    }
    function updateSW() { swElapsed = Date.now() - swStartTime; swDisplay.innerHTML = formatSW(swElapsed); }

    btnStart.addEventListener('click', () => {
        swStartTime = Date.now() - swElapsed;
        swRunning = true;
        swInterval = setInterval(updateSW, 37);
        btnStart.style.display = 'none'; btnStop.style.display = 'flex'; btnReset.style.display = 'none';
        swWrap.classList.add('sw-running'); swWrap.classList.remove('sw-stopped');
    });
    btnStop.addEventListener('click', () => {
        clearInterval(swInterval); swRunning = false;
        btnStop.style.display = 'none'; btnStart.style.display = 'flex';
        btnStart.innerHTML = '<span class="material-icons">play_arrow</span> LANJUT';
        btnReset.style.display = 'flex';
        swWrap.classList.remove('sw-running'); swWrap.classList.add('sw-stopped');
    });
    btnReset.addEventListener('click', () => {
        clearInterval(swInterval); swRunning = false; swElapsed = 0;
        swDisplay.innerHTML = formatSW(0);
        btnStart.innerHTML = '<span class="material-icons">play_arrow</span> MULAI';
        btnStart.style.display = 'flex'; btnStop.style.display = 'none'; btnReset.style.display = 'none';
        swWrap.classList.remove('sw-running', 'sw-stopped');
    });

    // ===== AKHIRI SESI =====
    document.getElementById('btn-end-session').addEventListener('click', () => {
        if (!ACTIVE_HEAT_ID) {
            showToast('Tidak ada Heat yang aktif untuk diselesaikan!', 'warning');
            return;
        }
        new bootstrap.Modal(document.getElementById('modal-end-session')).show();
    });

    document.getElementById('btn-konfirmasi-end-session').addEventListener('click', () => {
        bootstrap.Modal.getInstance(document.getElementById('modal-end-session'))?.hide();
        
        if (!ACTIVE_HEAT_ID) return;

        // Panggil endpoint selesaikan heat
        fetch(`/heats/${ACTIVE_HEAT_ID}/complete`, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' }
        }).then(r => r.json()).then(res => {
            showToast('Sesi Heat berhasil diselesaikan!', 'success');
            
            // Panggil API untuk hapus seluruh data IoT DB (agar saat buka heat baru layarnya kosong)
            fetch('/results/clear-all', {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': CSRF }
            }).then(() => {
                // UI Cleanup Peserta
                document.getElementById('lane-tbody').innerHTML = `<tr><td colspan="4" class="empty-state" style="padding:40px;">
                    <span class="material-icons">pool</span> Heat telah diakhiri. Aktifkan Heat lain.
                </td></tr>`;
                document.getElementById('ahb-title').innerHTML = '<span class="material-icons">waves</span> Belum ada Heat aktif';
                ACTIVE_HEAT_ID = null;

                // UI Cleanup IoT
                document.getElementById('iot-tbody').innerHTML = `<tr><td colspan="7" class="empty-state" style="padding:30px;">
                    <span class="material-icons">flag</span> Heat telah diselesaikan.
                </td></tr>`;
                
                // Meredundasi beban fetching loadIoTResults sampai heat dihidupkan lagi
                iotHidden = true;
                setTimeout(() => { iotHidden = false; }, 4000); 
                
                loadHeats(); // Muat ulang navigasi kiri
            });
        }).catch(() => showToast('Gagal mengakhiri sesi', 'error'));
    });

    // ===== RESET SESI TERAKHIR (hanya hapus data batch/heat terakhir) =====
    document.getElementById('btn-reset-db').addEventListener('click', () => {
        const bsModal = new bootstrap.Modal(document.getElementById('modal-reset-sesi'));
        bsModal.show();
    });

    document.getElementById('btn-konfirmasi-reset-sesi').addEventListener('click', () => {
        bootstrap.Modal.getInstance(document.getElementById('modal-reset-sesi'))?.hide();

        fetch('/results/clear-last-session', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' }
        })
        .then(r => r.json())
        .then(d => {
            showToast(d.message || '✅ Data sesi terakhir berhasil dihapus!', 'success');
            loadIoTResults();
        })
        .catch(() => showToast('❌ Gagal menghapus data!', 'error'));
    });

    // ===== EXPORT EXCEL =====
    document.getElementById('btn-export-excel').addEventListener('click', () => {
        const rows = document.querySelectorAll('#iot-tbody tr');
        if (rows.length === 0 || (rows.length === 1 && rows[0].querySelector('.empty-state'))) {
            showToast('Tidak ada data untuk di-export!', 'warning'); return;
        }

        // Tampilkan dulu teks di modal apa yang akan terdownload
        const activeTitle  = document.getElementById('ahb-title').textContent.trim();
        const genderSelect = document.getElementById('select-gender');
        const genderText   = genderSelect ? (genderSelect.value === 'L' ? 'Putra' : genderSelect.value === 'P' ? 'Putri' : 'Semua') : 'Semua';
        const eventSelect  = document.getElementById('select-event');
        const eventText    = eventSelect ? (eventSelect.options[eventSelect.selectedIndex]?.text || 'Event') : 'Event';
        const eventClean   = eventText.replace(/^\d+\s*[\-—]\s*/, '').trim();
        const heatMatch    = activeTitle.match(/Heat\s*(\d+)/i);
        const heatLabel    = heatMatch ? `Heat ${heatMatch[1]}` : (activeTitle || 'Heat');
        
        const fileTitle = activeTitle && activeTitle !== 'Belum ada Heat aktif' ? activeTitle : `${eventClean} — ${genderText} — ${heatLabel}`;
        
        document.getElementById('export-modal-filename').textContent = fileTitle;
        
        const exportModal = new bootstrap.Modal(document.getElementById('modal-export-excel'));
        exportModal.show();
    });

    // Proses download setelah konfirmasi
    document.getElementById('btn-konfirmasi-export').addEventListener('click', () => {
        bootstrap.Modal.getInstance(document.getElementById('modal-export-excel'))?.hide();
        
        fetch('/results/data')
            .then(r => r.json())
            .then(data => {
                if (!data || data.length === 0) { showToast('Tidak ada data yang tersedia!', 'warning'); return; }

                const activeTitle  = document.getElementById('ahb-title').textContent.trim();
                const genderSelect = document.getElementById('select-gender');
                const genderText   = genderSelect ? (genderSelect.value === 'L' ? 'Putra' : genderSelect.value === 'P' ? 'Putri' : 'Semua') : 'Semua';
                const eventSelect  = document.getElementById('select-event');
                const eventText    = eventSelect ? (eventSelect.options[eventSelect.selectedIndex]?.text || 'Event') : 'Event';
                const eventClean   = eventText.replace(/^\d+\s*[\-—]\s*/, '').trim();
                const heatMatch    = activeTitle.match(/Heat\s*(\d+)/i);
                const heatLabel    = heatMatch ? `Heat ${heatMatch[1]}` : (activeTitle || 'Heat');
                
                const fileTitle = activeTitle && activeTitle !== 'Belum ada Heat aktif' ? activeTitle : `${eventClean} — ${genderText} — ${heatLabel}`;

                const sortedData   = [...data].sort((a, b) => (a.waktu_ms || 0) - (b.waktu_ms || 0));
                const now     = new Date();
                const tanggal = now.toLocaleDateString('id-ID', { weekday:'long', year:'numeric', month:'long', day:'numeric' });

                const sheetRows = [
                    [`Hasil Waktu Lomba Renang — ${fileTitle}`],
                    [`${heatLabel}`],
                    [`Tanggal: ${tanggal}`],
                    [],
                    ['Rank', 'No. Jalur', 'Nama Atlet', 'Waktu (Menit)', 'Waktu (Detik)', 'Waktu (MS)', 'Waktu Format']
                ];

                sortedData.forEach((item, idx) => {
                    const fmt = item.waktu_format || '-';
                    const parts = fmt.split(':');
                    let menit = '-', detik = '-', ms = '-';
                    if (parts.length === 2) {
                        menit = parts[0];
                        const sub = parts[1].split('.');
                        detik = sub[0]; ms = sub[1] || '-';
                    }
                    sheetRows.push([
                        idx + 1,
                        item.player,
                        item.athlete_name || '-',
                        menit, detik, ms, fmt
                    ]);
                });

                const ws = XLSX.utils.aoa_to_sheet(sheetRows);
                ws['!cols'] = [{wch:5},{wch:8},{wch:25},{wch:13},{wch:13},{wch:10},{wch:15}];
                ws['!merges'] = [
                    {s:{r:0,c:0},e:{r:0,c:6}},
                    {s:{r:1,c:0},e:{r:1,c:6}},
                    {s:{r:2,c:0},e:{r:2,c:6}}
                ];

                const wb = XLSX.utils.book_new();
                XLSX.utils.book_append_sheet(wb, ws, 'Hasil Lomba');

                const filename = `${fileTitle}.xlsx`;
                XLSX.writeFile(wb, filename);
                showToast(`File "${filename}" berhasil didownload!`, 'success');
            })
        .catch(() => showToast('❌ Gagal mengambil data untuk export!', 'error'));
    });

    // ===== INIT =====
    window.addEventListener('load', () => { loadActiveHeat(); loadIoTResults(); });
    setInterval(() => { loadActiveHeat(); loadIoTResults(); }, 2000);
