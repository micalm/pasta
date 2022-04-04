<p align="center">
    <img src="./public/img/pasta.svg" width="128">
</p>

# What is Pasta?
Pasta is a (yet another) opinionated ~pastebin~ pastabin, written using Laravel.

It's a personal project, which began as a fun way to relax without staying away from code for too long.

New features, improvements, and fixes will (or won't) come as I implement them.

# Installation
- Get a server running PHP 8.1, Node.js 17, and MySQL 8. Older (not much older, though) versions [should work](https://laravel.com/docs/9.x/deployment#server-requirements). Other [databases](https://laravel.com/docs/9.x/database#introduction) also should work.
- Clone the repo or download a zip.
- Install dependencies - `composer install` for PHP, `npm install` for JS.
- Copy `.env.example` to `.env` and fill in values to match your setup. At the moment the important ones are `APP_ENV` (should be set to `production` on production), `APP_DEBUG` (should be `false` in production), and `DB_*` (should match your DB).
- Generate an encryption key with `php artisan key:generate`. Important - it isn't used to encrypt pastas. Pasta encryption keys are derived from user-provided passwords. Laravel uses it for some internal stuff.
- Run migrations - `php artisan migrate`. This will set up the database structure for you.
- Build styles and scripts for production with `npm run production`. `npm run dev` will build for development and `npm run watch` will watch for changes while you're developing.
- Serve with `php artisan serve` for development. Serve from `public/` with Apache, Caddy, Nginx or whatever you prefer.

See [Laravel 9.x docs](https://laravel.com/docs/9.x/installation) for more information on possibilities. This isn't an advanced app. A basic LEMP stack or shared hosting with PHP/MySQL should work. 

# Usage
- Top right is where all the settings live.
- *Language* means *syntax highlighting mode*. Most of what [CodeMirror](https://codemirror.net/) supports is there. Some entries are duplicated for clarity.
- *Anonymous* is the default author name. This will change when user features are implemented. You can enter your name here and it will display with your pasta.
- *Password (optional)* is the (optional) password, from which an encryption key is derived. If you're okay with your pasta being sent and stored in plaintext, leave it alone. If not, provide an 8+ chars password. It WILL be sent to the server, but it won't be stored. At least if nobody modified the code (I didn't. This repo should be ok. Unless one of a couple dozen things that can happen will happen, so... review the code before using it.)

# Roadmap
- Finish up the repo. ~Readme~, examples, wiki, dotfiles.
- Client-side encryption
- Web frontend using Alpine.js
- Clean up initial Laravel files
- User login/registration
- Figure out what to do next. Something will inevitably come up :)

# Contributing
Any contributions are welcome, but you really should consider helping [other FLOSS projects](https://github.com/topics/help-wanted) first. They need it more.

# License
Pasta is open-source software licensed under the [MIT license](LICENSE).
TLDR: Do (almost) anything you want with it, don't blame me if it doesn't work the way you thought it will.
