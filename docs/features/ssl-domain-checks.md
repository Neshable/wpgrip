# SSL & Domain Checks

WPGrip continuously monitors your SSL certificates and domain registrations.

## SSL Certificate Monitoring

Every minute, alongside uptime checks, WPGrip verifies:

- **Certificate validity** — is the cert valid and trusted?
- **Expiry date** — when does the cert expire?
- **Certificate chain** — are all intermediate certs present?

### Expiry Alerts

You’ll receive alerts when your SSL certificate is:
- **30 days** from expiry — informational
- **14 days** from expiry — warning
- **7 days** from expiry — critical
- **Expired** — immediate alert

## Domain Expiry Monitoring

WPGrip checks domain registration expiry dates weekly via WHOIS lookups.

::: warning
Domain expiry checks depend on public WHOIS data. Some registrars or privacy-protected domains may not expose expiry dates.
:::

## Viewing Status

SSL and domain status are visible on each site’s **Monitoring** page, shown as stat cards with:
- Current status (valid / expiring / expired)
- Days until expiry
- Certificate issuer (for SSL)
- Registrar (for domains)
