(function ($) {
    'use strict';

    const settings = window.codboostMoneyManager || {};

    function buildCashflowChart(ctx, data) {
        if (!ctx || !data) {
            return;
        }

        const chartData = {
            labels: data.labels,
            datasets: [
                {
                    label: settings.i18n ? settings.i18n.income : 'Income',
                    data: data.income,
                    fill: false,
                    borderColor: '#0f766e',
                    backgroundColor: 'rgba(15, 118, 110, 0.2)',
                    tension: 0.35,
                    pointRadius: 4,
                },
                {
                    label: settings.i18n ? settings.i18n.outcome : 'Outcome',
                    data: data.outcome,
                    fill: false,
                    borderColor: '#dc2626',
                    backgroundColor: 'rgba(220, 38, 38, 0.2)',
                    tension: 0.35,
                    pointRadius: 4,
                },
            ],
        };

        return new window.Chart(ctx, {
            type: 'line',
            data: chartData,
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            usePointStyle: true,
                        },
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false,
                    },
                },
                interaction: {
                    mode: 'nearest',
                    axis: 'x',
                    intersect: false,
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: (value) => new Intl.NumberFormat().format(value),
                        },
                    },
                },
            },
        });
    }

    function buildAccountsChart(ctx, distribution) {
        if (!ctx || !distribution || !distribution.length) {
            return;
        }

        const labels = distribution.map((item) => item.account || 'Unassigned');
        const values = distribution.map((item) => item.balance);
        const palette = ['#1414ff', '#d7ff00', '#0f766e', '#dc2626', '#2563eb', '#9333ea'];

        return new window.Chart(ctx, {
            type: 'doughnut',
            data: {
                labels,
                datasets: [
                    {
                        data: values,
                        backgroundColor: labels.map((_, index) => palette[index % palette.length]),
                        borderWidth: 0,
                    },
                ],
            },
            options: {
                cutout: '65%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 16,
                            usePointStyle: true,
                        },
                    },
                },
            },
        });
    }

    $(function () {
        if (!settings.analytics) {
            return;
        }

        const cashflowCanvas = document.getElementById('cbm-cashflow-chart');
        const accountsCanvas = document.getElementById('cbm-accounts-chart');

        buildCashflowChart(cashflowCanvas, settings.analytics);
        buildAccountsChart(accountsCanvas, settings.analytics.accountsDistribution);
    });
})(jQuery);
