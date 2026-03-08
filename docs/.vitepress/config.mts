import { defineConfig } from 'vitepress'

export default defineConfig({
  title: 'WPGrip Docs',
  description: 'Documentation for WPGrip — manage all your WordPress sites from one control panel.',
  lang: 'en-US',
  cleanUrls: true,

  head: [
    ['link', { rel: 'icon', href: '/images/logo-dark.svg', type: 'image/svg+xml' }],
    ['link', { rel: 'preconnect', href: 'https://fonts.bunny.net' }],
    ['link', { rel: 'stylesheet', href: 'https://fonts.bunny.net/css?family=poppins:300,400,500,600,700&display=swap' }],
    ['meta', { property: 'og:type', content: 'website' }],
    ['meta', { property: 'og:site_name', content: 'WPGrip Docs' }],
  ],

  themeConfig: {
    logo: {
      light: '/images/logo-dark.svg',
      dark: '/images/logo-light.svg',
    },
    siteTitle: false,

    nav: [
      { text: 'Guide', link: '/guide/getting-started' },
      { text: 'Features', link: '/features/site-management' },
      { text: 'Billing', link: '/billing/plans-and-pricing' },
      {
        text: 'WPGrip',
        items: [
          { text: 'Homepage', link: 'https://wpgrip.com' },
          { text: 'Dashboard', link: 'https://wpgrip.com/login' },
          { text: 'Pricing', link: 'https://wpgrip.com/pricing' },
        ],
      },
    ],

    sidebar: {
      '/guide/': [
        {
          text: 'Getting Started',
          items: [
            { text: 'Introduction', link: '/guide/getting-started' },
            { text: 'SSH Connection', link: '/guide/ssh-connection' },
            { text: 'Dashboard Overview', link: '/guide/dashboard-overview' },
          ],
        },
        {
          text: 'Sites',
          items: [
            { text: 'Adding a Site', link: '/guide/adding-a-site' },
            { text: 'Monitoring Setup', link: '/guide/monitoring-setup' },
            { text: 'Creating Backups', link: '/guide/creating-backups' },
          ],
        },
        {
          text: 'Git Deployments',
          items: [
            { text: 'Adding a Repository', link: '/guide/adding-a-repository' },
            { text: 'Connecting Repo to Site', link: '/guide/connecting-repository-to-site' },
            { text: 'Deploying with Git', link: '/guide/deploying-with-git' },
          ],
        },
        {
          text: 'Account',
          items: [
            { text: 'Team Management', link: '/guide/team-management' },
            { text: 'Workspaces', link: '/guide/workspaces' },
          ],
        },
      ],
      '/features/': [
        {
          text: 'Site Management',
          items: [
            { text: 'Managing Sites', link: '/features/site-management' },
            { text: 'Plugins & Themes', link: '/features/plugins-and-themes' },
            { text: 'WP-CLI Access', link: '/features/wp-cli' },
          ],
        },
        {
          text: 'Monitoring',
          items: [
            { text: 'Uptime Monitoring', link: '/features/uptime-monitoring' },
            { text: 'SSL & Domain Checks', link: '/features/ssl-domain-checks' },
            { text: 'Performance Testing', link: '/features/performance-testing' },
          ],
        },
        {
          text: 'DevOps',
          items: [
            { text: 'Git Deployments', link: '/features/git-deployments' },
            { text: 'Database Backups', link: '/features/database-backups' },
          ],
        },
        {
          text: 'Security',
          items: [
            { text: 'Vulnerability Scanning', link: '/features/vulnerability-scanning' },
          ],
        },
      ],
      '/billing/': [
        {
          text: 'Billing',
          items: [
            { text: 'Plans & Pricing', link: '/billing/plans-and-pricing' },
            { text: 'Managing Subscriptions', link: '/billing/managing-subscriptions' },
          ],
        },
      ],
    },

    socialLinks: [
      { icon: 'github', link: 'https://github.com/wpgrip' },
    ],

    footer: {
      message: 'All your WordPress sites. One powerful control panel.',
      copyright: `© ${new Date().getFullYear()} WPGrip. All rights reserved.`,
    },

    search: {
      provider: 'local',
    },

    editLink: {
      pattern: 'https://github.com/wpgrip/docs/edit/main/:path',
      text: 'Suggest edits to this page',
    },
  },
})
