<?php
/**
 * The front page template for Med Express Delivery.
 *
 * @package MedExpress
 */

get_header();

$hero_headline   = get_theme_mod( 'medexpress_hero_headline', __( 'Delivery without boundaries across Algeria', 'med-express' ) );
$hero_sub        = get_theme_mod( 'medexpress_hero_subheadline', __( 'Same-day pickup, real-time tracking, and COD reconciliation for Algerian eCommerce brands.', 'med-express' ) );
$hero_primary    = get_theme_mod( 'medexpress_hero_primary_text', __( 'Get a Quote', 'med-express' ) );
$hero_primary_url = get_theme_mod( 'medexpress_hero_primary_link', '#contact' );
$hero_secondary  = get_theme_mod( 'medexpress_hero_secondary_text', __( 'Track a Parcel', 'med-express' ) );
$hero_secondary_url = get_theme_mod( 'medexpress_hero_secondary_link', '#' );
$hero_style      = medexpress_get_hero_background_style();
?>
<section class="hero" <?php echo $hero_style; ?>>
    <div class="container">
        <div class="hero__content">
            <h1><?php echo esc_html( $hero_headline ); ?></h1>
            <p><?php echo wp_kses_post( $hero_sub ); ?></p>
            <div class="hero__actions">
                <a class="button button--primary" href="<?php echo esc_url( $hero_primary_url ); ?>"><?php echo esc_html( $hero_primary ); ?></a>
                <a class="button button--ghost" href="<?php echo esc_url( $hero_secondary_url ); ?>"><?php echo esc_html( $hero_secondary ); ?></a>
            </div>
            <div class="hero__stats">
                <div class="stat">
                    <div class="stat__value">98%</div>
                    <div class="stat__label"><?php esc_html_e( 'On-time deliveries', 'med-express' ); ?></div>
                </div>
                <div class="stat">
                    <div class="stat__value">48h</div>
                    <div class="stat__label"><?php esc_html_e( 'Average COD payout', 'med-express' ); ?></div>
                </div>
                <div class="stat">
                    <div class="stat__value">27+</div>
                    <div class="stat__label"><?php esc_html_e( 'Wilayas covered nationwide', 'med-express' ); ?></div>
                </div>
                <div class="stat">
                    <div class="stat__value">120k</div>
                    <div class="stat__label"><?php esc_html_e( 'Parcels delivered in 2023', 'med-express' ); ?></div>
                </div>
            </div>
        </div>
        <div class="hero__card">
            <img src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/medexpress-badge.svg' ); ?>" alt="<?php esc_attr_e( 'Med Express Delivery badge', 'med-express' ); ?>" width="120" height="120" />
            <h2><?php esc_html_e( 'Trusted delivery partner for Algerian businesses', 'med-express' ); ?></h2>
            <ul>
                <li><?php esc_html_e( 'Dedicated account manager and support in Arabic & French', 'med-express' ); ?></li>
                <li><?php esc_html_e( 'Cash on Delivery, pre-paid and subscription-friendly workflows', 'med-express' ); ?></li>
                <li><?php esc_html_e( 'Dynamic routing technology to minimise failed deliveries', 'med-express' ); ?></li>
            </ul>
        </div>
    </div>
</section>

<section class="section" id="services">
    <div class="container">
        <div class="section__header">
            <h2><?php esc_html_e( 'Solutions built for growth', 'med-express' ); ?></h2>
            <p><?php esc_html_e( 'Med Express combines nationwide coverage with technology-enabled workflows tailored for Algerian retailers and startups.', 'med-express' ); ?></p>
        </div>
        <div class="grid grid--3">
            <div class="card">
                <div class="card__icon dashicons dashicons-cart"></div>
                <h3><?php esc_html_e( 'eCommerce Fulfilment', 'med-express' ); ?></h3>
                <p><?php esc_html_e( 'Same-day pickup from your store or warehouse with digital proof of delivery sent instantly to your dashboard.', 'med-express' ); ?></p>
            </div>
            <div class="card">
                <div class="card__icon dashicons dashicons-money"></div>
                <h3><?php esc_html_e( 'Cash-on-Delivery Experts', 'med-express' ); ?></h3>
                <p><?php esc_html_e( 'Transparent COD reconciliation with payouts in 24-48h and automated SMS notifications for your customers.', 'med-express' ); ?></p>
            </div>
            <div class="card">
                <div class="card__icon dashicons dashicons-randomize"></div>
                <h3><?php esc_html_e( 'Reverse Logistics', 'med-express' ); ?></h3>
                <p><?php esc_html_e( 'Streamlined returns and exchanges with quality control to keep your buyers delighted and loyal.', 'med-express' ); ?></p>
            </div>
        </div>
    </div>
