# Setting Up Monitoring

WPGrip monitors every connected site for uptime, response time, SSL certificate health, and domain registration expiry. Monitoring requires **zero configuration** — it activates the moment you [add a site](/guide/adding-a-site) and runs continuously until you remove it.

## What Gets Monitored

Four monitors run automatically for every site in your workspace:

| Monitor | Frequency | What it checks | Alerts |
|---------|-----------|----------------|--------|
| **Uptime** | Every 60 seconds | HTTP response status from your site's URL | Site down / recovered |
| **Response time** | Every 60 seconds (same check) | Full request lifecycle timing breakdown | No threshold alerts — tracked for analysis |
| **SSL certificate** | Every 60 seconds | Certificate validity, issuer, and days until expiry | 30 / 14 / 7 / 0 days before expiration |
| **Domain expiry** | Once per week | WHOIS lookup for domain registration end date | Domain nearing expiration |

::: tip No setup, no toggle, no config
There is nothing to enable. You will never find a "Start Monitoring" button in WPGrip — it doesn't exist because monitoring is always on. The first check fires within 60 seconds of saving a new site.
:::

---

## Uptime Monitoring

### How Checks Work

Every 60 seconds WPGrip sends an HTTP `GET` request to the site's URL (the one you entered when [adding the site](/guide/adding-a-site)). WPGrip records:

