/** @type {import('next').NextConfig} */
const nextConfig = {
    reactStrictMode: true,
    images: {
        domains: ['localhost', 'localhost:8081', 'secure.gravatar.com'],
    },
}

module.exports = nextConfig
