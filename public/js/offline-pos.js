(function () {
    const form = document.getElementById('form');
    if (!form) return;

    const STORAGE_KEY = 'pos_offline_queue';
    const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || form.querySelector('input[name="_token"]')?.value;

    function loadQueue() {
        try {
            return JSON.parse(localStorage.getItem(STORAGE_KEY)) || [];
        } catch (e) {
            return [];
        }
    }

    function saveQueue(queue) {
        localStorage.setItem(STORAGE_KEY, JSON.stringify(queue));
    }

    function notify(msg, type = 'info') {
        const alert = document.createElement('div');
        alert.className = `alert alert-${type}`;
        alert.textContent = msg;
        document.body.prepend(alert);
        setTimeout(() => alert.remove(), 4000);
    }

    function buildFormData(entries) {
        const fd = new FormData();
        entries.forEach(({ name, value }) => fd.append(name, value));
        return fd;
    }

    async function syncQueue() {
        const queue = loadQueue();
        if (!queue.length || !navigator.onLine) return;

        const remaining = [];
        for (const payload of queue) {
            try {
                const body = buildFormData(payload.entries);
                const res = await fetch(form.action, {
                    method: 'POST',
                    credentials: 'same-origin',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': csrf,
                    },
                    body,
                });
                if (!res.ok) throw new Error('failed');
            } catch (err) {
                remaining.push(payload);
            }
        }
        saveQueue(remaining);
        if (queue.length && !remaining.length) {
            notify('تمت مزامنة فواتير نقاط البيع العالقة بعد عودة الاتصال', 'success');
        }
    }

    function serializeForm(formEl) {
        const fd = new FormData(formEl);
        if (!fd.get('POS')) fd.set('POS', '1');
        const entries = [];
        fd.forEach((value, key) => {
            entries.push({ name: key, value });
        });
        return entries;
    }

    form.addEventListener('submit', function (e) {
        if (navigator.onLine) return;

        e.preventDefault();
        const queue = loadQueue();
        queue.push({
            createdAt: new Date().toISOString(),
            entries: serializeForm(form)
        });
        saveQueue(queue);
        notify('لا يوجد اتصال بالإنترنت. تم حفظ الفاتورة وسيتم إرسالها تلقائياً عند عودة الاتصال.', 'warning');
        form.reset();
    });

    window.addEventListener('online', syncQueue);
    document.addEventListener('DOMContentLoaded', syncQueue);
})();
