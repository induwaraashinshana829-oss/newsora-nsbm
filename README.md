# Newsora

A responsive PHP campus news website implemented from the supplied Figma screenshots. The existing PHP/CSS/JS folders are preserved. Additional portal pages fill out the designs.

## Run locally

Requires PHP 8.1+ with PDO SQLite, mbstring and fileinfo. No Composer, npm installation, or MySQL server is needed.

From this folder:

```sh
php -S 127.0.0.1:8080 router.php
```

On this Mac, MAMP's PHP is available:

```sh
/Applications/MAMP/bin/php/php8.3.30/bin/php -S 127.0.0.1:8080 router.php
```

Open **http://127.0.0.1:8080**. Register using an NSBM university email (for example `yourname@students.nsbm.ac.lk`) and a password with at least 8 characters and a number. Registration validates the email format; it does not verify ownership by email. There are no default login credentials. Your first visit initializes `database/newsora.sqlite` and inserts sample campus stories.

For Apache/MAMP, place the project in the document root and visit its folder URL. PHP must be able to write to `database/` and `uploads/`. Directory `.htaccess` files block direct access to the database and uploaded scripts. Use the supplied router with PHP's development server. PHP upload limits should permit a 5 MB image (`upload_max_filesize = 5M`, `post_max_size = 8M`).

## Pages

| File | Purpose |
| --- | --- |
| `index.php` | Public landing page; signed-in student home |
| `login.php`, `register.php`, `logout.php` | Session authentication |
| `forgot-password.php` | Account recovery requests sent to the admin dashboard |
| `browse.php` | Search, category filters, sorting and pagination |
| `article.php` | Story, bookmarks, sharing, comments and related articles |
| `announcements.php` | Official notices, pinned cards and search/filter controls |
| `saved.php` | Persistent personal reading list |
| `subscriptions.php` | Saved category and in-app notification preferences |
| `settings.php` | Profile, bio, faculty and password updates |
| `admin/index.php` | Submission status and editorial review for administrators |
| `admin/add-article.php`, `admin/edit-article.php` | Drafts, submission, cover uploads and live card preview |
| `about.php` | About, editorial guidelines, privacy and help |
| `api.php` | Authenticated bookmark endpoint |
| `includes/` | Shared layout, database, escaping, sessions and helpers |
| `database/newsora.sql` | SQLite schema applied automatically |
| `css/` | Shared design and responsive breakpoints |
| `js/main.js` | Navigation, bookmarks, share, form preview and password visibility |
| `router.php` | Protected local development routing |

## Editorial workflow

Students save drafts or submit articles for review. Submitted articles appear in My Submissions and only approved articles appear in public feeds. Rejected submissions show editorial feedback and can be edited and resubmitted. Every registration receives the student role. To grant editorial access, use a database administrator tool to update an existing account:

```sql
UPDATE users SET role = 'admin' WHERE email = 'editor@nsbm.ac.lk';
```

Administrators see submissions from all contributors and can approve articles or request revisions. Do not give ordinary students access to the database.

## Design and scope

- White, deep teal and mint palette; responsive portal sidebar, news cards and public landing page.
- Local JPEG images were extracted from the user-supplied screenshots. They reproduce the reference imagery at the available screenshot resolution. Replace them with original Figma exports for production image quality. The wordmark is recreated in text, rather than an original logo export.
- Google Fonts loads DM Sans with Arial fallback; the site remains usable without external fonts.
- Persistent bookmarks, comments, profiles, subscriptions and submissions use SQLite and prepared queries. Passwords are hashed. Mutating requests use CSRF tokens and uploads validate MIME type and size.
- Approved announcement submissions appear in the announcements page and student dashboard; category and keyword filters use the saved article data. Public statistics are calculated from the local database.
- Sample editorial stories are inserted on first run. Replace or remove sample records before using the site as a live publication.
- Password recovery requests appear in the admin dashboard and admin notification bell. Administrators verify identity and set a new password via `admin/recovery.php`, then communicate it directly to the account holder. Email delivery, email ownership verification and push notifications are not configured.
- Profile avatars use initials. No profile photo upload or author-follow service is provided.
- Empty states appear when the corresponding list has no results, rather than always displaying the Figma demonstration states below populated lists.

## Checks

```sh
find . -name '*.php' -print0 | xargs -0 -n 1 php -l
node --check js/main.js
```

Use PHP's development server for local development only. For public deployment, configure a production PHP host, HTTPS, request limits, backups, mail delivery, and host-specific protection of private directories.
