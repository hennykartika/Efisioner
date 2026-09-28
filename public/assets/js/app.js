(function () {
    'use strict';

    const rupiah = new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        maximumFractionDigits: 0,
    });

    // Ask before destructive or final actions.
    document.querySelectorAll('form[data-confirm]').forEach(function (form) {
        form.addEventListener('submit', function (event) {
            if (!window.confirm(form.dataset.confirm)) {
                event.preventDefault();
            }
        });
    });

    // Reveal the free-text field when "Other" is selected.
    document.querySelectorAll('select[data-other-toggle]').forEach(function (select) {
        const target = document.querySelector(select.dataset.otherToggle);
        if (!target) {
            return;
        }

        const sync = function () {
            target.hidden = select.value !== 'other';
        };

        select.addEventListener('change', sync);
        sync();
    });

    // Live total on the survey form. The server recalculates it; this is only a preview.
    document.querySelectorAll('[data-price-calculator]').forEach(function (container) {
        const count = container.querySelector('[data-price-count]');
        const reward = container.querySelector('[data-price-reward]');
        const output = container.querySelector('[data-price-total]');
        const fee = Number(container.dataset.fee) || 0;

        if (!count || !reward || !output) {
            return;
        }

        const update = function () {
            const respondents = Number.parseInt(count.value, 10);
            const perRespondent = Number.parseInt(reward.value, 10);

            output.textContent = respondents > 0 && perRespondent > 0
                ? rupiah.format(respondents * perRespondent + fee)
                : '-';
        };

        count.addEventListener('input', update);
        reward.addEventListener('input', update);
        update();
    });

    document.querySelectorAll('[data-print]').forEach(function (button) {
        button.addEventListener('click', function () {
            window.print();
        });
    });

    if (window.Chart) {
        const palette = ['#0077b6', '#f4a261', '#2a9d8f', '#e76f51'];

        document.querySelectorAll('canvas[data-chart]').forEach(function (canvas) {
            const data = JSON.parse(canvas.dataset.chart);

            new window.Chart(canvas, {
                type: 'doughnut',
                data: {
                    labels: data.labels,
                    datasets: [{ data: data.values, backgroundColor: palette }],
                },
                options: {
                    maintainAspectRatio: false,
                    plugins: { legend: { position: 'bottom' } },
                },
            });
        });
    }
})();
