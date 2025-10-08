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
                    borderColor: '#16a34a',
                    backgroundColor: 'rgba(22, 163, 74, 0.1)',
                    tension: 0.35,
                    pointRadius: 4,
                    borderWidth: 3,
                },
                {
                    label: settings.i18n ? settings.i18n.outcome : 'Outcome',
                    data: data.outcome,
                    fill: false,
                    borderColor: '#ef4444',
                    backgroundColor: 'rgba(239, 68, 68, 0.1)',
                    tension: 0.35,
                    pointRadius: 4,
                    borderDash: [6, 4],
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
                        grid: {
                            drawBorder: false,
                        },
                    },
                    x: {
                        grid: {
                            display: false,
                        },
                    },
                },
            },
        });
    }

    function buildBalanceChart(ctx, data) {
        if (!ctx || !data) {
            return;
        }

        return new window.Chart(ctx, {
            type: 'line',
            data: {
                labels: data.labels,
                datasets: [
                    {
                        label: 'Cumulative Balance',
                        data: data.cumulative,
                        fill: true,
                        borderColor: '#1414ff',
                        backgroundColor: 'rgba(20, 20, 255, 0.12)',
                        tension: 0.35,
                        pointRadius: 0,
                        borderWidth: 3,
                    },
                ],
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        mode: 'index',
                        intersect: false,
                    },
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: (value) => new Intl.NumberFormat().format(value),
                        },
                        grid: { drawBorder: false },
                    },
                    x: {
                        grid: { display: false },
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

    function buildReasonChart(ctx, reasonData) {
        if (!ctx || !reasonData || !reasonData.length) {
            return;
        }

        const labels = reasonData.map((item) => item.reason);
        const netValues = reasonData.map((item) => item.net);

        return new window.Chart(ctx, {
            type: 'bar',
            data: {
                labels,
                datasets: [
                    {
                        label: 'Net Impact',
                        data: netValues,
                        backgroundColor: netValues.map((value) => (value >= 0 ? 'rgba(22, 163, 74, 0.8)' : 'rgba(239, 68, 68, 0.8)')),
                        borderRadius: 8,
                    },
                ],
            },
            options: {
                responsive: true,
                indexAxis: 'y',
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: (context) => new Intl.NumberFormat().format(context.parsed.x),
                        },
                    },
                },
                scales: {
                    x: {
                        grid: { drawBorder: false },
                        ticks: {
                            callback: (value) => new Intl.NumberFormat().format(value),
                        },
                    },
                    y: {
                        grid: { display: false },
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
        const balanceCanvas = document.getElementById('cbm-balance-chart');
        const reasonCanvas = document.getElementById('cbm-reason-chart');

        buildCashflowChart(cashflowCanvas, settings.analytics);
        buildAccountsChart(accountsCanvas, settings.analytics.accountsDistribution);
        buildBalanceChart(balanceCanvas, settings.analytics);
        buildReasonChart(reasonCanvas, settings.analytics.reasonBreakdown);
    });
})(jQuery);
