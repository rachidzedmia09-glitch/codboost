<?php
/*
Plugin Name: Codboost Plugin
Plugin URI: https://codboost.pro/
Description: Provides the DGTools Project Keystone landing page shortcode.
Version: 1.0.0
Author: Codboost
Author URI: https://codboost.pro/
License: GPL2
*/

if (!defined('ABSPATH')) {
    exit;
}

function dgtools_render_landing_page() {
    static $dgtools_assets_printed = false;
    ob_start();

    if (!$dgtools_assets_printed) {
        $dgtools_assets_printed = true;
        ?>
        <style id="dgtools-landing-style">
            :root {
                --dgtools-dark: #060c1c;
                --dgtools-light: #f4f7ff;
                --dgtools-primary: #5c7cfa;
                --dgtools-secondary: #05d1ff;
                --dgtools-accent: #f6ad55;
                --dgtools-success: #3ddc97;
                --dgtools-danger: #ff5f7e;
                --dgtools-gradient: linear-gradient(120deg, rgba(92, 124, 250, 0.95), rgba(5, 209, 255, 0.85), rgba(246, 173, 85, 0.85));
                --dgtools-glass: rgba(255, 255, 255, 0.12);
                --dgtools-radius: 28px;
                --dgtools-radius-small: 20px;
            }

            .dgtools-landing {
                font-family: 'Poppins', 'Segoe UI', sans-serif;
                color: var(--dgtools-light);
                background: radial-gradient(circle at 10% 20%, rgba(92, 124, 250, 0.3), transparent 45%),
                            radial-gradient(circle at 90% 10%, rgba(5, 209, 255, 0.35), transparent 40%),
                            radial-gradient(circle at 50% 100%, rgba(246, 173, 85, 0.25), transparent 50%),
                            var(--dgtools-dark);
                overflow: hidden;
                position: relative;
                padding: 0;
                margin: 0;
                letter-spacing: 0.02em;
            }

            .dgtools-landing * {
                box-sizing: border-box;
            }

            .dgtools-landing .dgtools-container {
                width: min(1120px, 92vw);
                margin: 0 auto;
                padding: clamp(3rem, 6vw, 6rem) 0;
                position: relative;
            }

            .dgtools-landing .dgtools-grid {
                display: grid;
                gap: clamp(1.5rem, 4vw, 3.5rem);
            }

            .dgtools-landing .dgtools-card {
                background: linear-gradient(145deg, rgba(8, 12, 24, 0.85), rgba(16, 24, 48, 0.8));
                border: 1px solid rgba(255, 255, 255, 0.08);
                box-shadow: 0 25px 60px -30px rgba(0, 0, 0, 0.6);
                border-radius: var(--dgtools-radius);
                padding: clamp(1.75rem, 3.5vw, 2.75rem);
                backdrop-filter: blur(22px);
                position: relative;
                overflow: hidden;
            }

            .dgtools-landing .dgtools-card::before {
                content: '';
                position: absolute;
                inset: 0;
                background: radial-gradient(circle at top right, rgba(92, 124, 250, 0.25), transparent 55%);
                opacity: 0.9;
                pointer-events: none;
            }

            .dgtools-landing h2,
            .dgtools-landing h3 {
                font-weight: 700;
                letter-spacing: 0.03em;
                margin-bottom: 1rem;
            }

            .dgtools-landing p {
                line-height: 1.75;
                color: rgba(244, 247, 255, 0.88);
            }

            .dgtools-landing ul {
                list-style: none;
                padding: 0;
                margin: 0;
            }

            .dgtools-landing li {
                display: flex;
                gap: 0.85rem;
                align-items: flex-start;
                margin-bottom: 1rem;
            }

            .dgtools-landing li::before {
                content: '\2713';
                width: 2rem;
                height: 2rem;
                border-radius: 50%;
                background: linear-gradient(135deg, var(--dgtools-primary), var(--dgtools-secondary));
                display: inline-grid;
                place-items: center;
                font-size: 1.1rem;
                color: #050910;
                flex: 0 0 2rem;
                margin-top: 0.25rem;
                filter: drop-shadow(0 10px 20px rgba(92, 124, 250, 0.35));
            }

            .dgtools-landing .dgtools-hero {
                position: relative;
                overflow: hidden;
            }

            .dgtools-landing .dgtools-hero-content {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
                gap: clamp(2rem, 5vw, 4rem);
                align-items: center;
            }

            .dgtools-landing .dgtools-hero-title {
                font-size: clamp(2.5rem, 5vw, 3.6rem);
                line-height: 1.1;
                margin-bottom: 1.5rem;
                text-transform: uppercase;
                text-shadow: 0 20px 40px rgba(0, 0, 0, 0.45);
            }

            .dgtools-landing .dgtools-hero-highlight {
                display: inline-flex;
                align-items: center;
                gap: 0.85rem;
                padding: 0.7rem 1rem;
                border-radius: 999px;
                background: rgba(92, 124, 250, 0.12);
                border: 1px solid rgba(255, 255, 255, 0.1);
                margin-bottom: 1.75rem;
                font-size: 0.95rem;
                letter-spacing: 0.06em;
                text-transform: uppercase;
            }

            .dgtools-landing .dgtools-hero-visual {
                position: relative;
                padding: 2.5rem;
                border-radius: clamp(2rem, 4vw, 3rem);
                background: linear-gradient(135deg, rgba(92, 124, 250, 0.45), rgba(5, 209, 255, 0.3));
                box-shadow: inset 0 0 0 1px rgba(255, 255, 255, 0.1);
                overflow: hidden;
            }

            .dgtools-landing .dgtools-hero-visual::before,
            .dgtools-landing .dgtools-hero-visual::after {
                content: '';
                position: absolute;
                border-radius: 999px;
                background: rgba(255, 255, 255, 0.15);
                filter: blur(0);
                animation: dgtools-orbit 18s linear infinite;
            }

            .dgtools-landing .dgtools-hero-visual::before {
                width: 120px;
                height: 120px;
                top: -20px;
                right: 18%;
                animation-delay: -6s;
            }

            .dgtools-landing .dgtools-hero-visual::after {
                width: 180px;
                height: 180px;
                bottom: -40px;
                left: 14%;
                animation-delay: -12s;
            }

            .dgtools-landing .dgtools-floating-card {
                position: relative;
                z-index: 2;
                padding: 2rem;
                border-radius: 26px;
                background: rgba(6, 12, 28, 0.88);
                box-shadow: 0 28px 60px -22px rgba(5, 209, 255, 0.55);
                border: 1px solid rgba(255, 255, 255, 0.12);
                backdrop-filter: blur(22px);
                display: grid;
                gap: 1.25rem;
            }

            .dgtools-landing .dgtools-floating-card img {
                width: 190px;
                filter: drop-shadow(0 12px 24px rgba(0, 0, 0, 0.45));
            }

            .dgtools-landing .dgtools-chip-group {
                display: flex;
                flex-wrap: wrap;
                gap: 0.75rem;
            }

            .dgtools-landing .dgtools-chip {
                padding: 0.55rem 1rem;
                border-radius: 999px;
                font-size: 0.85rem;
                background: rgba(255, 255, 255, 0.08);
                border: 1px solid rgba(255, 255, 255, 0.16);
                box-shadow: inset 0 0 0 1px rgba(92, 124, 250, 0.2);
            }

            .dgtools-landing .dgtools-divider {
                height: 1px;
                background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.22), transparent);
                margin: clamp(2.5rem, 5vw, 4rem) 0;
                position: relative;
            }

            .dgtools-landing .dgtools-divider::after {
                content: '';
                position: absolute;
                top: -8px;
                left: 50%;
                transform: translateX(-50%);
                width: 70px;
                height: 70px;
                border-radius: 50%;
                background: radial-gradient(circle, rgba(92, 124, 250, 0.5), transparent 60%);
                filter: blur(12px);
                animation: dgtools-pulse 6s ease-in-out infinite;
            }

            .dgtools-landing .dgtools-panels {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
                gap: clamp(1.5rem, 3vw, 2.25rem);
            }

            .dgtools-landing .dgtools-panel {
                padding: clamp(1.25rem, 2.5vw, 2rem);
                border-radius: var(--dgtools-radius-small);
                border: 1px solid rgba(255, 255, 255, 0.08);
                background: linear-gradient(150deg, rgba(10, 18, 40, 0.92), rgba(12, 20, 44, 0.7));
                position: relative;
                overflow: hidden;
            }

            .dgtools-landing .dgtools-panel::after {
                content: '';
                position: absolute;
                inset: 0;
                background: linear-gradient(135deg, transparent 35%, rgba(92, 124, 250, 0.1));
                opacity: 0;
                transition: opacity 0.4s ease;
            }

            .dgtools-landing .dgtools-panel:hover::after {
                opacity: 1;
            }

            .dgtools-landing .dgtools-panel h4 {
                margin-bottom: 0.65rem;
                font-size: 1.1rem;
                letter-spacing: 0.04em;
            }

            .dgtools-landing .dgtools-panel strong {
                color: var(--dgtools-secondary);
            }

            .dgtools-landing .dgtools-highlight {
                font-size: clamp(1.5rem, 3vw, 2.2rem);
                font-weight: 600;
                margin-bottom: 1rem;
                background: linear-gradient(120deg, var(--dgtools-primary), var(--dgtools-secondary));
                -webkit-background-clip: text;
                color: transparent;
            }

            .dgtools-landing .dgtools-pill {
                display: inline-flex;
                align-items: center;
                gap: 0.6rem;
                padding: 0.6rem 1.1rem;
                border-radius: 999px;
                background: rgba(255, 255, 255, 0.08);
                font-size: 0.85rem;
            }

            .dgtools-landing .dgtools-pill svg {
                width: 18px;
                height: 18px;
            }

            .dgtools-landing .dgtools-persona-switcher {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
                gap: 1.5rem;
                margin-top: 2rem;
            }

            .dgtools-landing .dgtools-persona-card {
                padding: 1.5rem;
                border-radius: 22px;
                border: 1px solid rgba(255, 255, 255, 0.08);
                background: rgba(6, 12, 28, 0.82);
                transition: transform 0.4s ease, box-shadow 0.4s ease, border-color 0.4s ease;
                cursor: pointer;
                position: relative;
            }

            .dgtools-landing .dgtools-persona-card:hover {
                transform: translateY(-8px);
                box-shadow: 0 24px 60px -28px rgba(5, 209, 255, 0.55);
                border-color: rgba(92, 124, 250, 0.5);
            }

            .dgtools-landing .dgtools-persona-card h4 {
                margin: 0 0 0.75rem;
                font-size: 1.05rem;
            }

            .dgtools-landing .dgtools-persona-card .dgtools-badge {
                display: inline-flex;
                align-items: center;
                gap: 0.35rem;
                padding: 0.45rem 0.9rem;
                border-radius: 999px;
                font-size: 0.75rem;
                background: rgba(92, 124, 250, 0.18);
                border: 1px solid rgba(92, 124, 250, 0.32);
                margin-bottom: 0.85rem;
            }

            .dgtools-landing .dgtools-persona-card ul {
                font-size: 0.95rem;
            }

            .dgtools-landing .dgtools-persona-card ul li::before {
                content: '\2022';
                background: none;
                width: auto;
                height: auto;
                color: var(--dgtools-secondary);
                font-size: 1.5rem;
                margin-top: -0.1rem;
                filter: none;
            }

            .dgtools-landing .dgtools-timeline {
                position: relative;
                padding-left: 1.5rem;
            }

            .dgtools-landing .dgtools-timeline::before {
                content: '';
                position: absolute;
                left: 0.7rem;
                top: 0;
                bottom: 0;
                width: 2px;
                background: linear-gradient(180deg, rgba(92, 124, 250, 0.5), rgba(5, 209, 255, 0.2));
            }

            .dgtools-landing .dgtools-timeline-step {
                position: relative;
                margin-bottom: 1.75rem;
                padding-left: 1.5rem;
            }

            .dgtools-landing .dgtools-timeline-step:last-child {
                margin-bottom: 0;
            }

            .dgtools-landing .dgtools-timeline-step::before {
                content: '';
                position: absolute;
                left: -1.2rem;
                top: 0.4rem;
                width: 14px;
                height: 14px;
                border-radius: 50%;
                background: var(--dgtools-secondary);
                box-shadow: 0 0 0 6px rgba(92, 124, 250, 0.2);
            }

            .dgtools-landing .dgtools-stat-grid {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
                gap: clamp(1rem, 2.5vw, 1.75rem);
                margin-top: 2rem;
            }

            .dgtools-landing .dgtools-stat {
                padding: 1.25rem;
                border-radius: 20px;
                background: rgba(6, 12, 28, 0.78);
                border: 1px solid rgba(255, 255, 255, 0.06);
                text-align: center;
                position: relative;
                overflow: hidden;
            }

            .dgtools-landing .dgtools-stat strong {
                display: block;
                font-size: 2rem;
                color: var(--dgtools-secondary);
                letter-spacing: 0.05em;
            }

            .dgtools-landing .dgtools-stat span {
                font-size: 0.9rem;
                text-transform: uppercase;
                letter-spacing: 0.08em;
                color: rgba(244, 247, 255, 0.7);
            }

            .dgtools-landing .dgtools-orbital {
                position: absolute;
                inset: 0;
                pointer-events: none;
            }

            .dgtools-landing .dgtools-orbital span {
                position: absolute;
                width: 14px;
                height: 14px;
                border-radius: 50%;
                background: rgba(92, 124, 250, 0.35);
                box-shadow: 0 0 16px rgba(92, 124, 250, 0.65);
                animation: dgtools-drift 22s linear infinite;
            }

            .dgtools-landing .dgtools-orbital span:nth-child(2) {
                background: rgba(5, 209, 255, 0.45);
                animation-duration: 28s;
                animation-delay: -8s;
            }

            .dgtools-landing .dgtools-orbital span:nth-child(3) {
                background: rgba(246, 173, 85, 0.45);
                animation-duration: 24s;
                animation-delay: -12s;
            }

            .dgtools-landing .dgtools-orbital span:nth-child(4) {
                background: rgba(61, 220, 151, 0.45);
                animation-duration: 32s;
                animation-delay: -18s;
            }

            .dgtools-landing .dgtools-kpi-card {
                border-left: 3px solid var(--dgtools-secondary);
                padding-left: 1.1rem;
            }

            .dgtools-landing .dgtools-floating {
                animation: dgtools-float 10s ease-in-out infinite;
            }

            .dgtools-landing .dgtools-golden-card {
                background: linear-gradient(135deg, rgba(255, 215, 128, 0.18), rgba(255, 173, 73, 0.1));
                border: 1px solid rgba(255, 215, 128, 0.35);
                position: relative;
                overflow: hidden;
            }

            .dgtools-landing .dgtools-golden-card::before {
                content: '';
                position: absolute;
                inset: -60% 20% -60% -20%;
                background: linear-gradient(120deg, transparent 35%, rgba(255, 215, 128, 0.65), transparent 65%);
                animation: dgtools-shine 8s linear infinite;
                opacity: 0.9;
            }

            .dgtools-landing .dgtools-footer {
                text-align: center;
                padding: 3rem 0 4rem;
                color: rgba(244, 247, 255, 0.7);
                font-size: 0.9rem;
            }

            .dgtools-landing a.dgtools-cta {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                gap: 0.75rem;
                padding: 0.85rem 1.8rem;
                border-radius: 999px;
                font-weight: 600;
                text-transform: uppercase;
                letter-spacing: 0.08em;
                background: linear-gradient(135deg, var(--dgtools-primary), var(--dgtools-secondary));
                color: #02050e;
                text-decoration: none;
                box-shadow: 0 25px 45px -22px rgba(92, 124, 250, 0.6);
                transition: transform 0.3s ease, box-shadow 0.3s ease;
            }

            .dgtools-landing a.dgtools-cta:hover {
                transform: translateY(-4px) scale(1.02);
                box-shadow: 0 28px 60px -18px rgba(92, 124, 250, 0.75);
            }

            .dgtools-landing .dgtools-cta svg {
                width: 18px;
                height: 18px;
            }

            @keyframes dgtools-orbit {
                0% {
                    transform: rotate(0deg) translateX(0);
                }
                50% {
                    transform: rotate(180deg) translateX(12px);
                }
                100% {
                    transform: rotate(360deg) translateX(0);
                }
            }

            @keyframes dgtools-drift {
                0% {
                    transform: translate(var(--start-x, 0), var(--start-y, 0)) scale(1);
                    opacity: 0.7;
                }
                50% {
                    transform: translate(var(--mid-x, 30px), var(--mid-y, -40px)) scale(1.2);
                    opacity: 1;
                }
                100% {
                    transform: translate(var(--end-x, -10px), var(--end-y, 20px)) scale(1);
                    opacity: 0.7;
                }
            }

            @keyframes dgtools-float {
                0% {
                    transform: translateY(0px);
                }
                50% {
                    transform: translateY(-12px);
                }
                100% {
                    transform: translateY(0px);
                }
            }

            @keyframes dgtools-pulse {
                0%, 100% {
                    transform: translateX(-50%) scale(0.85);
                    opacity: 0.55;
                }
                50% {
                    transform: translateX(-50%) scale(1.1);
                    opacity: 1;
                }
            }

            @keyframes dgtools-shine {
                0% {
                    transform: translateX(-120%) rotate(12deg);
                }
                60% {
                    transform: translateX(120%) rotate(12deg);
                }
                100% {
                    transform: translateX(120%) rotate(12deg);
                }
            }

            @media (max-width: 768px) {
                .dgtools-landing .dgtools-card {
                    padding: 1.75rem;
                }

                .dgtools-landing .dgtools-hero-visual {
                    padding: 1.75rem;
                }

                .dgtools-landing li::before {
                    margin-top: 0.1rem;
                }
            }
        </style>
        <script id="dgtools-landing-script">
            document.addEventListener('DOMContentLoaded', function () {
                const observerOptions = {
                    root: null,
                    rootMargin: '0px',
                    threshold: 0.2
                };

                const animateOnScroll = (entries, observer) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            entry.target.classList.add('dgtools-in-view');
                            observer.unobserve(entry.target);
                        }
                    });
                };

                const observer = new IntersectionObserver(animateOnScroll, observerOptions);

                document.querySelectorAll('.dgtools-reveal').forEach((section, index) => {
                    section.style.transitionDelay = `${index * 60}ms`;
                    observer.observe(section);
                });

                document.querySelectorAll('.dgtools-stat strong').forEach(stat => {
                    const value = stat.dataset.target;
                    if (!value) {
                        return;
                    }
                    let current = 0;
                    const duration = 2200;
                    const stepTime = 16;
                    const steps = Math.ceil(duration / stepTime);
                    const increment = value / steps;

                    const counter = setInterval(() => {
                        current += increment;
                        if (current >= value) {
                            current = value;
                            clearInterval(counter);
                        }
                        stat.textContent = Math.round(current);
                    }, stepTime);
                });

                const personaCards = document.querySelectorAll('.dgtools-persona-card');
                const personaDetails = document.querySelectorAll('[data-persona-target]');

                personaCards.forEach(card => {
                    card.addEventListener('mouseenter', () => {
                        const target = card.dataset.persona;
                        personaCards.forEach(c => c.classList.toggle('dgtools-active', c === card));
                        personaDetails.forEach(detail => {
                            detail.style.display = detail.dataset.personaTarget === target ? 'grid' : 'none';
                        });
                    });
                });

                if (personaCards.length > 0) {
                    personaCards[0].dispatchEvent(new Event('mouseenter'));
                }
            });
        </script>
        <style id="dgtools-landing-animations">
            .dgtools-reveal {
                opacity: 0;
                transform: translateY(30px) scale(0.98);
                transition: opacity 0.8s ease, transform 0.8s ease;
            }

            .dgtools-reveal.dgtools-in-view {
                opacity: 1;
                transform: translateY(0) scale(1);
            }

            .dgtools-persona-card.dgtools-active {
                border-color: rgba(5, 209, 255, 0.75);
                box-shadow: 0 24px 60px -26px rgba(5, 209, 255, 0.65);
            }
        </style>
        <?php
    }

    ?>
    <div class="dgtools-landing">
        <div class="dgtools-orbital">
            <span style="top: 12%; left: 18%; --start-x: -30px; --start-y: -10px; --mid-x: 25px; --mid-y: 40px; --end-x: 10px; --end-y: -20px;"></span>
            <span style="top: 75%; left: 22%; --start-x: 10px; --start-y: 20px; --mid-x: -25px; --mid-y: -35px; --end-x: 20px; --end-y: 15px;"></span>
            <span style="top: 35%; left: 80%; --start-x: 15px; --start-y: -15px; --mid-x: -30px; --mid-y: 35px; --end-x: 18px; --end-y: -22px;"></span>
            <span style="top: 60%; left: 68%; --start-x: -20px; --start-y: 10px; --mid-x: 30px; --mid-y: -25px; --end-x: -12px; --end-y: 18px;"></span>
        </div>
        <div class="dgtools-container dgtools-hero">
            <div class="dgtools-hero-content dgtools-reveal">
                <div>
                    <div class="dgtools-hero-highlight">Project Keystone | DGTools Market Launch</div>
                    <h1 class="dgtools-hero-title">The Golden Key to Algeria's Digital Universe</h1>
                    <p>DGTools removes borders, complexity, and doubt by delivering the world's most wanted digital services in a trusted, cash-on-delivery gift card experience. We are crafting an Algerian-first solution that unlocks premium subscriptions with a single, beautifully designed access point.</p>
                    <div class="dgtools-chip-group">
                        <span class="dgtools-chip">58 Wilaya Delivery Grid</span>
                        <span class="dgtools-chip">Cash on Delivery Confidence</span>
                        <span class="dgtools-chip">All-in-One Digital Access</span>
                    </div>
                    <div style="margin-top: 2.25rem; display: flex; flex-wrap: wrap; gap: 1rem; align-items: center;">
                        <a class="dgtools-cta" href="#persona-packs">Explore Persona Packs
                            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
                        </a>
                        <span class="dgtools-pill">
                            <svg fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8"></path><path stroke-linecap="round" stroke-linejoin="round" d="M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                            Order. Deliver. Scan. Unlock.
                        </span>
                    </div>
                </div>
                <div class="dgtools-hero-visual dgtools-floating">
                    <div class="dgtools-floating-card">
                        <img src="https://codboost.pro/wp-content/uploads/2025/10/Untitled-design-2025-09-27T062629.647-1.png" alt="DGTools Golden Key Logo" />
                        <div>
                            <strong style="font-size: 1.25rem; letter-spacing: 0.05em;">Mission:</strong>
                            <p>Become Algeria's number one gateway to premium global digital services through trust, convenience, and unbeatable value.</p>
                        </div>
                        <div class="dgtools-chip-group">
                            <span class="dgtools-chip">Netflix</span>
                            <span class="dgtools-chip">Canva Pro</span>
                            <span class="dgtools-chip">ChatGPT Plus</span>
                            <span class="dgtools-chip">Disney+</span>
                            <span class="dgtools-chip">Discord Nitro</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="dgtools-divider"></div>

        <div class="dgtools-container dgtools-grid">
            <div class="dgtools-card dgtools-reveal">
                <h2>01. Mission Statement</h2>
                <p>Our mission is to become Algeria's number one gateway to the world's premium digital services. We eliminate payment barriers, simplify subscription management, and build unshakable trust with a physical gift card experience delivered to every wilaya.</p>
            </div>
            <div class="dgtools-card dgtools-reveal">
                <h2>02. The Problem We Solve</h2>
                <ul>
                    <li><strong>Payment barriers:</strong> Limited access and low trust in international cards.</li>
                    <li><strong>Complexity:</strong> Managing scattered subscriptions is confusing and expensive.</li>
                    <li><strong>Accessibility:</strong> Signing up for global services is cumbersome.</li>
                    <li><strong>Trust:</strong> Consumers hesitate to pay for intangible goods upfront.</li>
                </ul>
            </div>
            <div class="dgtools-card dgtools-reveal">
                <h2>03. Our Solution: DGTools</h2>
                <ul>
                    <li><strong>One card, total access:</strong> Bundle top global platforms into a single subscription.</li>
                    <li><strong>Cash on Delivery:</strong> Payment only when the card is received.</li>
                    <li><strong>Hyper-local delivery:</strong> 58 wilaya distribution network.</li>
                    <li><strong>Simplicity first:</strong> Order → Deliver → Scan → Unlock your digital world.</li>
                </ul>
            </div>
        </div>

        <div class="dgtools-divider"></div>

        <div class="dgtools-container dgtools-card dgtools-reveal" id="persona-packs">
            <div class="dgtools-grid">
                <div>
                    <h2>04. Launch Strategy &amp; Persona Packs</h2>
                    <p>The "Ultimate Persona Pack" campaign rolls out in waves to capture high-intent communities with precision. Each persona receives a curated subscription bundle, irresistible incentives, and content that reflects their passions.</p>
                    <div class="dgtools-persona-switcher">
                        <div class="dgtools-persona-card" data-persona="creator">
                            <div class="dgtools-badge">Phase 1 · Early Adopters</div>
                            <h4>The Creator &amp; Student Pack</h4>
                            <ul>
                                <li>ChatGPT Plus</li>
                                <li>Canva Pro</li>
                                <li>Productivity boosters</li>
                            </ul>
                        </div>
                        <div class="dgtools-persona-card" data-persona="binge">
                            <div class="dgtools-badge">Phase 2 · Mass Appeal</div>
                            <h4>The Binge-Watcher Pack</h4>
                            <ul>
                                <li>Netflix</li>
                                <li>Disney+</li>
                                <li>Crunchyroll</li>
                            </ul>
                        </div>
                        <div class="dgtools-persona-card" data-persona="gamer">
                            <div class="dgtools-badge">Phase 3 · Passion Core</div>
                            <h4>The Gamer Pack</h4>
                            <ul>
                                <li>Game Pass / PS Plus</li>
                                <li>Discord Nitro</li>
                                <li>Premium boosts</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div>
                    <div data-persona-target="creator" class="dgtools-panel" style="display:none;">
                        <div class="dgtools-highlight">Phase 1 · Creator &amp; Student Pack</div>
                        <p>Designed for students, designers, marketers, and young professionals. These influential digital natives instantly recognize value, amplify word-of-mouth, and fuel momentum.</p>
                        <ul>
                            <li>Launch content series: "Top 5 Canva tricks" &amp; "Study smarter with ChatGPT".</li>
                            <li>Daily micro-content powered by AI visuals.</li>
                            <li>Golden Key incentive for the first 50 buyers (10% renewal bonus).</li>
                        </ul>
                    </div>
                    <div data-persona-target="binge" class="dgtools-panel" style="display:none;">
                        <div class="dgtools-highlight">Phase 2 · Binge-Watcher Pack</div>
                        <p>Entertainment-focused households and superfans get one card with every streaming service they crave—no international cards, no hassle.</p>
                        <ul>
                            <li>Bundle includes Netflix, Disney+, Crunchyroll, and rotating local add-ons.</li>
                            <li>Mass-market ads across Facebook, Instagram, and TikTok.</li>
                            <li>Referral rewards for shared viewing circles.</li>
                        </ul>
                    </div>
                    <div data-persona-target="gamer" class="dgtools-panel" style="display:none;">
                        <div class="dgtools-highlight">Phase 3 · Gamer Pack</div>
                        <p>Hardcore gamers unlock a prestige experience with top-tier gaming subscriptions, exclusive community perks, and Discord Nitro upgrades.</p>
                        <ul>
                            <li>Partnerships with local eSports arenas and streamers.</li>
                            <li>Timed drops with limited Golden Key skins.</li>
                            <li>Leaderboards and challenge-based incentives.</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <div class="dgtools-divider"></div>

        <div class="dgtools-container dgtools-grid">
            <div class="dgtools-card dgtools-reveal">
                <h2>05. Team Roles &amp; Responsibilities</h2>
                <div class="dgtools-panels">
                    <div class="dgtools-panel">
                        <h4>Sidali · Product &amp; Strategy</h4>
                        <p>Finalize services, pricing, and margins for each persona pack. Conduct market validation for future bundles. Maintain provider relationships and optimize post-launch offerings.</p>
                    </div>
                    <div class="dgtools-panel">
                        <h4>Chakib &amp; Tahar · Visual Content</h4>
                        <p>Create a dynamic visual identity, AI-powered mockups, lifestyle imagery, and unboxing reels for the "Digital Treasure Chest" experience.</p>
                    </div>
                    <div class="dgtools-panel">
                        <h4>Lotfi · Copy &amp; Community</h4>
                        <p>Craft persuasive ad copy, manage daily posting cadences, engage with communities, and partner with the AI Sales Agent to convert leads in real-time.</p>
                    </div>
                </div>
            </div>
            <div class="dgtools-card dgtools-golden-card dgtools-reveal">
                <h3>The Golden Key Incentive</h3>
                <p>The first 50 customers in each phase receive a limited "Golden Key" card granting 10% off their next renewal. Each order ships within a signature "Digital Treasure Chest"—a premium, sharable unboxing moment engineered for virality.</p>
            </div>
        </div>

        <div class="dgtools-divider"></div>

        <div class="dgtools-container dgtools-card dgtools-reveal">
            <h2>06. Measuring Success</h2>
            <p>We ground every launch in data, iterating quickly to scale what resonates and refine what underperforms.</p>
            <div class="dgtools-panels">
                <div class="dgtools-panel dgtools-kpi-card">
                    <h4>Sales Velocity</h4>
                    <p>Track total cards sold in month one, sliced by wilaya to identify hotspots and expansion opportunities.</p>
                </div>
                <div class="dgtools-panel dgtools-kpi-card">
                    <h4>Cost Per Acquisition</h4>
                    <p>Monitor CPA across channels to keep campaigns efficient while scaling reach.</p>
                </div>
                <div class="dgtools-panel dgtools-kpi-card">
                    <h4>Conversion Rate</h4>
                    <p>Optimize landing funnel conversion from website visit to order confirmation.</p>
                </div>
                <div class="dgtools-panel dgtools-kpi-card">
                    <h4>Engagement Pulse</h4>
                    <p>Measure likes, shares, comments, and follower growth to gauge community traction.</p>
                </div>
            </div>
            <div class="dgtools-stat-grid">
                <div class="dgtools-stat">
                    <strong data-target="58">0</strong>
                    <span>Wilayas Served</span>
                </div>
                <div class="dgtools-stat">
                    <strong data-target="3">0</strong>
                    <span>Launch Phases</span>
                </div>
                <div class="dgtools-stat">
                    <strong data-target="50">0</strong>
                    <span>Golden Keys / Phase</span>
                </div>
                <div class="dgtools-stat">
                    <strong data-target="4">0</strong>
                    <span>Core KPIs</span>
                </div>
            </div>
        </div>

        <div class="dgtools-divider"></div>

        <div class="dgtools-container dgtools-card dgtools-reveal">
            <h2>07. Immediate Next Steps</h2>
            <div class="dgtools-grid" style="grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));">
                <div class="dgtools-panel">
                    <h4>Team Kick-Off</h4>
                    <p>Align on the launch blueprint, gather final feedback, and finalize go/no-go milestones.</p>
                    <div class="dgtools-timeline">
                        <div class="dgtools-timeline-step">
                            <strong>Today</strong>
                            <p>Full team briefing &amp; roadmap confirmation.</p>
                        </div>
                        <div class="dgtools-timeline-step">
                            <strong>+2 Days</strong>
                            <p>Finalize backlog and assign sprint owners.</p>
                        </div>
                    </div>
                </div>
                <div class="dgtools-panel">
                    <h4>Product Finalization</h4>
                    <p>Sidali to confirm service lineup, launch pricing, and profitability for the Creator &amp; Student Pack by end of week.</p>
                    <div class="dgtools-timeline">
                        <div class="dgtools-timeline-step">
                            <strong>Market Pulse</strong>
                            <p>Validate demand and shortlist add-ons for future packs.</p>
                        </div>
                        <div class="dgtools-timeline-step">
                            <strong>Provider Sync</strong>
                            <p>Secure agreements and delivery SLAs.</p>
                        </div>
                    </div>
                </div>
                <div class="dgtools-panel">
                    <h4>Creative &amp; Launch Date</h4>
                    <p>Chakib &amp; Tahar to deliver a mood board and hero assets by early next week. Lock the Phase 1 launch date immediately after review.</p>
                    <div class="dgtools-timeline">
                        <div class="dgtools-timeline-step">
                            <strong>Content Engine</strong>
                            <p>Set up AI asset pipeline for daily social drops.</p>
                        </div>
                        <div class="dgtools-timeline-step">
                            <strong>Ad Deployment</strong>
                            <p>Lotfi to launch the first flight of ads and monitor live feedback with the AI Sales Agent.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="dgtools-footer">
            &copy; <?php echo date('Y'); ?> DGTools · Project Keystone · Unlock Algeria's premium digital future.
        </div>
    </div>
    <?php

    return ob_get_clean();
}

add_shortcode('dgtools_landing_page', 'dgtools_render_landing_page');