- Whether the request **succeeded** (2xx / 3xx status) or **failed** (timeout, 5xx, connection refused, DNS failure)
- A full **response time breakdown** of every phase of the request (see [Response Time Breakdown](#response-time-breakdown) below)

If a check fails, WPGrip confirms the failure with a second check before marking the site as down. This avoids false-positive alerts caused by a single dropped packet or momentary network blip.

### Sites List — At-a-Glance View

On the main **Sites** page, every site card surfaces two monitoring indicators so you can scan the health of your entire portfolio in seconds:

**Status badge** — a colored dot next to the site name:

| Badge | Meaning |
|-------|---------|
| 🟢 **Green** | Site is up and responding normally |
| 🔴 **Red** | Site is currently unreachable |
| 🟡 **Yellow** | Site is responding but with degraded performance or intermittent errors |
| ⚪ **Grey** | No data yet (site was just added) |

**Response time sparkline** — a compact mini-chart to the right of each site card showing the last 24 hours of response times. A flat, low line is healthy; sudden spikes or sustained elevation are worth investigating.

::: info Reading sparklines
Sparklines are intentionally small — they are designed for pattern recognition, not precise numbers. Click through to the site's Monitoring page for exact values.
:::

### Site Monitoring Page

Click any site, then navigate to the **Monitoring** tab. This is the detailed monitoring dashboard for a single site.

#### 24-Hour Response Time Chart

A full-width line chart plotting every response time measurement from the last 24 hours. Key features:

- **Hover** over any data point to see the exact response time (in milliseconds) and timestamp
- **Downtime periods** are highlighted with a red background band so you can see exactly when and how long an outage lasted
- The Y-axis auto-scales to the range of your data

#### Summary Metrics

Above the chart, five headline statistics give you the current state at a glance:

| Metric | What it tells you |
|--------|-------------------|
| **Current status** | `Up` or `Down`, plus how long the site has been in that state (e.g., "Up for 6 days 14 hours") |
| **Uptime %** | Percentage of successful checks over the last 30 days |
| **Avg response time** | Mean response time across all checks in the last 24 hours |
| **SSL certificate status** | `Valid`, `Expiring soon`, or `Expired` — with the exact expiry date |
| **Domain expiry** | Number of days until the domain registration expires, or `Unknown` if WHOIS data is unavailable |

### Response Time Breakdown

Each 60-second check records six timing phases that together make up the **Total** response time. Understanding these phases helps you pinpoint exactly *where* slowness originates.

| Phase | What it measures | Typical healthy range |
|-------|-----------------|----------------------|
| **DNS** | Resolving the domain name to an IP address | 5–50 ms |
| **TCP Connect** | Opening a TCP socket to the server | 10–80 ms |
| **TLS** | Completing the SSL/TLS handshake | 20–100 ms |
| **TTFB** (Time to First Byte) | Waiting for the server to generate the first byte of the response | 100–600 ms |
| **Transfer** | Downloading the full response body | 10–200 ms |
| **Total** | End-to-end request time (sum of the above) | 200–800 ms |

The phases are sequential — each one must complete before the next begins:

```
|-- DNS --|-- TCP --|-- TLS --|-------- TTFB --------|-- Transfer --|
|                        Total                                       |
```

::: tip Diagnosing slow response times by phase
| Slow phase | Likely cause | Action |
|------------|-------------|--------|
| **DNS** | Slow or distant DNS provider | Switch to Cloudflare DNS (`1.1.1.1`), Google DNS, or Route 53 |
| **TCP Connect** | Server is far from the monitoring location, or is overloaded | Use a CDN, upgrade server resources, or check for network saturation |
| **TLS** | Large certificate chain, OCSP stapling disabled, or slow certificate authority | Enable OCSP stapling; remove unnecessary intermediate certs |
| **TTFB** | Slow PHP execution, heavy database queries, no page cache | Install a page-caching plugin (WP Super Cache, W3 Total Cache), optimize queries, enable OPcache |
| **Transfer** | Large HTML response body or constrained bandwidth | Enable gzip/Brotli compression; reduce page weight |
:::

---

## SSL Certificate Monitoring

SSL certificate checks run **every 60 seconds** as part of the same uptime request. WPGrip reads the presented TLS certificate and records:

- **Issuer** — the Certificate Authority (e.g., Let's Encrypt, DigiCert, Sectigo)
- **Issued date** and **expiry date**
- **Days remaining** until expiration
- **Validity** — whether the certificate chain is complete and the hostname matches

### Expiry Alert Schedule

As your certificate approaches expiration, WPGrip sends escalating notifications:

| Days remaining | Severity | Notification |
|---------------|----------|-------------------------------|
| **30 days** | Info | "SSL certificate for example.com expires in 30 days" |
| **14 days** | Warning | "SSL certificate for example.com expires in 14 days" |
| **7 days** | Urgent | "SSL certificate for example.com expires in 7 days — renew immediately" |
| **0 days** | Critical | "SSL certificate for example.com has expired" |

Each alert is sent once — you won't receive repeated daily alerts at the same tier.

::: danger An expired certificate kills your traffic
When an SSL certificate expires, every major browser displays a full-page security warning ("Your connection is not private"). Most visitors leave immediately. Search engines may also de-index pages served over an invalid certificate. Treat the 14-day alert as your hard deadline.
:::

::: warning Let's Encrypt certificates auto-renew — but not always
Let's Encrypt certificates are valid for 90 days and auto-renew at 60 days. If you receive a 30-day alert for a Let's Encrypt cert, auto-renewal has failed. SSH into the server and diagnose:
```bash
# Test renewal without actually renewing
sudo certbot renew --dry-run
```
Common causes: changed DNS records, port 80 blocked by firewall, Certbot not in cron, HTTP-01 challenge failing behind a reverse proxy.
:::

::: tip Wildcard and multi-domain certificates
WPGrip checks the certificate presented for the exact URL of your site. If you use a wildcard certificate (`*.example.com`), it will be detected correctly. Multi-domain (SAN) certificates are also supported — WPGrip verifies that the site's domain is listed in the Subject Alternative Names.
:::

---

## Domain Expiry Monitoring

Once per week, WPGrip performs a **WHOIS lookup** on each site's domain to check when the registration expires. This is a separate check from uptime and SSL — it doesn't use HTTP at all.

### Why This Matters

Expired domains are a real risk, especially for agencies managing client sites:

- The registrar account may belong to a former employee or the client directly
- Auto-renewal credit cards expire
- Registrar emails land in spam and renewal reminders go unnoticed
- An expired domain can be snatched by domain squatters within days

WPGrip's weekly WHOIS check gives you early warning before any of this happens.

::: info WHOIS limitations
WHOIS data availability varies by registrar and TLD:
- Some registrars redact expiry dates behind **WHOIS privacy** (GDPR-era RDAP responses often omit dates)
- Country-code TLDs (`.de`, `.uk`, `.au`) may use non-standard WHOIS formats
- Newer generic TLDs (`.io`, `.app`, `.dev`) sometimes have restricted lookups

If WPGrip cannot determine the expiry date, the field shows **"Unknown"**. In that case, check your registrar's dashboard directly.
:::

---

## Alert Flow

WPGrip sends notifications through your workspace's configured channels (email by default). Notification preferences can be managed in **Settings → Notifications**.

Here is the complete lifecycle of a downtime alert:

### 1. Downtime Detected

```
Check fails → Confirmation check fails → Site marked DOWN → Alert sent
```

- WPGrip performs a **confirmation check** after the first failure to rule out transient network issues
- If both checks fail, the site status flips to **Down**
- A notification is sent immediately:

  > **Subject:** Site example.com is DOWN
  >
  > Your site example.com is not responding. Detected at 2024-03-15 14:32 UTC.
  > Error: Connection timed out after 10 seconds.

### 2. Recovery Detected

```
Check succeeds (after downtime) → Site marked UP → Recovery alert sent
```

- The next successful check after a downtime period flips the status to **Up**
- A recovery notification is sent that includes the **total downtime duration**:

  > **Subject:** Site example.com is UP
  >
  > Your site example.com has recovered. It was down for **4 minutes 23 seconds**
  > (from 14:32 UTC to 14:36 UTC).

::: tip Downtime duration is invaluable for reporting
The recovery alert's duration field gives you a precise outage length without having to dig through charts. Use it for client SLA reports, incident post-mortems, or hosting provider support tickets.
:::

### Alert Timing Summary

| Event | When you're notified | What's included |
|-------|---------------------|------------------|
| Site goes down | Immediately after confirmation | Timestamp, error type |
| Site recovers | On next successful check | Timestamp, total downtime duration |
| SSL cert expiring | At 30, 14, 7, 0 days remaining | Domain, expiry date, days remaining |
| SSL cert expired | On expiry day | Domain, expired date |
| Domain expiring | As expiry approaches | Domain, registrar (if available), expiry date |

---

## Data Retention

All monitoring data is retained for **30 days**:

| Data type | Retention |
|-----------|-----------|
| Individual uptime check results (up/down + status code) | 30 days |
| Response time measurements (all 6 phases) | 30 days |
| SSL certificate snapshots (issuer, expiry, validity) | 30 days |
| Downtime incident records (start, end, duration) | 30 days |

After 30 days, raw data points are permanently purged. The 24-hour chart and summary metrics always reflect the most recent data.

::: warning No long-term historical data
WPGrip does not currently offer extended data retention. If you need monitoring data beyond 30 days for compliance, SLA reporting, or trend analysis, export or screenshot the data before it ages out.
:::

---

## Monitoring in Practice

### Reading the Response Time Chart

A healthy WordPress site shows a relatively flat response-time line with minor fluctuations. Here's what common patterns mean:

| Chart pattern | Likely cause | What to do |
|---------------|-------------|------------|
| Flat, low line (~200–400 ms) | Healthy, well-cached site | Nothing — this is the goal |
| Gradual upward trend over days | Growing resource consumption (memory leak, bloating DB) | Run `wp db optimize`, check for runaway cron jobs, review plugin memory usage |
| Sudden spike then quick recovery | Traffic burst, heavy cron job, or backup running | Check server access logs for the spike period; stagger cron jobs |
| Regular spikes at the same time daily | Scheduled task: WordPress cron, server backup, log rotation | Move heavy cron jobs to off-peak hours; use `DISABLE_WP_CRON` and a real system cron |
| Consistently high baseline (>1 s) | No page cache, slow database, over-loaded server | Add page caching, optimize MySQL, upgrade hosting |
| Flatline at zero | Site is down — no response to measure | Investigate immediately; check server status and error logs |

### Uptime Percentage Reference

Uptime percentages sound abstract until you convert them to actual downtime:

| Uptime % | Downtime per month | Downtime per year | Assessment |
|----------|-------------------|-------------------|------------|
| **99.99%** | ~4 minutes | ~52 minutes | Enterprise-grade |
| **99.9%** | ~43 minutes | ~8.7 hours | Excellent for most sites |
| **99.5%** | ~3.6 hours | ~1.8 days | Acceptable for low-traffic sites |
| **99.0%** | ~7.3 hours | ~3.6 days | Needs investigation |
| **95.0%** | ~1.5 days | ~18 days | Serious reliability problems |

::: info What to aim for
Most well-managed WordPress sites on decent hosting should achieve **99.5% or better** without much effort. If you're running WooCommerce, membership sites, or any site where downtime costs revenue, target **99.9%+** and investigate every outage.
:::

### Responding to Common Alerts

#### "Site is DOWN" but you can load it in your browser

1. **IP restrictions or geo-blocking** — your server firewall may be blocking WPGrip's monitoring IP addresses. Whitelist them in your firewall or security plugin.
2. **WordPress maintenance mode** — core, plugin, or theme updates put WordPress into maintenance mode, returning a `503` status that WPGrip counts as down. Brief maintenance windows (under 60 seconds) are usually not caught; longer ones will trigger an alert.
3. **CDN caching** — you see a cached page from Cloudflare or Fastly, but WPGrip's check hit the origin server, which is down.
4. **Rate limiting** — some security tools (Wordfence, Sucuri, server-level WAF) rate-limit requests and may block WPGrip's frequent checks.

#### Response times seem higher than expected

WPGrip measures from its own monitoring infrastructure, not from your local machine. The Total time includes **real network latency** between the monitoring server and yours. A site hosted in Frankfurt will naturally show higher TTFB when checked from a US-based monitoring node than it would from a European browser.

#### SSL shows "Invalid" but HTTPS works fine in your browser

Common causes:

- **Incomplete certificate chain** — the server is missing an intermediate certificate. Browsers often cache intermediates, hiding the problem; WPGrip does not.
- **Hostname mismatch** — the cert covers `www.example.com` but WPGrip checks `example.com` (or vice versa).
- **CDN-provided certificate** — Cloudflare or another CDN provides its own edge certificate. The origin certificate (between CDN and your server) may be expired, but browsers never see it.

Test your certificate chain independently:

```bash
openssl s_client -connect example.com:443 -servername example.com </dev/null 2>/dev/null | openssl x509 -noout -dates -subject -issuer
```

Or use the free [SSL Labs Server Test](https://www.ssllabs.com/ssltest/) for a comprehensive report.

#### Domain expiry shows "Unknown"

WHOIS privacy and non-standard TLD registries can prevent WPGrip from reading the expiry date. For these domains, set a calendar reminder to check your registrar's dashboard quarterly.

---

## Next Steps

- [Adding a Site](/guide/adding-a-site) — monitoring starts here
- [Creating Database Backups](/guide/creating-backups) — protect your data alongside monitoring
- [Dashboard Overview](/guide/dashboard-overview) — understand the full interface
