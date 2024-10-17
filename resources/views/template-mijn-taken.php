<?php declare(strict_types=1);
get_header(); ?>
<main id="main" class="template-opentaak">
    <div class="container">
        <div class="container-inner">
            <?php while (have_posts()) : the_post(); ?>

            <header>
                <h1>
                    <?php echo get_the_title() ?>
                </h1>
                <div id="readspeaker_button1" class="rs_skip rsbtn rs_preserve">
                    <a rel="nofollow" class="rsbtn_play" accesskey="L"
                        title="Laat de tekst voorlezen met ReadSpeaker webReader"
                        href="//app-eu.readspeaker.com/cgi-bin/rsent?customerid=8150&amp;lang=nl_nl&amp;readid=readspeaker&amp;url=<?php echo get_permalink() ?>">
                        <span class="rsbtn_left rsimg rspart"><span class="rsbtn_text"><span>Lees
                                    voor</span></span></span>
                        <span class="rsbtn_right rsimg rsplay rspart"></span>
                    </a>
                </div>
            </header>

            <div class="content">
                <div id="readspeaker">
                    <?php the_content(); ?>
                </div>
            </div>

            <?php endwhile; ?>
        </div>
    </div>
</main>

<?php get_footer();
