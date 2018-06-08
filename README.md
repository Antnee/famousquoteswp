# Famous Quotes Wordpress Plugin

This project was part of a 35-hour challenger, along with a corresponding API
built with Symfony and Doctrine, to see what I can do in a small amount of
time. This portion required that I create a WP plugin with admin pages, that
inserts _Famous Quotes_ into the footer of the template, as well as providing a
link from the author's name to a page with all of their quotes displayed.

The data is deliberately stored in the API, and not in a local DB. It's a bad
design, but this was a requirement of the challenge. If you're using this
plugin on a live site you are doing it wrong.

This code should exist on Github at
[Antnee/famousquoteswp](https://github.com/Antnee/famousquoteswp), and the
accompanying Symfony/Doctrine API at
[Antnee/famousquotesapi](https://github.com/Antnee/famousquotesapi). I rarely
leave any code publicly available, so don't expect these repositories to exist
forever.

## Usage

### Activation and Admin
Copy the `anthonysfamousquotes` directory to your `/wp-content/plugins` folder.
Now, log in to your Wordpress admin console and click on the Plugins option in
the menu on the left. You should see _Anthony's Famous Quotes_ in the list on
the right-hand side of the page now. Activate the plugin and click on Settings.

Go into the API settings and configure the endpoint and API key. If you don't
know what these should be then I'm afraid I can't help you.

Once that's set up, click on Authors and add any authors that you want. You can
also edit and delete them from here. _Note that when deleting authors, you also
delete their quotes, so be careful!_

Once your authors are configured you can start adding quotes. Click the Quote
menu on the left and start adding. You can load up existing quotes and edit
them, changing the text and even who the quote is attributed to, if you want.
You can also delete quotes from this page.

### Front End
When you view the front-end of your site now, you should see your quotes
showing up in the footer. If no quotes can be found you should see the default
quote instead.

Click on the quote author from the footer-quote, and you should be taken to a
page displaying the rest of that author's quotes.
