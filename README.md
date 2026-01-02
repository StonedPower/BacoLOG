BacoLOG

BacoLOG is a lightweight, mobile-friendly PHP syslog viewer and dashboard. It allows you to monitor remote syslog files, filter by severity, and get a quick overview of system logs across multiple hosts. All configuration is handled via a static JSON file, making it easy to deploy and maintain.

⸻

Features
	•	✅ Dashboard overview: shows total hosts and logs per severity (emerg, alert, crit, error, warn, notice, info, debug)
	•	✅ Viewer: per-host logs with severity filters and “ALL” button
	•	✅ Newest logs on top for quick access
	•	✅ Mobile-friendly layout — works on any device
	•	✅ Static JSON configuration — no database or settings page required
	•	✅ Auto-load logs from multiple hosts (/var/log/remote)

⸻

Screenshots

(Add your screenshots here)
	•	Dashboard view with all severity cards
	•	Viewer with per-host tabs and severity filters
	•	Mobile view

⸻

Installation
	1.	Clone the repository into your web server:

git clone https://github.com/yourusername/bacolog.git /var/www/html/bacolog

	2.	Make sure your PHP environment is running (tested on PHP-FPM 8.2).
	3.	Ensure the web server user has read access to your remote logs directory:

sudo chmod -R 755 /var/log/remote

	4.	Create the configuration file includes/settings.json:

{
  "auto_refresh": true,
  "refresh_interval": 10,
  "max_lines": 2000,
  "dashboard_host": "Production Syslog"
}

	•	Edit this file to adjust refresh intervals, maximum log lines, or the dashboard host label.

	5.	Open in your browser:

http://your-server/bacolog/index.php


⸻

Usage

Dashboard
	•	Displays total hosts and counts for all severities.
	•	Fully responsive for desktop and mobile.
	•	No logs displayed, only severity overview cards.

Viewer
	•	Per-host tabs show logs for each host.
	•	Filter logs by severity (emerg → debug) or show all logs.
	•	Newest logs appear on top.
	•	Mobile-friendly and auto-loads all logs.

⸻

Directory Structure

/bacolog
├── index.php         # Dashboard
├── viewer.php        # Log viewer
├── includes
│   ├── common.php    # Helper functions
│   ├── navbar.php    # Navigation bar
│   └── settings.json # Static configuration
└── README.md


⸻

Configuration
	•	auto_refresh (boolean) — Enable/disable auto-refresh
	•	refresh_interval (int) — Refresh interval in seconds
	•	max_lines (int) — Max log lines per file
	•	dashboard_host (string) — Label for dashboard

Note: All configuration is static via settings.json. No dynamic settings page.

⸻

Requirements
	•	PHP >= 8.2 with FPM
	•	Web server (Nginx or Apache)
	•	Read access to /var/log/remote

⸻

Contributing
	1.	Fork the repository
	2.	Create a feature branch (git checkout -b feature/awesome-feature)
	3.	Commit your changes (git commit -am 'Add awesome feature')
	4.	Push to the branch (git push origin feature/awesome-feature)
	5.	Open a Pull Request

⸻

License

This project is licensed under the MIT License.

⸻

Acknowledgements
	•	Built with PHP-FPM and flexible CSS layouts
	•	Inspired by syslog monitoring needs for multi-host environments

⸻