</section>

<section class="section">
    <div class="container process">
        <div class="section__header">
            <h2><?php esc_html_e( 'From order to doorstep in four steps', 'med-express' ); ?></h2>
            <p><?php esc_html_e( 'Our operations team orchestrates each shipment with proactive communication at every milestone.', 'med-express' ); ?></p>
        </div>
        <div class="process__steps">
            <div class="process__step">
                <h3><?php esc_html_e( '01. Smart pickup scheduling', 'med-express' ); ?></h3>
                <p><?php esc_html_e( 'Book pickups online or via WhatsApp and get notified when our courier is on the way.', 'med-express' ); ?></p>
            </div>
            <div class="process__step">
                <h3><?php esc_html_e( '02. Secure parcel handling', 'med-express' ); ?></h3>
                <p><?php esc_html_e( 'Parcels are sealed and scanned into our network with live tracking updates for you and your customer.', 'med-express' ); ?></p>
            </div>
            <div class="process__step">
                <h3><?php esc_html_e( '03. Dynamic routing', 'med-express' ); ?></h3>
                <p><?php esc_html_e( 'AI-assisted routes and bilingual couriers ensure successful deliveries even in hard-to-find areas.', 'med-express' ); ?></p>
            </div>
            <div class="process__step">
                <h3><?php esc_html_e( '04. Cash reconciliation', 'med-express' ); ?></h3>
                <p><?php esc_html_e( 'Reconcile COD payments through your dashboard with transparent statements and quick transfers.', 'med-express' ); ?></p>
            </div>
        </div>
    </div>
</section>

<section class="section" id="industries">
    <div class="container">
        <div class="section__header">
            <h2><?php esc_html_e( 'Tailored for the industries driving Algeria forward', 'med-express' ); ?></h2>
            <p><?php esc_html_e( 'From fashion to pharmaceuticals, Med Express adapts to your compliance and customer experience requirements.', 'med-express' ); ?></p>
        </div>
        <div class="grid grid--3">
            <div class="card">
                <div class="card__icon dashicons dashicons-admin-users"></div>
                <h3><?php esc_html_e( 'Fashion & Beauty', 'med-express' ); ?></h3>
                <p><?php esc_html_e( 'Flexible delivery windows, fitting-time waiting, and easy exchanges keep your customers stylish.', 'med-express' ); ?></p>
            </div>
            <div class="card">
                <div class="card__icon dashicons dashicons-heart"></div>
                <h3><?php esc_html_e( 'Health & Wellness', 'med-express' ); ?></h3>
                <p><?php esc_html_e( 'Temperature-controlled handling, pharmacist-trained couriers, and discreet delivery options.', 'med-express' ); ?></p>
            </div>
            <div class="card">
                <div class="card__icon dashicons dashicons-store"></div>
                <h3><?php esc_html_e( 'Grocery & FMCG', 'med-express' ); ?></h3>
                <p><?php esc_html_e( 'Real-time inventory sync and multi-drop routes for perishables, beverages, and household goods.', 'med-express' ); ?></p>
            </div>
        </div>
    </div>
</section>

