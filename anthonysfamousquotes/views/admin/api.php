<?php
if (!current_user_can('manage_options')) {
    return;
}
?>

<div class="afq wrap">
    <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
    <?php if (stripos($_SERVER['REQUEST_URI'], '&settings-updated=true') > 0): ?>
    <div id="message" class="updated"><p>Settings saved.</p></div>
    <?php endif; ?>
    <p>Manage your API configuration below:</p>

    <form method="post" action="options.php">
        <?php
            settings_fields('afq-group');
            do_settings_sections('afq-group');
        ?>

        <table class="form-table">
            <tr valign="top">
                <th scope="row">API Endpoint URI</th>
                <td><input class="regular-text" type="url" name="afk-api_uri" value="<?php echo esc_attr( get_option('afk-api_uri') ); ?>" placeholder="eg: http://example.url" title="Enter a valid URL where the Famous Quotes API can be found" required></td>
            </tr>

            <tr valign="top">
                <th scope="row">API Key</th>
                <td><input class="regular-text" type="text" name="afk-api_key" value="<?php echo esc_attr( get_option('afk-api_key') ); ?>" placeholder="Contact administrator" title="Enter the API key that you were given by the Famous Quotes administrator" required pattern="[-A-Za-z0-9+/=]{32,64}"></td>
            </tr>
        </table>

        <?php submit_button(); ?>
    </form>
</div>
