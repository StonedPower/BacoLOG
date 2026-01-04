![BacoLOG](https://cdn.kroes.frl/images/bacolog/logo-bacolog-light-500x500.png)
BacoLOG is a web-based syslog monitoring and viewer tool. 

It provides:
- Live log viewing per host
- Severity counts and dashboards
- Mobile-friendly layout
- Read-only API access for automation and dashboards


## Table of Contents

- [Overview](#overview)
- [Installation](#installation)
- [Web Interface](#web-interface)
- [Setup](#setup)
- [Nginx Configuration](#nginx-configuration)
- [Acknowledgment](#acknowledgment)

## Overview

BacoLOG collects syslog files from `/var/log/remote` and presents them in a structured, readable dashboard. It allows monitoring log severities per host and provides API access for integration with other tools or dashboards.

## Installation

1. Clone the repository:

```bash
git clone https://github.com/StonedPower/BacoLOG.git
```

2. Place the files under your web root (e.g., `/var/www/html`):

```
html/
├── index.php          # Main dashboard
├── viewer.php         # Optional log viewer
├── api/               # API directory
│   ├── index.php      # API docs page
│   ├── stats.php      # Stats API
│   ├── health.php     # Health API
│   └── common.php     # Shared functions
```

3. Set correct permissions so the web server can read logs:

```bash
chmod -R 755 /var/log/remote
chown -R www-data:www-data /var/log/remote
```

## Web Interface

- **Dashboard** – shows global severity counts and host summaries.
- **Viewer** – browse log files by host, newest entries on top.
- **Mobile-compatible** – responsive layout for tablets and phones.


## Setup

1. Clone repository and place files in web root (see Installation above).

2. Configure Nginx for PHP-FPM 8.2:

```nginx
worker_processes auto;

events { worker_connections 1024; }

http {
    include       mime.types;
    default_type  application/octet-stream;

    access_log /var/log/nginx/access.log;
    error_log  /var/log/nginx/error.log;

    sendfile on;

server {
    listen 80;
    server_name bacolog.kroes.frl;

    root /var/www/html;
    index index.php;

    access_log /var/log/nginx/access.log;
    error_log  /var/log/nginx/error.log;

    # =========================
    # API ENDPOINTS (NO REDIRECTS)
    # =========================

    location = /api/v1/stats {
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $document_root/api/v1/stats.php;
        fastcgi_pass 127.0.0.1:9000;
    }

    location = /api/v1/hosts {
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $document_root/api/v1/hosts.php;
        fastcgi_pass 127.0.0.1:9000;
    }

    location = /api/v1/health {
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $document_root/api/v1/health.php;
        fastcgi_pass 127.0.0.1:9000;
    }
    location = /api/v1/docs {
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $document_root/api/v1/docs.php;
        fastcgi_pass 127.0.0.1:9000;
    }

    # =========================
    # PHP FILES (NORMAL SITE)
    # =========================

    location ~ \.php$ {
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        fastcgi_pass 127.0.0.1:9000;
    }

    # =========================
    # STATIC FILES
    # =========================

    location / {
        try_files $uri $uri/ /index.php;
    }
}
}
```

3. Reload Nginx:

```bash
nginx -t && systemctl reload nginx
```

## Acknowledgment 

This whole project is vibe coded using AI. 
I’m not a programmer, but know somewhat php, I’ve checked the code over, but I cannot guarantee anything. 

### **Warning** 

Do not expose this to the internet without proper authentication. 

I am **NOT** responsible for any hacks, data loss or whatever.  

**YOU ARE WARNED** 




