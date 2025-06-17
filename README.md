## Magento 2 Merchant Documentation

This is the source of the Magento 2 Merchant Documentation website - your complete guide to managing and growing your Magento 2 store.

## Contribute to the Documentation

If you want to curate, edit, add or change content on the documentation, please submit a pull request or open an issue in this repository.

## Local Development
The website is built with Laravel. **A database is not required**. You can run the website locally with the following
commands:

```bash
git clone git@github.com:your-username/merchant-docs.git merchant-docs
cd merchant-docs
bash bin/setup.sh
```

Next, you need to compile the assets and start the server:

```bash
npm run dev
php artisan serve
```

Now add the documentation itself;

```
bash bin/checkout_latest_docs.sh
```

There is no container based setup. There are plenty of different setups out there, so we leave it up to you to choose
your favorite one. If you want a docker-based setup, [Laravel's Sail](https://laravel.com/docs/10.x/sail) might be an 
option for you.

### Torchlight Integration

This project relies on Torchlight for syntax highlighting. You will need to create an account
at [torchlight.dev](https://torchlight.dev/) and generate a free personal token for use in this project. If you used 
the `bin/setup.sh` script to setup the project, the token is in your .env file. If not, add the following line to your
.env file manually:

```ini
TORCHLIGHT_TOKEN=your-torchlight-token
```

## Documentation Categories

The Merchant Documentation is organized into six main categories:

- **Start Selling**: Get your store up and running with your first products, payments, and shipping setup
- **Manage Catalog**: Organize and maintain your product catalog efficiently with bulk operations and smart workflows
- **Handle Orders**: Process orders, manage fulfillment, and handle customer service efficiently
- **Grow Store**: Scale your business with marketing tools, analytics, and customer retention strategies
- **Improve UX**: Enhance customer experience with better design, navigation, and performance optimizations
- **Stay Compliant**: Ensure your store meets legal requirements and industry standards for data protection and commerce

### Syncing Upstream Changes Into Your Fork

This [GitHub article](https://help.github.com/en/articles/syncing-a-fork) provides instructions on how to pull the
latest changes from this repository into your fork.

### Updating After Remote Code Changes

If you pull down the upstream changes from this repository into your local repository, you'll want to update your
Composer and NPM dependencies, as well as update your documentation branches. For convenience, you may run
the `bin/update.sh` script to update these things:

```bash
./bin/update.sh
```
