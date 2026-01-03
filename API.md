# API Documentation

## Base URL

```bash
http://yourserver/api
```

Replace `yourserver` with your domain or IP.

## Endpoints

### GET /api/

Returns the HTML documentation for the API.

**Example Request:**

```bash
curl http://yourserver/api/
```

### GET /api/stats

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

### GET /api/health

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

## JSON Structure

### Stats API (`/api/stats`)

- `generated_at` — ISO 8601 timestamp when stats were generated
- `total_hosts` — integer, number of hosts detected
- `hosts` — array of host IP addresses
- `global_severities` — object with keys `emerg`, `alert`, `crit`, `error`, `warn`, `notice`, `info`, `debug` representing counts across all hosts
- `per_host` — object keyed by host IP containing:
  - `total_severities` — total number of log entries for that host
  - `severities` — object with individual severity counts
  - `last_log` — string containing the last log entry for that host

### Health API (`/api/health`)

- `status` — `"ok"` if system is healthy
- `time` — ISO 8601 timestamp
- `uptime_seconds` — integer representing system uptime in seconds
