<?php
namespace AFQ;

require_once ANTHONYS_FAMOUS_QUOTES__PLUGIN_DIR . 'exception/ApiErrorException.php';
require_once ANTHONYS_FAMOUS_QUOTES__PLUGIN_DIR . 'Author.php';
require_once ANTHONYS_FAMOUS_QUOTES__PLUGIN_DIR . 'Quote.php';
require_once ANTHONYS_FAMOUS_QUOTES__PLUGIN_DIR . 'Client.php';

use AFQ\Exception\ApiErrorException;
use WP_Post;

/**
 * Plugin Views
 *
 * @package AnthonysFamousQuotes
 */
class Views
{
    /**
     * ADMIN VIEWS
     */
    public static function generalAdminPage()
    {
        include ANTHONYS_FAMOUS_QUOTES__PLUGIN_DIR . 'views/admin/general.php';
    }

    public static function apiSettingsAdminPage()
    {
        include ANTHONYS_FAMOUS_QUOTES__PLUGIN_DIR . 'views/admin/api.php';
    }

    public static function authorsAdminPage()
    {
        include ANTHONYS_FAMOUS_QUOTES__PLUGIN_DIR . 'views/admin/authors.php';
    }

    public static function quotesAdminPage()
    {
        include ANTHONYS_FAMOUS_QUOTES__PLUGIN_DIR . 'views/admin/quotes.php';
    }



    /**
     * FRONTEND VIEWS
     */


    /**
     * Dynamic Page Title
     *
     * @param WP_Post $post
     */
    public static function authorPageTitle(WP_Post $post)
    {
        if (is_page('anthonys-famous-quotes') && isset($_GET['author'])) {
            $uri = get_option('afk-api_uri');
            $key = get_option('afk-api_key');

            $client = new Client($uri, $key);

            try {
                $author = $client->getAuthorByName($_GET['author']);
                $post->post_title = sprintf("Famous Quotes by %s", $author->getName());
            } catch (ApiErrorException $e) {
                $post->post_title = 'Anthony\'s Famous Quotes';
            }
        }
    }

    /**
     * Dynamic Page Content
     *
     * @param string $content
     * @return string
     */
    public static function authorPageContent(string $content)
    {
        if (is_page('anthonys-famous-quotes') && isset($_GET['author'])) {
            $uri = get_option('afk-api_uri');
            $key = get_option('afk-api_key');

            $client = new Client($uri, $key);

            try {
                $quotes = $client->getQuotesForAuthor($_GET['author']);

                if (count($quotes)) {
                    $body = '<ul>';
                    foreach ($quotes as $quote) {
                        $body .= sprintf('<li><q>%s</q></li>', $quote->getText());
                    }
                    $body .= '</ul>';
                } else {
                    $body = sprintf("It seems that we don't have any record of %s saying anything interesting", $author->getName());
                }

                return $body;

            } catch (ApiErrorException $e) {
                return 'Unfortunately, an error occurred while fetching the quotes';
            }
        }
        return $content;
    }

    /**
     * Put the quote in the footer
     */
    public static function footer()
    {
        require_once(ANTHONYS_FAMOUS_QUOTES__PLUGIN_DIR. 'Client.php');
        $uri = get_option('afk-api_uri');
        $key = get_option('afk-api_key');
        $client = new Client($uri, $key);

        try {
            $quote = $client->getRandomQuote();
        } catch (ApiErrorException $e) {
            $author = new Author('', 'William Edward Hickson');
            $quote = new Quote('', "If at first you don't succeed, try, try again", $author);
        }

        $authorName = $quote->getAuthor()->getName();

        \wp_enqueue_style('afqStyle', ANTHONYS_FAMOUS_QUOTES__PLUGIN_URI . 'css/main.css');
        echo '
            <footer>
                <p id="afk_footer_quote"><q>' . $quote->getText() . '</q> - <a href="/anthonys-famous-quotes/?author='.urlencode($authorName).'">' . esc_html($authorName) . '</a></p>
            </footer>';
    }
}