<?php
use AFQ\Author;

if (!current_user_can('manage_options')) {
    return;
}

require_once ANTHONYS_FAMOUS_QUOTES__PLUGIN_DIR.'/Client.php';

$uri = get_option('afk-api_uri');
$key = get_option('afk-api_key');

$ready = $uri && $key;
$message = false;

if ($ready) {
    $client = new \AFQ\Client($uri, $key);

    if (!empty($_POST)) {
        switch ($_POST['apiMethod']) {
            case 'PATCH':
            case 'POST':
            case 'DELETE':
                $update = true;
                break;
            default:
                $update = false;
        }

        /**
         * Update/Delete Author
         */
        if ($update) {
            try {
                if ($_POST['updateAuthor'] && $_POST['authorNameNew'] && $_POST['authorNameOld'] && $_POST['authorNameNew'] !== $_POST['authorNameOld']) {
                    $oldAuthor = new Author($_POST['authorId'], $_POST['authorNameOld']);
                    $newAuthor = new Author($_POST['authorId'], $_POST['authorNameNew']);
                    $author = $client->updateAuthor($oldAuthor, $newAuthor);
                    $message = 'Author updated OK';

                } elseif ($_POST['deleteAuthor'] && $_POST['authorNameOld']) {
                    $author = new Author($_POST['authorId'], $_POST['authorNameOld']);
                    $client->deleteAuthor($author);
                    $message = 'Author deleted OK';

                } elseif ($_POST['addAuthor'] && $_POST['authorName']) {
                    $client->addAuthor($_POST['authorName']);
                    $message = $response->error ? $response->error->message : 'Author added OK. See below';
                }
            } catch (Exception $e) {
                $message = $e->getMessage();
            }
        }

    }

    // Get Authors
    try {
        $authors = $client->getAuthors();
    } catch (\Exception $e) {
        $message = $e->getMessage();
    }
} else {
    $message = 'You have not yet set your API URI and/or API key. Please set these and try again';
    $ready = false;
}
?>

<div class="afq wrap">
    <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
    <?php if ($message) echo '<div id="message" class="error"><p>'.$message.'</p></div>'; ?>
    <h2>Add a New Author</h2>
    <form method="post" id="addAuthor">
        <table class="form-table">
            <tr valign="top">
                <th scope="row">New Author Name</th>
                <td>
                    <input class="regular-text"
                           type="text"
                           name="authorName"
                           value=""
                           placeholder="Author Name"
                           title="Enter this author's name"
                           required>
                    <input type="hidden" name="apiMethod" value="POST">
                    <?php submit_button('Add new author', 'primary', 'addAuthor'); ?>
                </td>
            </tr>
        </table>
    </form>

    <?php
    if ($ready) {
        echo '<h2>Existing Authors</h2><table class="form-table">';
        foreach ($authors as $author) {
            ?>
            <tr valign="top">
                <td><form method="post">
                        <input class="regular-text"
                               type="text"
                               name="authorNameNew"
                               value="<?php echo esc_attr($author->getName()); ?>"
                               placeholder="Author Name"
                               title="<?php echo sprintf("%s has %d quote(s) in the database", $author->getName(), $author->getQuoteCount()); ?>"
                               required>
                        <input type="hidden" name="authorNameOld" value="<?php echo esc_attr($author->getName());?>">
                        <input type="hidden" name="authorId" value="<?php echo esc_attr($author->getId());?>">
                        <input type="hidden" name="apiMethod" value="PATCH">
                        <?php submit_button('Save Changes', 'button button-small button-primary','updateAuthor', false); ?>
                        <?php submit_button('Delete Author', 'button button-small','deleteAuthor', false); ?>
                        <a href="?page=famous-quotes-manage&author=<?=urlencode($author->getName());?>">Quotes</a>
                    </form>
                </td>
            </tr>
            <?php
        }
        echo '</table>';
    }
    ?>
</div>
