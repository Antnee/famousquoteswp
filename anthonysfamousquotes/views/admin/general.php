<?php
if (!current_user_can('manage_options')) {
    return;
}
?>

<div class="afq wrap">
    <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
    <p><strong>This Wordpress plugin will display famous quotes in the footer of your Wordpress blog.</strong></p>
    <p>If the API is unavailable for any reason, you'll get a special quote.</p>
    <p>You are currently viewing the plugin configurator:</p>
    <dl>
        <dt>API Settings</dt>
        <dd>Set up the API endpoint and your keys <a href="admin.php?page=famous-quotes-api">Go &raquo;</a></dd>

        <dt>Authors</dt>
        <dd>Add, remove and update the authors in the API <a href="admin.php?page=famous-quotes-authors">Go &raquo;</a></dd>

        <dt>Quotes</dt>
        <dd>Manage the quotes available in the API <a href="admin.php?page=famous-quotes-manage">Go &raquo;</a></dd>
    </dl>
</div>
