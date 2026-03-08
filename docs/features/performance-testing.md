# Performance Testing

WPGrip runs automated Google Lighthouse tests to track your sites’ performance over time.

## How It Works

Every day at 1:00 AM UTC, WPGrip runs Lighthouse audits against all production sites for both **mobile** and **desktop** viewports.

## Metrics Tracked

| Metric | Description |
|--------|-------------|
| **Performance** | Overall performance score (0–100) |
| **First Contentful Paint** | When the first content appears |
| **Largest Contentful Paint** | When the largest content element loads |
| **Total Blocking Time** | Sum of long-task blocking periods |
| **Cumulative Layout Shift** | Visual stability of the page |
| **Speed Index** | How quickly content is visually displayed |

## Viewing Results

Go to any site’s **Performance** page to see:

- **Score trend chart** — performance score over time (mobile & desktop)
- **Latest audit details** — all Core Web Vitals with pass/fail indicators
- **Historical comparison** — see how scores have changed

## Score Ranges

| Score | Rating | Color |
|-------|--------|-------|
| 90–100 | Good | 🟢 Green |
| 50–89 | Needs Improvement | 🟠 Orange |
| 0–49 | Poor | 🔴 Red |

## Data Retention

Performance data is retained for **30 days**.

::: tip
Staging sites are excluded from automatic daily tests. You can still trigger manual tests from the site’s performance page.
:::
