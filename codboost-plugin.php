<?php
/*
Plugin Name: Codboost Plugin
Plugin URI: https://codboost.pro/
Description: DG Network 2025 landing experience.
Version: 1.0.0
Author: Codboost
Author URI: https://codboost.pro/
License: GPL2
*/

if (!defined('ABSPATH')) {
    exit;
}

add_action('init', function () {
    add_shortcode('dg_network_plan', 'codboost_render_dg_network_plan');
});

add_action('wp_enqueue_scripts', function () {
    if (!is_singular()) {
        return;
    }

    global $post;
    if (!has_shortcode($post->post_content, 'dg_network_plan')) {
        return;
    }

    $handle = 'dg-network-plan';
    wp_register_style(
        $handle,
        plugins_url('assets/css/dg-network-plan.css', __FILE__),
        [],
        filemtime(__DIR__ . '/assets/css/dg-network-plan.css')
    );

    wp_register_script(
        $handle,
        plugins_url('assets/js/dg-network-plan.js', __FILE__),
        [],
        filemtime(__DIR__ . '/assets/js/dg-network-plan.js'),
        true
    );

    wp_enqueue_style($handle);
    wp_enqueue_script($handle);
});

function codboost_render_dg_network_plan(): string
{
    ob_start();
    ?>
    <div class="dg-network-shell" data-theme="light">
        <div class="dg-hero" data-parallax>
            <div class="dg-hero__bg" aria-hidden="true"></div>
            <div class="dg-hero__content">
                <h1>DG Network — Digital Marketing &amp; Brand Identity Plan (2025 Edition)</h1>
                <button type="button" class="dg-theme-toggle" aria-pressed="false">
                    <span class="dg-theme-toggle__label">Toggle dark mode</span>
                </button>
                <div class="dg-scroll-indicator" role="presentation">
                    <span class="dg-scroll-indicator__bar"></span>
                </div>
            </div>
        </div>
        <article class="dg-plan" data-animate>
            <section class="dg-section" id="section-1" data-index="1">
                <h2>1. Executive Overview</h2>
                <p>DG Network is a multi-niche digital media ecosystem that connects Algerian youth through entertainment, culture, and technology.</p>
                <p>The network operates across five verticals: Sports, Tech, Movies, Anime, and Gaming — each with its own identity but unified under one brand vision.</p>
                <p>Goal (2025): Build Algeria’s most engaging content ecosystem by leveraging consistent daily content, AI-driven automation, and strong community engagement.</p>
            </section>
            <section class="dg-section" id="section-2" data-index="2">
                <h2>2. Vision &amp; Mission</h2>
                <h3>Vision:</h3>
                <p>To become the #1 digital content network in North Africa, inspiring the next generation of creators and audiences through innovation and storytelling.</p>
                <h3>Mission:</h3>
                <p>To deliver daily, high-value content that informs, entertains, and connects audiences organically while maintaining authenticity and creative excellence.</p>
            </section>
            <section class="dg-section" id="section-3" data-index="3">
                <h2>3. Brand Ecosystem</h2>
                <p>DG Network unites five key brands under one creative umbrella:</p>
                <div class="dg-table" role="table">
                    <div class="dg-table__row" role="row">
                        <span role="columnheader">Brand</span>
                        <span role="columnheader">Niche</span>
                        <span role="columnheader">Purpose</span>
                    </div>
                    <div class="dg-table__row" role="row">
                        <span role="cell">DGSPORTS</span>
                        <span role="cell">Sports &amp; football culture</span>
                        <span role="cell">Showcase Algerian and global sports moments.</span>
                    </div>
                    <div class="dg-table__row" role="row">
                        <span role="cell">DGTECH</span>
                        <span role="cell">Technology &amp; innovation</span>
                        <span role="cell">Simplify tech for youth and professionals.</span>
                    </div>
                    <div class="dg-table__row" role="row">
                        <span role="cell">DGMOVIES</span>
                        <span role="cell">Cinema &amp; TV series</span>
                        <span role="cell">Explore film culture, reviews, and storytelling.</span>
                    </div>
                    <div class="dg-table__row" role="row">
                        <span role="cell">DGANIME</span>
                        <span role="cell">Anime &amp; manga</span>
                        <span role="cell">Celebrate anime fandom and community debates.</span>
                    </div>
                    <div class="dg-table__row" role="row">
                        <span role="cell">DGGAMING</span>
                        <span role="cell">Gaming &amp; esports</span>
                        <span role="cell">Connect gamers and showcase gameplay culture.</span>
                    </div>
                </div>
                <p>Each sub-brand maintains its own color identity, tone, and target audience, while all share the DG Network design language.</p>
            </section>
            <section class="dg-section" id="section-4" data-index="4">
                <h2>4. Target Audience</h2>
                <ul>
                    <li>Age: 15–35 years</li>
                    <li>Location: Algeria, Tunisia, Morocco, French-speaking Africa</li>
                    <li>Psychographics: Trend-seekers, gamers, film lovers, sports fans, and tech enthusiasts</li>
                    <li>Habits: Daily users of TikTok, Instagram, and YouTube; engaged in online communities; prefer visual and fast content.</li>
                </ul>
            </section>
            <section class="dg-section" id="section-5" data-index="5">
                <h2>5. Brand Tone &amp; Identity</h2>
                <p>DG Network communicates with a tone that is:</p>
                <ul>
                    <li>Authentic: Feels real and grounded in Algerian culture.</li>
                    <li>Energetic: Quick, bold, and driven by emotion.</li>
                    <li>Youthful: Speaks the language of trends, humor, and curiosity.</li>
                    <li>Innovative: Always experimenting with new content formats and ideas.</li>
                </ul>
            </section>
            <section class="dg-section" id="section-6" data-index="6">
                <h2>6. Platform Strategy</h2>
                <p>Each niche builds its audience across five major platforms:</p>
                <div class="dg-table" role="table">
                    <div class="dg-table__row" role="row">
                        <span role="columnheader">Platform</span>
                        <span role="columnheader">Objective</span>
                        <span role="columnheader">Content Type</span>
                        <span role="columnheader">Frequency</span>
                    </div>
                    <div class="dg-table__row" role="row">
                        <span role="cell">TikTok</span>
                        <span role="cell">Reach &amp; virality</span>
                        <span role="cell">Short-form trends, edits</span>
                        <span role="cell">2 videos/day</span>
                    </div>
                    <div class="dg-table__row" role="row">
                        <span role="cell">Instagram</span>
                        <span role="cell">Visual storytelling</span>
                        <span role="cell">Reels, carousels, stories</span>
                        <span role="cell">1–2 posts/day</span>
                    </div>
                    <div class="dg-table__row" role="row">
                        <span role="cell">YouTube</span>
                        <span role="cell">Long-term growth</span>
                        <span role="cell">Shorts + long-form analysis</span>
                        <span role="cell">1 short/day + 1 long/week</span>
                    </div>
                    <div class="dg-table__row" role="row">
                        <span role="cell">Facebook</span>
                        <span role="cell">Community engagement</span>
                        <span role="cell">Memes, posts, videos</span>
                        <span role="cell">1–2 posts/day</span>
                    </div>
                    <div class="dg-table__row" role="row">
                        <span role="cell">Telegram</span>
                        <span role="cell">Retention &amp; loyalty</span>
                        <span role="cell">Exclusive updates, polls</span>
                        <span role="cell">3–5 posts/week</span>
                    </div>
                </div>
            </section>
            <section class="dg-section" id="section-7" data-index="7">
                <h2>7. Content Strategy</h2>
                <p>Core Rule: Create once, repurpose everywhere.</p>
                <p>Each video is reformatted for every platform — from one master clip to Reels, Shorts, and TikToks.</p>
                <p>Weekly Content Pillars:</p>
                <ul>
                    <li>DGSPORTS: Highlights, rivalries, skills, and player stories.</li>
                    <li>DGTECH: Product tips, comparisons, AI news, and how-tos.</li>
                    <li>DGMOVIES: Reviews, scene breakdowns, cinematic edits.</li>
                    <li>DGANIME: Top lists, debates, character stories, community reactions.</li>
                    <li>DGGAMING: Challenges, tutorials, humor, and live event coverage.</li>
                </ul>
            </section>
            <section class="dg-section" id="section-8" data-index="8">
                <h2>8. Monthly Content Calendar (2025 Launch Schedule)</h2>
                <div class="dg-table" role="table">
                    <div class="dg-table__row" role="row">
                        <span role="columnheader">Week</span>
                        <span role="columnheader">Focus</span>
                        <span role="columnheader">Example Theme</span>
                        <span role="columnheader">Deliverables</span>
                    </div>
                    <div class="dg-table__row" role="row">
                        <span role="cell">Week 1</span>
                        <span role="cell">Launch &amp; Awareness</span>
                        <span role="cell">“Welcome to DG Network”</span>
                        <span role="cell">5 Reels + 2 YouTube Shorts</span>
                    </div>
                    <div class="dg-table__row" role="row">
                        <span role="cell">Week 2</span>
                        <span role="cell">Engagement Boost</span>
                        <span role="cell">“Top 5 Moments” Challenge</span>
                        <span role="cell">10 short videos + 1 campaign post</span>
                    </div>
                    <div class="dg-table__row" role="row">
                        <span role="cell">Week 3</span>
                        <span role="cell">Collaboration</span>
                        <span role="cell">Partner with local influencers</span>
                        <span role="cell">5 collab Reels + 1 group post</span>
                    </div>
                    <div class="dg-table__row" role="row">
                        <span role="cell">Week 4</span>
                        <span role="cell">Retention</span>
                        <span role="cell">“Community Picks of the Month”</span>
                        <span role="cell">1 long-form recap + 5 Shorts</span>
                    </div>
                </div>
                <p>Monthly Objective:</p>
                <ul>
                    <li>100+ total posts (across all platforms)</li>
                    <li>8–10 viral content experiments</li>
                    <li>+10% audience growth across niches</li>
                </ul>
            </section>
            <section class="dg-section" id="section-9" data-index="9">
                <h2>9. AI &amp; Automation Stack</h2>
                <p>DG Network uses a modern workflow to automate content creation, scheduling, and optimization:</p>
                <ul>
                    <li>Planning: Notion, Trello</li>
                    <li>Scripting &amp; Captions: ChatGPT, Jasper AI</li>
                    <li>Design: Canva Pro, Figma templates</li>
                    <li>Video Editing: CapCut, Premiere Pro</li>
                    <li>Scheduling: Meta Suite, YouTube Studio</li>
                    <li>Analytics: Google Sheets Dashboard + platform insights</li>
                </ul>
            </section>
            <section class="dg-section" id="section-10" data-index="10">
                <h2>10. Growth Roadmap (Q1–Q4 2025)</h2>
                <div class="dg-table" role="table">
                    <div class="dg-table__row" role="row">
                        <span role="columnheader">Quarter</span>
                        <span role="columnheader">Focus</span>
                        <span role="columnheader">Target</span>
                        <span role="columnheader">Key Action</span>
                    </div>
                    <div class="dg-table__row" role="row">
                        <span role="cell">Q1</span>
                        <span role="cell">Launch &amp; Awareness</span>
                        <span role="cell">25K followers</span>
                        <span role="cell">Establish brand identity, test formats</span>
                    </div>
                    <div class="dg-table__row" role="row">
                        <span role="cell">Q2</span>
                        <span role="cell">Expansion</span>
                        <span role="cell">60K followers</span>
                        <span role="cell">Collaborations &amp; cross-promotion</span>
                    </div>
                    <div class="dg-table__row" role="row">
                        <span role="cell">Q3</span>
                        <span role="cell">Monetization</span>
                        <span role="cell">100K followers</span>
                        <span role="cell">Activate brand deals, affiliate products</span>
                    </div>
                    <div class="dg-table__row" role="row">
                        <span role="cell">Q4</span>
                        <span role="cell">Community Scaling</span>
                        <span role="cell">150K+ followers</span>
                        <span role="cell">Launch DG app &amp; merch drops</span>
                    </div>
                </div>
            </section>
            <section class="dg-section" id="section-11" data-index="11">
                <h2>11. Viral Campaign Framework</h2>
                <p>Each month features a branded campaign under one of the niches, e.g.:</p>
                <ul>
                    <li>#DGDerbyWeek (Sports)</li>
                    <li>#DGTechHack (Tech)</li>
                    <li>#DGMovieGem (Movies)</li>
                    <li>#DGAnimeArc (Anime)</li>
                    <li>#DGGamingRush (Gaming)</li>
                </ul>
                <p>Process:</p>
                <ol>
                    <li>Define theme &amp; hashtag.</li>
                    <li>Launch 5 short videos.</li>
                    <li>Activate user-generated content (UGC).</li>
                    <li>Repost &amp; feature best entries.</li>
                </ol>
            </section>
            <section class="dg-section" id="section-12" data-index="12">
                <h2>12. Monetization Plan</h2>
                <p>DG Network monetizes through multiple revenue streams:</p>
                <ul>
                    <li>Brand Sponsorships &amp; Ads – Collaborate with tech, sports, and entertainment brands.</li>
                    <li>Affiliate Marketing – Tech products, streaming platforms, gaming gear.</li>
                    <li>Ticketing Partnerships – Sports events, conventions, screenings.</li>
                    <li>Digital Products – Templates, guides, presets.</li>
                    <li>Merchandise Drops – Branded hoodies, caps, accessories.</li>
                </ul>
            </section>
            <section class="dg-section" id="section-13" data-index="13">
                <h2>13. Team Workflow</h2>
                <p>DG Network functions as a structured content agency:</p>
                <ul>
                    <li>Strategist: Sets goals, analytics, and campaigns.</li>
                    <li>Content Creator: Produces daily videos and captions.</li>
                    <li>Editor: Cuts and optimizes content for all platforms.</li>
                    <li>Designer: Maintains brand visuals and thumbnails.</li>
                    <li>Community Manager: Manages posting, replies, and engagement.</li>
                </ul>
                <p>Workflow Cycle:</p>
                <p>Plan → Create → Edit → Review → Publish → Analyze</p>
            </section>
            <section class="dg-section" id="section-14" data-index="14">
                <h2>14. KPIs &amp; Analytics</h2>
                <p>Key performance indicators are tracked weekly and monthly:</p>
                <ul>
                    <li>Audience Growth (followers/subscribers)</li>
                    <li>Engagement Rate (likes, comments, shares)</li>
                    <li>Watch Time (video performance)</li>
                    <li>Conversion (Telegram group join or CTA clicks)</li>
                    <li>ROI per campaign (ads vs. reach)</li>
                </ul>
            </section>
            <section class="dg-section" id="section-15" data-index="15">
                <h2>15. Future Objectives (2026 Preview)</h2>
                <ul>
                    <li>Expand to two new niches: DGFOOD and DGTRAVEL.</li>
                    <li>Develop DG Academy — a content creator learning platform.</li>
                    <li>Launch DG App for exclusive content and loyalty rewards.</li>
                    <li>Establish DG Network Algeria HQ studio for in-house production.</li>
                </ul>
            </section>
            <section class="dg-section" id="section-16" data-index="16">
                <h2>16. Core Values</h2>
                <ul>
                    <li>Consistency: Daily output builds audience trust.</li>
                    <li>Authenticity: Speak in the real voice of the Algerian youth.</li>
                    <li>Creativity: Never recycle; always innovate.</li>
                    <li>Community: Build belonging before monetization.</li>
                </ul>
            </section>
            <section class="dg-section" id="section-17" data-index="17">
                <h2>17. Closing Statement</h2>
                <p>DG Network is more than content — it’s a new creative economy built for Algeria’s digital generation.</p>
                <p>By combining storytelling, strategy, and technology, we’re not just building pages — we’re building a movement.</p>
                <p>“Consistency. Creativity. Connection.” — DG Network 2025</p>
            </section>
        </article>
        <aside class="dg-toc" data-sticky>
            <button type="button" class="dg-command" popovertarget="dg-command-palette" popovertargetaction="toggle">Open command palette</button>
            <nav aria-label="Sections">
                <h2 class="screen-reader-text">Plan sections</h2>
                <ol class="dg-toc__list"></ol>
            </nav>
        </aside>
        <div id="dg-command-palette" popover="manual" class="dg-command-palette" role="dialog" aria-modal="false">
            <div class="dg-command-palette__header">
                <label for="dg-command-search" class="screen-reader-text">Search sections</label>
                <input id="dg-command-search" type="search" placeholder="Search sections" autocomplete="off" />
                <button type="button" class="dg-command-close" popovertarget="dg-command-palette" popovertargetaction="hide">Close</button>
            </div>
            <ul class="dg-command-results" role="listbox" aria-label="Sections"></ul>
        </div>
    </div>
    <?php
    return ob_get_clean();
}
