# 📸 Picstome (Local-First Fork)

> A containerized, **local-first fork** of the original [picstome/picstome](https://github.com/picstome/picstome) project.

This fork lets developers and self-hosters deploy, evaluate, and develop on the Picstome photography ecosystem entirely on local machines — no commercial licensing and no external cloud dependencies (paid UI packages, public AWS S3 subscriptions, etc.) required.

---

## 🕒 Changelog (Fork Modifications)

| Change                                | Description                                                                                                                                                                                                                                            |
| ------------------------------------- | ------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------ |
| **Removed Commercial UI Constraints** | Stripped the commercial `livewire/flux-pro` package dependency from `composer.json` and removed its private repository link. The app now builds smoothly on the open-source free tier of `livewire/flux`.                                              |
| **Dockerized Architecture**           | Scaffolding for a fully containerized stack: PHP 8.3/Laravel app server, Vite dev server, a local S3 engine, and a local SMTP relay service.                                                                                                           |
| **Automated Dependency Scaffolding**  | An intelligent `entrypoint.sh` runtime script dynamically patches `composer.json` with open-source fallbacks and forces network configurations inside concurrently managed scripts.                                                                    |
| **Local-First S3 Fallback**           | Intercepted CDN rewrites inside core models (`App\Models\Team` and `App\Models\Photo`) to bypass production optimization services like `wsrv.nl` or `bunny` when no cloud domain is declared, streaming files directly from your local bucket instead. |
| **Isolated Email Capture**            | Realigned outbound mailing away from file logging directly into a dedicated local SMTP interface.                                                                                                                                                      |

---

## 🛠️ Prerequisites

To run this fork, you only need:

- **Docker** and **Docker Compose** installed on your host system.
- No local installations of PHP, Node.js, Composer, or databases are required.

---

## 🚀 Quick Start: Local Deployment

### 1. Set Up Your Configuration Files

Clone this fork to your machine. Ensure your local `.env` is tailored to use the inner container names and explicit host port mappings. Copy the template below:

```ini
APP_NAME=Picstome
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost:8091

DB_CONNECTION=sqlite
SESSION_DRIVER=database
QUEUE_CONNECTION=database
CACHE_STORE=database

# Local S3 Storage (MinIO) Configuration
FILESYSTEM_DISK=s3
LIVEWIRE_TEMPORARY_FILE_UPLOAD_DISK=s3
AWS_ACCESS_KEY_ID=generic_user
AWS_SECRET_ACCESS_KEY=generic_password
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=picstome-galleries
AWS_ENDPOINT=http://minio_s3:9000
AWS_URL=http://localhost:9000/picstome-galleries
AWS_USE_PATH_STYLE_ENDPOINT=true

# Outbound Local SMTP Routing (Mailpit)
MAIL_MAILER=smtp
MAIL_HOST=mailpit_smtp
MAIL_PORT=1025

# Leave blank to bypass production CDN thumbnail rewrites locally
PICSTOME_PHOTO_CDN_DOMAIN=
```

### 2. Launch the Service Stack

Run the following command to pull dependencies, patch core requirements, run structural migrations, and boot the ecosystem:

```bash
docker compose up --build
```

### 3. Initialize the Storage Bucket (Crucial Step)

Before attempting any image uploads in your new dashboard, you must establish the local bucket inside your S3 container:

1. Open your browser and navigate to the MinIO Console: **http://localhost:9001**
2. Log in using the generic credentials:
    - User: `generic_user`
    - Password: `generic_password`
3. Click **Buckets → Create Bucket** and name it exactly: `picstome-galleries`
4. Go to the bucket's anonymous/access policy configuration and change it from **Private** to **Public** (or **Read-Only Download**) so your browser is permitted to view the gallery photos.

---

## 🌐 Local Endpoint Dashboard

Once the containers stabilize, you can access the localized network stack using these addresses:

| Service               | Endpoint URL          | Description                                                                  |
| --------------------- | --------------------- | ---------------------------------------------------------------------------- |
| **Picstome Web App**  | http://localhost:8091 | The main photographer dashboard and client gallery interface.                |
| **Vite Dev Server**   | http://localhost:5173 | Hot-reloading asset compiler (runs automatically).                           |
| **MinIO Console**     | http://localhost:9001 | Local S3 web interface to monitor uploaded photos and buckets.               |
| **Mailpit Dashboard** | http://localhost:8025 | Local inbox to view emails (e.g., contracts, notifications) sent by the app. |

---

## 💡 Notes on Development UI Stubs

Because this fork drops the commercial `flux-pro` package, layout elements calling complex enterprise components (e.g., specific rich editors, data tables, or advanced command palettes) may occasionally throw an `InvalidArgumentException`.

To patch these layout holes gracefully, drop an open-source fallback file (built with standard Tailwind CSS or Alpine.js) inside the centralized custom directory mapping structure at:

```
resources/views/components/flux/
```

For example, a custom override for a missing input element belongs at:

```
resources/views/components/flux/command/input.blade.php
```

---

_This fork exists to give anyone a clear, zero-licensing path to running and developing on the Picstome photography ecosystem entirely locally._