<section class="section">
    <div class="container testimonials">
        <div class="section__header">
            <h2><?php esc_html_e( 'What Algerian founders say about Med Express', 'med-express' ); ?></h2>
            <p><?php esc_html_e( 'Stories from partners who scale with confidence thanks to reliable fulfilment.', 'med-express' ); ?></p>
        </div>
        <div class="grid grid--3">
            <div class="testimonial">
                <p>“<?php esc_html_e( 'Med Express reduced our failed deliveries by 40% and their COD reconciliation saves hours every week.', 'med-express' ); ?>”</p>
                <div class="testimonial__author">
                    <span><?php esc_html_e( 'Amel B., Founder of EziShop DZ', 'med-express' ); ?></span>
                    <span>★★★★★</span>
                </div>
            </div>
            <div class="testimonial">
                <p>“<?php esc_html_e( 'Their couriers are professional and always communicate with our customers in Arabic or French. A real partnership.', 'med-express' ); ?>”</p>
                <div class="testimonial__author">
                    <span><?php esc_html_e( 'Yacine L., CEO of TechHub', 'med-express' ); ?></span>
                    <span>★★★★★</span>
                </div>
            </div>
            <div class="testimonial">
                <p>“<?php esc_html_e( 'We scaled to nationwide deliveries within months thanks to their transparent pricing and client dashboard.', 'med-express' ); ?>”</p>
                <div class="testimonial__author">
                    <span><?php esc_html_e( 'Ines R., Operations at VitaCare', 'med-express' ); ?></span>
                    <span>★★★★★</span>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="section" id="pricing">
    <div class="container">
        <div class="section__header">
            <h2><?php esc_html_e( 'Pricing built to scale', 'med-express' ); ?></h2>
            <p><?php esc_html_e( 'Choose the plan that matches your shipment volume. Custom enterprise quotes available on request.', 'med-express' ); ?></p>
        </div>
        <div class="pricing">
            <div class="pricing__plan">
                <h3><?php esc_html_e( 'Starter', 'med-express' ); ?></h3>
                <div class="pricing__price">590 DZD</div>
                <p><?php esc_html_e( 'per parcel within Algiers', 'med-express' ); ?></p>
                <ul class="pricing__features">
                    <li><?php esc_html_e( 'Next-day delivery', 'med-express' ); ?></li>
                    <li><?php esc_html_e( 'COD reconciliation in 72h', 'med-express' ); ?></li>
                    <li><?php esc_html_e( 'Customer support via WhatsApp', 'med-express' ); ?></li>
                </ul>
                <a class="button button--primary" href="#contact"><?php esc_html_e( 'Start shipping', 'med-express' ); ?></a>
            </div>
            <div class="pricing__plan pricing__plan--featured">
                <h3><?php esc_html_e( 'Growth', 'med-express' ); ?></h3>
                <div class="pricing__price">520 DZD</div>
                <p><?php esc_html_e( 'per parcel nationwide', 'med-express' ); ?></p>
                <ul class="pricing__features">
                    <li><?php esc_html_e( 'Same-day pickup', 'med-express' ); ?></li>
                    <li><?php esc_html_e( 'COD reconciliation in 48h', 'med-express' ); ?></li>
                    <li><?php esc_html_e( 'Advanced dashboard analytics', 'med-express' ); ?></li>
                </ul>
                <a class="button button--primary" href="#contact"><?php esc_html_e( 'Talk to sales', 'med-express' ); ?></a>
            </div>
            <div class="pricing__plan">
                <h3><?php esc_html_e( 'Enterprise', 'med-express' ); ?></h3>
                <div class="pricing__price"><?php esc_html_e( 'Custom', 'med-express' ); ?></div>
                <p><?php esc_html_e( 'tailored for 500+ shipments per month', 'med-express' ); ?></p>
                <ul class="pricing__features">
                    <li><?php esc_html_e( 'Dedicated success manager', 'med-express' ); ?></li>
                    <li><?php esc_html_e( 'Reverse logistics & warehousing', 'med-express' ); ?></li>
                    <li><?php esc_html_e( 'API integrations & SLAs', 'med-express' ); ?></li>
                </ul>
                <a class="button button--primary" href="#contact"><?php esc_html_e( 'Book a consultation', 'med-express' ); ?></a>
            </div>
        </div>
    </div>
</section>

<section class="section" id="faq">
    <div class="container">
        <div class="section__header">
            <h2><?php esc_html_e( 'Frequently asked questions', 'med-express' ); ?></h2>
            <p><?php esc_html_e( 'Have a question? We have answers to help you onboard faster.', 'med-express' ); ?></p>
        </div>
        <div class="faq">
            <details class="faq__item">
                <summary><?php esc_html_e( 'How fast can you start picking up my orders?', 'med-express' ); ?></summary>
                <p><?php esc_html_e( 'We can activate new accounts within 24 hours and begin pickups the next business day after verifying your business details.', 'med-express' ); ?></p>
            </details>
            <details class="faq__item">
                <summary><?php esc_html_e( 'Do you offer integrations with Shopify or WooCommerce?', 'med-express' ); ?></summary>
                <p><?php esc_html_e( 'Yes. Our team will help you connect your store for automated order import, status updates, and tracking links.', 'med-express' ); ?></p>
            </details>
            <details class="faq__item">
                <summary><?php esc_html_e( 'Can customers pay by card on delivery?', 'med-express' ); ?></summary>
                <p><?php esc_html_e( 'We support cash, CIB, and Edahabia card payments through mobile POS terminals in major cities.', 'med-express' ); ?></p>
            </details>
            <details class="faq__item">
                <summary><?php esc_html_e( 'What happens if a delivery fails?', 'med-express' ); ?></summary>
                <p><?php esc_html_e( 'Our couriers attempt delivery twice, contact the recipient, and log detailed reasons. You decide whether to reschedule or return.', 'med-express' ); ?></p>
            </details>
        </div>
    </div>
