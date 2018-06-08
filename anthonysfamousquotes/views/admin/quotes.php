<?php
error_reporting(E_ALL);
if (!current_user_can('manage_options')) {
    return;
}

require_once ANTHONYS_FAMOUS_QUOTES__PLUGIN_DIR.'/Client.php';

$uri = get_option('afk-api_uri');
$key = get_option('afk-api_key');

$client = new \AFQ\Client($uri, $key);

$randomQuote = $client->getRandomQuote();
$authors = $client->getAuthors();
$message = null;

if ($_POST) {
    try {
        if (isset($_POST['newQuoteText'], $_POST['author'], $_POST['saveNewQuote'])) {
            $client->addQuoteForAuthor($_POST['newQuoteText'], $_POST['author']);
            $message = 'New quote added';

        } elseif (isset($_POST['quoteId'], $_POST['newText'], $_POST['newAuthor'], $_POST['oldText'], $_POST['oldAuthor'], $_POST['updateQuote'])) {
            $oldAuthor = null;
            $newAuthor = null;
            $oldName = strtolower(trim($_POST['oldAuthor']));
            $newName = strtolower(trim($_POST['newAuthor']));
            foreach ($authors as $author) {
                if (strtolower(trim($author->getName())) == $oldName) {
                    $oldAuthor = $author;
                }
                if (strtolower(trim($author->getName())) == $newName) {
                    $newAuthor = $author;
                }
            }
            $oldQuote = new \AFQ\Quote($_POST['quoteId'], $_POST['oldText'], $oldAuthor);
            $newQuote = new \AFQ\Quote($_POST['quoteId'], $_POST['newText'], $newAuthor);

            $client->updateQuote($oldQuote, $newQuote);
            $message = 'Quote updated';

        } elseif (isset($_POST['quoteId'], $_POST['deleteQuote'])) {
            $client->deleteQuote(new \AFQ\Quote($_POST['quoteId'], '', new \AFQ\Author('', '', 0)));
            $message = 'Quote deleted';

        }

        // Update counts etc
        $authors = $client->getAuthors();
    } catch (Exception $e) {
        $message = $e->getMessage();
    }

}

$quotes = isset($_GET['author']) ? $client->getQuotesForAuthor($_GET['author']) : [];
$message = isset($_GET['author']) && !count($quotes) && !$message ? 'No messages to show for this user. Add one below' : $message;

$authorOptions = function(string $thisAuthor='') use ($authors): Generator
{
    $thisAuthor = strtolower(trim($thisAuthor));
    foreach ($authors as $author) {
        $selected = (isset($_GET['author']) && $thisAuthor == strtolower(trim($author->getName()))) ? ' selected' : '';
        yield '<option value="' . esc_attr($author->getName()) . '"' . $selected . '>' . esc_html(sprintf("%s (%d quotes found)", $author->getName(), $author->getQuoteCount())) . '</option>';
    }
}
?>

<div class="afq wrap">
    <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
    <?php if ($message) echo '<div id="message" class="error"><p>'.$message.'</p></div>'; ?>
    <form method="get">
        <select name="author">
            <option>Select Author</option>
            <?php foreach ($authorOptions($_GET['author']??'') as $opt) {
                echo $opt;
            } ?>
        </select>
        <input type="hidden" name="page" value="<?=$_GET['page'];?>">
        <?php submit_button('Get quotes', 'primary', 'getQuotes', false); ?>
    </form>

    <h2>Add a Quote</h2>
    <form method="post">
        <table class="form-table">
            <tr>
                <th>Quote</th>
                <td><input type="text" class="regular-text" name="newQuoteText" required></td>
            </tr>
            <tr>
                <th>Quote Author</th>
                <td><select name="author">
                        <option>Select Author</option>
                        <?php foreach ($authorOptions($_GET['author']??'') as $opt) {
                            echo $opt;
                        } ?>
                    </select></td>
            </tr>
        </table>
        <?php submit_button('Save quote', 'primary', 'saveNewQuote'); ?>
    </form>

    <?php
    if (count($quotes)) {
        echo '<h2>Existing Quotes</h2>';
        foreach ($quotes as $quote) {
            ?>
            <form method="post">
                <table class="form-table">
                    <tr>
                        <td><input class="regular-text"
                                   type="text"
                                   name="newText"
                                   value="<?php echo esc_attr($quote->getText()); ?>"
                                   placeholder="Quote text"
                                   title="Enter the quote body here"
                                   required>
                            by
                            <select name="newAuthor">
                                <option>Select Author</option>
                                <?php foreach ($authorOptions($quote->getAuthor()->getName()) as $opt) {
                                    echo $opt;
                                } ?>
                            </select>
                            <input type="hidden" name="quoteId" value="<?=esc_attr($quote->getId());?>">
                            <input type="hidden" name="oldText" value="<?=esc_attr($quote->getText());?>">
                            <input type="hidden" name="oldAuthor" value="<?=esc_attr($quote->getAuthor()->getName());?>">
                            <?php submit_button('Update quote', 'button button-small button-primary', 'updateQuote', false); ?>
                            <?php submit_button('Delete quote', 'button button-small', 'deleteQuote', false); ?>
                        </td>
                    </tr>
                </table>
            </form>
            <?php
        }
    }

    ?>
</div>