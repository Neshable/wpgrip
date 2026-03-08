# Uptime Monitoring

WPGrip monitors your sites every minute and alerts you when something goes wrong.

## How It Works

1. Every 60 seconds, WPGrip sends an HTTP request to each monitored site
2. If the site responds with a 2xx status code, it’s marked as **up**
3. If it fails to respond or returns an error, it’s marked as **down**
4. Alerts are triggered after consecutive failures (to avoid false positives)

## Response Time Tracking

WPGrip captures detailed timing data for every check:

| Metric | Description |
|--------|-------------|
| **Total response time** | End-to-end request duration |
| **DNS lookup** | Time to resolve the domain |
| **TCP connect** | Time to establish the TCP connection |
| **TLS handshake** | Time for the SSL/TLS negotiation |
| **TTFB** | Time to first byte from the server |
| **Transfer** | Time to download the response body |

This data is displayed as:
- A **sparkline** in the sites list (last 24 hours)
- A **detailed chart** on the site’s Monitoring page (with individual timing components)
- **Stat cards** showing averages and P95 values

## Monitoring Page

Each site’s monitoring page includes:

- **24-hour response time chart** — line chart with Total, TTFB, TLS, Connect, and DNS layers
- **Uptime percentage** — calculated over the last 30 days
- **Average response time** — mean of the last 24 hours
- **Current status** — up/down with last check timestamp
- **SSL certificate status** — valid/expiring/expired with expiry date
- **Domain expiry** — when your domain registration expires

## Alerts

When a site goes down, WPGrip sends notifications via:
- In-app notification (always)
- Email notification (configurable)

When the site recovers, you’ll receive a recovery notification with the downtime duration.

## Data Retention

Monitoring data (response times, check logs) is retained for **30 days**. Older data is automatically pruned.

::: info
Uptime monitoring starts automatically when you add a site. No additional configuration needed.
:::