</section>

<section class="section" id="latest">
    <div class="container">
        <div class="section__header">
            <h2><?php esc_html_e( 'Logistics insights from our team', 'med-express' ); ?></h2>
            <p><?php esc_html_e( 'Discover tips on scaling fulfilment, improving COD, and growing your brand across Algeria.', 'med-express' ); ?></p>
        </div>
        <div class="grid grid--3">
            <?php
            $recent_posts = new WP_Query(
                array(
                    'posts_per_page'      => 3,
                    'ignore_sticky_posts' => true,
                )
            );

            if ( $recent_posts->have_posts() ) :
                while ( $recent_posts->have_posts() ) :
                    $recent_posts->the_post();
                    get_template_part( 'template-parts/content', get_post_type() );
                endwhile;
                wp_reset_postdata();
            else :
                get_template_part( 'template-parts/content', 'none' );
            endif;
            ?>
        </div>
    </div>
</section>

<section class="section" id="contact">
    <div class="container contact">
        <div class="contact__info">
            <h3><?php esc_html_e( 'Let’s design your delivery playbook', 'med-express' ); ?></h3>
            <p><?php esc_html_e( 'Tell us about your volumes and goals. We’ll craft an onboarding plan that keeps your customers delighted.', 'med-express' ); ?></p>
            <ul class="contact__details">
                <?php $phone = get_theme_mod( 'medexpress_contact_phone', '+213 (0) 21 123 456' ); ?>
                <li>
                    <span class="dashicons dashicons-phone"></span>
                    <a href="<?php echo esc_url( medexpress_get_phone_href( $phone ) ); ?>"><?php echo esc_html( $phone ); ?></a>
                </li>
                <?php $email = get_theme_mod( 'medexpress_contact_email', 'contact@medexpress.dz' ); ?>
                <li>
                    <span class="dashicons dashicons-email"></span>
                    <a href="mailto:<?php echo antispambot( $email ); ?>"><?php echo esc_html( $email ); ?></a>
                </li>
                <?php $address = get_theme_mod( 'medexpress_contact_address', __( '30 Rue Didouche Mourad, Algiers, Algeria', 'med-express' ) ); ?>
                <li>
                    <span class="dashicons dashicons-location"></span>
                    <span><?php echo esc_html( $address ); ?></span>
                </li>
            </ul>
        </div>
        <form class="contact__form" action="#" method="post">
            <label>
                <?php esc_html_e( 'Full Name', 'med-express' ); ?>
                <input type="text" name="contact-name" placeholder="<?php esc_attr_e( 'e.g. Laila Benali', 'med-express' ); ?>" required>
            </label>
            <label>
                <?php esc_html_e( 'Business Email', 'med-express' ); ?>
                <input type="email" name="contact-email" placeholder="<?php esc_attr_e( 'you@example.com', 'med-express' ); ?>" required>
            </label>
            <label>
                <?php esc_html_e( 'Monthly Shipments', 'med-express' ); ?>
                <input type="number" name="contact-volume" placeholder="<?php esc_attr_e( 'e.g. 300', 'med-express' ); ?>" min="0">
            </label>
            <label>
                <?php esc_html_e( 'How can we help?', 'med-express' ); ?>
                <textarea name="contact-message" placeholder="<?php esc_attr_e( 'Share details about your delivery needs…', 'med-express' ); ?>"></textarea>
            </label>
            <button class="button button--primary" type="submit"><?php esc_html_e( 'Request a callback', 'med-express' ); ?></button>
        </form>
    </div>
</section>

<?php
get_footer();
