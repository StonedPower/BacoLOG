# BacoLOG

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
- [API Documentation](#api-documentation)
  - [Base URL](#base-url)
  - [Endpoints](#endpoints)
  - [Request Examples](#request-examples)
  - [Response Examples](#response-examples)
  - [JSON Structure](#json-structure)
- [Setup](#setup)
- [Nginx Configuration](#nginx-configuration)
- [License](#license)

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

## API Documentation

### Base URL

```bash
http://yourserver/api
```

Replace `yourserver` with your domain or IP.

### Endpoints

#### GET /api/

Returns the HTML documentation for the API.

**Example Request:**

```bash
curl http://yourserver/api/
```

#### GET /api/stats

Returns:

- Global severity counts
- Total hosts with IPs
- Per-host severity totals
- Last log entry per host

**Example Request:**

```bash
curl http://yourserver/api/stats
```

**Example Response:**

```json
{
  "generated_at": "2026-01-03T12:00:00+00:00",
  "total_hosts": 3,
  "hosts": ["192.168.1.10", "192.168.1.11", "192.168.1.12"],
  "global_severities": {
    "emerg": 1,
    "alert": 0,
    "crit": 2,
    "error": 5,
    "warn": 10,
    "notice": 4,
    "info": 20,
    "debug": 8
  },
  "per_host": {
    "192.168.1.10": {
      "total_severities": 15,
      "severities": {
        "emerg": 1,
        "alert": 0,
        "crit": 2,
        "error": 3,
        "warn": 5,
        "notice": 1,
        "info": 2,
        "debug": 1
      },
      "last_log": "<2026-01-03 12:00:00> info System check complete"
    }
  }
}
```

#### GET /api/health

Returns system health and uptime.

**Example Request:**

```bash
curl http://yourserver/api/health
```

**Example Response:**

```json
{
  "status": "ok",
  "time": "2026-01-03T12:00:00+00:00",
  "uptime_seconds": 3600
}
```

### JSON Structure

#### Stats API (`/api/stats`)

- `generated_at` — ISO 8601 timestamp when stats were generated
- `total_hosts` — integer, number of hosts detected
- `hosts` — array of host IP addresses
- `global_severities` — object with keys `emerg`, `alert`, `crit`, `error`, `warn`, `notice`, `info`, `debug` representing counts across all hosts
- `per_host` — object keyed by host IP containing:
  - `total_severities` — total number of log entries for that host
  - `severities` — object with individual severity counts
  - `last_log` — string containing the last log entry for that host

#### Health API (`/api/health`)

- `status` — `"ok"` if system is healthy
- `time` — ISO 8601 timestamp
- `uptime_seconds` — integer representing system uptime in seconds

## Setup

1. Clone repository and place files in web root (see Installation above).

2. Configure Nginx for PHP-FPM 8.2:

```nginx
server {
    listen 80;
    server_name bacolog.kroes.frl;

    root /var/www/html;
    index index.php;

    location = /api { include fastcgi_params; fastcgi_param SCRIPT_FILENAME $document_root/api/index.php; fastcgi_pass unix:/run/php/php8.2-fpm.sock; }
    location = /api/ { include fastcgi_params; fastcgi_param SCRIPT_FILENAME $document_root/api/index.php; fastcgi_pass unix:/run/php/php8.2-fpm.sock; }
    location = /api/stats { include fastcgi_params; fastcgi_param SCRIPT_FILENAME $document_root/api/stats.php; fastcgi_pass unix:/run/php/php8.2-fpm.sock; }
    location = /api/health { include fastcgi_params; fastcgi_param SCRIPT_FILENAME $document_root/api/health.php; fastcgi_pass unix:/run/php/php8.2-fpm.sock; }

    location ~ \.php$ {
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        fastcgi_pass unix:/run/php/php8.2-fpm.sock;
    }

    location / {
        try_files $uri $uri/ /index.php;
    }
}
```

3. Reload Nginx:

```bash
nginx -t && systemctl reload nginx
```

## Acknowledgment 

This whole project is vibe coded using AI. 
I’m not a programmer, but know somewhat php, I’ve checked the code over, but I cannot guarantee that there are some errors. 

### **Warning** 

Do not expose this to the internet without proper authentication. 

I am **NOT** responsible for any hacks, data loss or whatever.  

**YOU ARE WARNED** 




